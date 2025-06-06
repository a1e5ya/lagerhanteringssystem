<?php
/**
 * Backup Handler for Karis Inventory System
 * 
 * Handles database backup operations including create, restore, download, and listing
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    require_once '../init.php';

    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    // Generate CSRF token if not exists
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $action = sanitizeString($_GET['action'] ?? $_POST['action'] ?? '', 20);
    
    // Verify CSRF token for state-changing operations
    if (in_array($action, ['create', 'restore']) && !verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid CSRF token');
    }

    switch ($action) {
        case 'list':
            handleListBackups();
            break;
        case 'create':
            handleCreateBackup();
            break;
        case 'restore':
            handleRestoreBackup();
            break;
        case 'download':
            handleDownloadBackup();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Okänd åtgärd: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8')]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ett fel inträffade: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]);
}
exit;

/**
 * Sanitize string input with length limit
 * 
 * @param mixed $input Input to sanitize
 * @param int $maxLength Maximum allowed length
 * @return string Sanitized string
 */
function sanitizeString($input, $maxLength = 255) {
    if (!is_string($input)) return '';
    // Remove null bytes and control characters
    $input = str_replace(chr(0), '', $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    return substr(trim($input), 0, $maxLength);
}

/**
 * Validate filename for security
 * 
 * @param string $filename Filename to validate
 * @return bool True if valid, false otherwise
 */
function validateFilename($filename) {
    // Check for directory traversal attempts
    if (strpos($filename, '..') !== false) {
        return false;
    }
    
    // Check for null bytes
    if (strpos($filename, "\0") !== false) {
        return false;
    }
    
    // Check for dangerous characters
    if (preg_match('/[<>:"\/\\\\|?*\x00-\x1f]/', $filename)) {
        return false;
    }
    
    // Check if filename matches expected pattern (more flexible for old backups)
    if (!preg_match('/^karis_inventory_backup_.+\.(sql|gz)$/', $filename)) {
        return false;
    }
    
    return true;
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Handle listing of backup files
 * 
 * @throws Exception If backup directory access fails
 */
function handleListBackups() {
    $backupDir = __DIR__ . '/../backups/';
    $backups = [];

    if (file_exists($backupDir)) {
        $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);

        foreach ($files as $file) {
            $filename = basename($file);
            
            // Validate filename for security
            if (!validateFilename($filename)) {
                continue;
            }

            // Extract timestamp from filename
            $baseFilename = str_replace('.gz', '', $filename);
            if (preg_match('/karis_inventory_backup_(\d{4}-\d{2}-\d{2})_(\d{6})/', $baseFilename, $matches)) {
                $date = $matches[1];
                $timeString = $matches[2];
                $time = substr($timeString, 0, 2) . ':' . substr($timeString, 2, 2) . ':' . substr($timeString, 4, 2);
            } else {
                $fileTime = filemtime($file);
                $date = date('Y-m-d', $fileTime);
                $time = date('H:i:s', $fileTime);
            }

            // Get file size
            $bytes = filesize($file);
            $units = ['B', 'KB', 'MB', 'GB'];
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $size = round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];

            // Get product count
            $productCount = getProductCountFromBackup($file);

            $backups[] = [
                'filename' => $filename,
                'date' => $date,
                'time' => $time,
                'size' => $size,
                'product_count' => $productCount,
                'hidden' => false
            ];
        }

        usort($backups, function($a, $b) {
            return strcmp($b['date'] . ' ' . $b['time'], $a['date'] . ' ' . $a['time']);
        });
    }

    echo json_encode(['success' => true, 'backups' => $backups]);
}

/**
 * Handle backup creation
 * 
 * @throws Exception If backup creation fails
 */
