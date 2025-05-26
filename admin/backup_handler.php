<?php
/**
 * Backup Handler - Final Fixed Version
 * 
 * Handles database backup operations with correct table names
 */

// Suppress deprecation warnings for clean JSON output
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once '../init.php';

// Check if user is authenticated with Admin permissions ONLY
try {
    checkAuth(1); // Only Admin (role 1) can access backup functionality
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Authentication failed: ' . $e->getMessage()
    ]);
    exit;
}

header('Content-Type: application/json');

// Get action
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Backup directory - use absolute path
$backupDir = $_SERVER['DOCUMENT_ROOT'] . '/prog23/lagerhanteringssystem/backups/';

// Create backup directory if it doesn't exist
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create backup directory: ' . $backupDir
        ]);
        exit;
    }
}

// Backup metadata file
$metadataFile = $backupDir . 'backup_metadata.json';

// Load metadata
function loadMetadata() {
    global $metadataFile;
    if (file_exists($metadataFile)) {
        $content = file_get_contents($metadataFile);
        return json_decode($content, true) ?: [];
    }
    return [];
}

// Save metadata
function saveMetadata($metadata) {
    global $metadataFile;
    $result = file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    return $result !== false;
}

// Get product count using correct table name
function getProductCount() {
    global $pdo;
    try {
        // Use 'product' (singular) not 'products' (plural)
        $stmt = $pdo->query("SELECT COUNT(*) FROM product");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Failed to get product count: " . $e->getMessage());
        return 0;
    }
}

// Format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

// Helper function to create PHP-based backup
function createPHPBackup() {
    global $pdo, $dbname;
    
    try {
        $backup = "-- Database backup created by PHP on " . date('Y-m-d H:i:s') . "\n";
        $backup .= "-- Database: $dbname\n\n";
        $backup .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $backup .= "START TRANSACTION;\n";
        $backup .= "SET time_zone = \"+00:00\";\n\n";
        
        // Get all tables
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        foreach ($tables as $table) {
            $backup .= "-- \n";
            $backup .= "-- Table structure for table `$table`\n";
            $backup .= "-- \n\n";
            
            $backup .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Get CREATE TABLE statement
            $result = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch(PDO::FETCH_NUM);
            $backup .= $row[1] . ";\n\n";
            
            // Get table data
            $backup .= "-- \n";
            $backup .= "-- Dumping data for table `$table`\n";
            $backup .= "-- \n\n";
            
            $result = $pdo->query("SELECT * FROM `$table`");
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $columns = array_keys($row);
                $backup .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (";
                
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                $backup .= implode(', ', $values);
                $backup .= ");\n";
            }
            $backup .= "\n";
        }
        
        $backup .= "COMMIT;\n";
        return $backup;
    } catch (Exception $e) {
        error_log("PHP Backup creation failed: " . $e->getMessage());
        return false;
    }
}

// Helper function to execute SQL from string
function executeSQLFromString($sqlData) {
    global $pdo;
    
    try {
        // Disable foreign key checks temporarily
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Split SQL into individual statements - handle multi-line statements better
        $statements = [];
        $currentStatement = '';
        $lines = explode("\n", $sqlData);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if (empty($line) || preg_match('/^(--|\/\*|\*)/', $line)) {
                continue;
            }
            
            $currentStatement .= $line . "\n";
            
            // If line ends with semicolon, it's end of statement
            if (substr($line, -1) === ';') {
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            }
        }
        
        // Add last statement if exists
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }
        
        $pdo->beginTransaction();
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    $successCount++;
                } catch (PDOException $e) {
                    $errorCount++;
                    
                    // If it's a critical error, rollback
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        $pdo->commit();
        
        return true;
        
    } catch (Exception $e) {
        $pdo->rollback();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // Re-enable even on error
        return false;
    }
}

// Helper function to execute SQL from file
function executeSQLFromFile($filepath) {
    $sqlData = file_get_contents($filepath);
    return $sqlData ? executeSQLFromString($sqlData) : false;
}