function handleCreateBackup() {
    global $pdo;
    
    $backupDir = __DIR__ . '/../backups/';
    if (!file_exists($backupDir)) {
        if (!mkdir($backupDir, 0755, true)) {
            throw new Exception('Kunde inte skapa backup-katalog');
        }
    }

    $timestamp = date('Y-m-d_His');
    $filename = "karis_inventory_backup_{$timestamp}.sql";
    $filepath = $backupDir . $filename;

    // Get database name safely
    $stmt = $pdo->prepare('SELECT DATABASE()');
    $stmt->execute();
    $dbname = $stmt->fetchColumn();

    $sqlContent = "-- Database Backup for " . htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8') . "\n";
    $sqlContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Get all tables
    $stmt = $pdo->prepare("SHOW TABLES");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        // Validate table name (whitelist approach)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            continue; // Skip invalid table names
        }
        
        $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        // Get table structure
        $stmt = $pdo->prepare("SHOW CREATE TABLE `{$table}`");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $sqlContent .= $row[1] . ";\n\n";

        // Get table data
        $stmt = $pdo->prepare("SELECT * FROM `{$table}`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';
            $batches = array_chunk($rows, 100);

            foreach ($batches as $batch) {
                $sqlContent .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                $values = [];

                foreach ($batch as $row) {
                    $escapedValues = [];
                    foreach ($row as $value) {
                        $escapedValues[] = $value === null ? 'NULL' : $pdo->quote($value);
                    }
                    $values[] = '(' . implode(', ', $escapedValues) . ')';
                }
                $sqlContent .= implode(",\n", $values) . ";\n\n";
            }
        }
    }

    $sqlContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    if (file_put_contents($filepath, $sqlContent) === false) {
        throw new Exception('Kunde inte skriva backup-fil');
    }

    // Try to compress the backup
    if (function_exists('gzencode')) {
        $compressedData = gzencode(file_get_contents($filepath));
        $compressedFilename = $filename . '.gz';
        $compressedFilepath = $backupDir . $compressedFilename;

        if (file_put_contents($compressedFilepath, $compressedData) !== false) {
            unlink($filepath);
            $filename = $compressedFilename;
        }
    }

    deleteOldBackups($backupDir);

    $downloadUrl = url('admin/backup_handler.php', ['action' => 'download', 'filename' => $filename]);

    echo json_encode([
        'success' => true,
        'message' => 'Backup skapad framgångsrikt!',
        'filename' => $filename,
        'download_url' => $downloadUrl
    ]);
}

/**
 * Handle backup restoration
 * 
 * @throws Exception If restoration fails
 */
function handleRestoreBackup() {
    $filename = sanitizeString($_POST['filename'] ?? '', 255);
    
    if (empty($filename)) {
        echo json_encode(['success' => false, 'message' => 'Filnamn krävs för återställning.']);
        return;
    }
    
    if (!validateFilename($filename)) {
        echo json_encode(['success' => false, 'message' => 'Ogiltigt filnamn.']);
        return;
    }
    
    $result = restoreDatabase($filename);
    echo json_encode($result);
}

/**
 * Handle backup download
 * 
 * @throws Exception If download fails
 */
function handleDownloadBackup() {
    $filename = sanitizeString($_GET['filename'] ?? '', 255);
    $backupDir = __DIR__ . '/../backups/';
    $filepath = $backupDir . $filename;

    if (empty($filename) || !validateFilename($filename) || !file_exists($filepath)) {
        http_response_code(404);
        die('Backup-fil hittades inte.');
    }

    if (ob_get_level()) ob_end_clean();

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    readfile($filepath);
    exit;
}

/**
 * Restore database from backup file
 * 
 * @param string $filename Backup filename to restore
 * @return array Result array with success status and message
 */