switch ($action) {
    case 'create':
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = 'karis_inventory_backup_' . $timestamp . '.sql';
            $filepath = $backupDir . $filename;
            
            // Use PHP-based backup (more reliable)
            $backupContent = createPHPBackup();
            if ($backupContent && file_put_contents($filepath, $backupContent)) {
                $fileSize = filesize($filepath);
                $productCount = getProductCount();
                
                // Try to compress the backup
                if (function_exists('gzencode')) {
                    $compressed = gzencode($backupContent, 9);
                    $compressedFilepath = $filepath . '.gz';
                    if ($compressed && file_put_contents($compressedFilepath, $compressed)) {
                        unlink($filepath); // Remove uncompressed file
                        $finalFilepath = $compressedFilepath;
                        $finalFilename = $filename . '.gz';
                        $fileSize = filesize($finalFilepath);
                    } else {
                        $finalFilepath = $filepath;
                        $finalFilename = $filename;
                    }
                } else {
                    $finalFilepath = $filepath;
                    $finalFilename = $filename;
                }
                
                // Save metadata
                $metadata = loadMetadata();
                $metadata[$finalFilename] = [
                    'filename' => $finalFilename,
                    'created' => date('Y-m-d H:i:s'),
                    'size' => $fileSize,
                    'product_count' => $productCount,
                    'hidden' => false
                ];
                
                if (saveMetadata($metadata)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Backup skapad framgångsrikt: ' . $finalFilename . ' (Produkter: ' . $productCount . ')',
                        'filename' => $finalFilename,
                        'product_count' => $productCount,
                        'file_path' => $finalFilepath
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Backup skapad men metadata kunde inte sparas'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Fel vid skapande av backup med PHP-metod'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Fel vid skapande av backup: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'list':
        try {
            $metadata = loadMetadata();
            $backups = [];
            
            // Get all backup files
            $files = array_merge(
                glob($backupDir . 'karis_inventory_backup_*.sql') ?: [],
                glob($backupDir . 'karis_inventory_backup_*.sql.gz') ?: []
            );
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                // Skip if not in metadata, add default metadata
                if (!isset($metadata[$filename])) {
                    $fileInfo = pathinfo($filename);
                    if (preg_match('/karis_inventory_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $fileInfo['filename'], $matches)) {
                        $createdTime = str_replace('_', ' ', str_replace('-', ':', $matches[1], 2));
                        $metadata[$filename] = [
                            'filename' => $filename,
                            'created' => $createdTime,
                            'size' => filesize($file),
                            'product_count' => getProductCount(), // Get current count as estimate
                            'hidden' => false
                        ];
                    }
                }
                
                if (isset($metadata[$filename])) {
                    $backupInfo = $metadata[$filename];
                    
                    // Skip hidden backups - don't show them in the list
                    if ($backupInfo['hidden'] === true) {
                        continue;
                    }
                    
                    $datetime = new DateTime($backupInfo['created']);
                    
                    $backups[] = [
                        'filename' => $filename,
                        'date' => $datetime->format('Y-m-d'),
                        'time' => $datetime->format('H:i:s'),
                        'size' => formatFileSize($backupInfo['size']),
                        'product_count' => $backupInfo['product_count'],
                        'hidden' => false // Only visible backups are returned
                    ];
                }
            }
            
            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                return strcmp($b['date'] . ' ' . $b['time'], $a['date'] . ' ' . $a['time']);
            });
            
            // Save updated metadata
            saveMetadata($metadata);
            
            echo json_encode([
                'success' => true,
                'backups' => $backups
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Fel vid laddning av backup-lista: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'restore':
        try {
            $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
            if (empty($filename)) {
                throw new Exception('Inget filnamn angivet');
            }
            
            $filepath = $backupDir . $filename;
            if (!file_exists($filepath)) {
                throw new Exception('Backup-filen hittades inte: ' . $filepath);
            }
            
            // Check file size to ensure it's not corrupted
            $fileSize = filesize($filepath);
            if ($fileSize < 100) {
                throw new Exception('Backup-filen verkar vara korrupt (för liten)');
            }
            
            // First create a backup of current database
            $currentBackupTimestamp = date('Y-m-d_H-i-s');
            $currentBackupFilename = 'karis_inventory_backup_before_restore_' . $currentBackupTimestamp . '.sql';
            $currentBackupFilepath = $backupDir . $currentBackupFilename;
            
            // Create backup of current state using PHP
            $currentBackupContent = createPHPBackup();
            if ($currentBackupContent && file_put_contents($currentBackupFilepath, $currentBackupContent)) {
                // Add current backup to metadata
                $metadata = loadMetadata();
                $metadata[$currentBackupFilename] = [
                    'filename' => $currentBackupFilename,
                    'created' => date('Y-m-d H:i:s'),
                    'size' => filesize($currentBackupFilepath),
                    'product_count' => getProductCount(),
                    'hidden' => false
                ];
                saveMetadata($metadata);
            }
            
            // Prepare restore
            if (pathinfo($filepath, PATHINFO_EXTENSION) === 'gz') {
                // Decompress and restore
                $compressedData = file_get_contents($filepath);
                if (!$compressedData) {
                    throw new Exception('Kunde inte läsa komprimerad backup-fil');
                }
                
                $sqlData = gzdecode($compressedData);
                if (!$sqlData) {
                    throw new Exception('Kunde inte dekomprimera backup-fil');
                }
                
                if (executeSQLFromString($sqlData)) {
                    $newProductCount = getProductCount();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Databasen återställd framgångsrikt från: ' . $filename . '. Produkter efter återställning: ' . $newProductCount . '.'
                    ]);
                } else {
                    throw new Exception('SQL-exekvering misslyckades vid återställning från komprimerad fil');
                }
            } else {
                // Restore directly from SQL file
                $sqlData = file_get_contents($filepath);
                if (!$sqlData) {
                    throw new Exception('Kunde inte läsa backup-fil');
                }
                
                if (executeSQLFromString($sqlData)) {
                    $newProductCount = getProductCount();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Databasen återställd framgångsrikt från: ' . $filename . '. Produkter efter återställning: ' . $newProductCount . '.'
                    ]);
                } else {
                    throw new Exception('SQL-exekvering misslyckades vid återställning från SQL-fil');
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Fel vid återställning: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'hide':
    case 'show':
        try {
            $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
            if (empty($filename)) {
                throw new Exception('Inget filnamn angivet');
            }
            
            $metadata = loadMetadata();
            if (!isset($metadata[$filename])) {
                throw new Exception('Backup-metadata hittades inte');
            }
            
            $metadata[$filename]['hidden'] = ($action === 'hide');
            saveMetadata($metadata);
            
            $message = ($action === 'hide') ? 'Backup dold' : 'Backup visad';
            
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Fel vid uppdatering av backup-status: ' . $e->getMessage()
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Ogiltig åtgärd'
        ]);
        break;
}
?>