function restoreDatabase($filename) {
    global $pdo;

    try {
        $backupDir = __DIR__ . '/../backups/';
        $filepath = $backupDir . $filename;
        
        if (!validateFilename($filename) || !file_exists($filepath)) {
            return ['success' => false, 'message' => 'Backup-fil hittades inte.'];
        }

        // Create automatic backup before restore
        $timestamp = date('Y-m-d_His');
        $preRestoreFilename = "karis_inventory_backup_prerestore_{$timestamp}.sql";
        $preRestorePath = $backupDir . $preRestoreFilename;

        // Get database name safely
        $stmt = $pdo->prepare('SELECT DATABASE()');
        $stmt->execute();
        $dbname = $stmt->fetchColumn();
        
        $preRestoreContent = "-- Pre-Restore Backup for " . htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8') . "\n";
        $preRestoreContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $preRestoreContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // Get all tables for pre-restore backup
        $stmt = $pdo->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Validate table name
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
                continue;
            }
            
            $preRestoreContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $stmt = $pdo->prepare("SHOW CREATE TABLE `{$table}`");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $preRestoreContent .= $row[1] . ";\n\n";

            $stmt = $pdo->prepare("SELECT * FROM `{$table}`");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                $batches = array_chunk($rows, 100);

                foreach ($batches as $batch) {
                    $preRestoreContent .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                    $values = [];

                    foreach ($batch as $row) {
                        $escapedValues = [];
                        foreach ($row as $value) {
                            $escapedValues[] = $value === null ? 'NULL' : $pdo->quote($value);
                        }
                        $values[] = '(' . implode(', ', $escapedValues) . ')';
                    }
                    $preRestoreContent .= implode(",\n", $values) . ";\n\n";
                }
            }
        }

        $preRestoreContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        // Save pre-restore backup
        $preRestoreSuccess = false;
        if (file_put_contents($preRestorePath, $preRestoreContent) !== false) {
            $preRestoreSuccess = true;
            
            // Try to compress the pre-restore backup
            if (function_exists('gzencode')) {
                $compressedData = gzencode($preRestoreContent);
                $compressedFilename = $preRestoreFilename . '.gz';
                $compressedFilepath = $backupDir . $compressedFilename;
                
                if (file_put_contents($compressedFilepath, $compressedData) !== false) {
                    unlink($preRestorePath);
                    $preRestoreFilename = $compressedFilename;
                }
            }
        }

        // Now restore the selected backup
        $sqlContent = pathinfo($filename, PATHINFO_EXTENSION) === 'gz' 
            ? gzdecode(file_get_contents($filepath))
            : file_get_contents($filepath);

        if (empty($sqlContent)) {
            return ['success' => false, 'message' => 'Backup-fil är tom eller skadad.'];
        }

        // Begin transaction for restore
        $pdo->beginTransaction();

        try {
            // Disable foreign key checks and autocommit
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('SET AUTOCOMMIT = 0');

            // Split SQL content into individual statements
            $statements = [];
            $currentStatement = '';
            $lines = explode("\n", $sqlContent);
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip empty lines and comments
                if (empty($line) || strpos($line, '--') === 0) {
                    continue;
                }
                
                $currentStatement .= $line . ' ';
                
                // If line ends with semicolon, it's the end of a statement
                if (substr($line, -1) === ';') {
                    $statements[] = trim($currentStatement);
                    $currentStatement = '';
                }
            }

            $executedCount = 0;
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                    $executedCount++;
                }
            }

            // Re-enable foreign key checks and autocommit
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $pdo->exec('SET AUTOCOMMIT = 1');
            
            // Commit the transaction
            $pdo->commit();

            $message = "Databas återställd från: " . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . ". Utförde {$executedCount} kommandon.";
            if ($preRestoreSuccess) {
                $message .= " Säkerhetsbackup skapad: " . htmlspecialchars($preRestoreFilename, ENT_QUOTES, 'UTF-8') . ".";
            }

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }

    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Ett fel inträffade vid återställning: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')];
    }
}

/**
 * Delete old backup files to maintain storage limits
 * 
 * @param string $backupDir Directory containing backup files
 */
function deleteOldBackups($backupDir) {
    $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);
    
    // Filter out invalid filenames
    $validFiles = [];
    foreach ($files as $file) {
        if (validateFilename(basename($file))) {
            $validFiles[] = $file;
        }
    }
    
    usort($validFiles, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $maxFiles = 30;
    if (count($validFiles) > $maxFiles) {
        foreach (array_slice($validFiles, $maxFiles) as $file) {
            unlink($file);
        }
    }
}

/**
 * Get product count from backup file
 * 
 * @param string $filepath Path to backup file
 * @return string|int Product count or 'Okänt' if unknown
 */
function getProductCountFromBackup($filepath) {
    try {
        if (!validateFilename(basename($filepath))) {
            return 'Okänt';
        }
        
        $content = pathinfo($filepath, PATHINFO_EXTENSION) === 'gz'
            ? gzdecode(file_get_contents($filepath))
            : file_get_contents($filepath);

        if ($content === false) return 'Okänt';

        $productCount = 0;
        if (preg_match_all('/INSERT INTO `?product`?\s*\([^)]+\)\s*VALUES\s*(.+?);/is', $content, $matches)) {
            foreach ($matches[1] as $valuesSection) {
                $rows = preg_split('/\),\s*\(/s', trim($valuesSection));
                $productCount += count($rows);
            }
        }

        return $productCount > 0 ? $productCount : 'Okänt';
    } catch (Exception $e) {
        return 'Okänt';
    }
}
?>