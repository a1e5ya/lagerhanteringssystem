<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    require_once '../init.php';

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if ($action === 'list') {
        // Real backup listing with product count
        $backupDir = __DIR__ . '/../backups/';
        $backups = [];

        if (file_exists($backupDir)) {
            $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);

            foreach ($files as $file) {
                $filename = basename($file);

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

    } elseif ($action === 'create') {
        $backupDir = __DIR__ . '/../backups/';
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $filename = "karis_inventory_backup_{$timestamp}.sql";
        $filepath = $backupDir . $filename;

        $dbname = $pdo->query('SELECT DATABASE()')->fetchColumn();

        $sqlContent = "-- Database Backup for {$dbname}\n";
        $sqlContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $sqlContent .= $row[1] . ";\n\n";

            $stmt = $pdo->query("SELECT * FROM `{$table}`");
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

        if (file_put_contents($filepath, $sqlContent)) {
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
        } else {
            echo json_encode(['success' => false, 'message' => 'Kunde inte skriva backup-fil.']);
        }

    } elseif ($action === 'restore') {
        $filename = $_POST['filename'] ?? '';
        if (empty($filename)) {
            echo json_encode(['success' => false, 'message' => 'Filnamn krävs för återställning.']);
        } else {
            $result = restoreDatabase($filename);
            echo json_encode($result);
        }

    } elseif ($action === 'download') {
        $filename = $_GET['filename'] ?? '';
        $backupDir = __DIR__ . '/../backups/';
        $filepath = $backupDir . $filename;

        if (strpos($filename, '..') !== false || !file_exists($filepath)) {
            http_response_code(404);
            die('Backup-fil hittades inte.');
        }

        if (ob_get_level()) ob_end_clean();

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));

        readfile($filepath);
        exit;

    } else {
        echo json_encode(['success' => false, 'message' => 'Okänd åtgärd: ' . $action]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
exit;

function restoreDatabase($filename) {
    global $pdo;

    try {
        $backupDir = __DIR__ . '/../backups/';
        $filepath = $backupDir . $filename;
        
        if (strpos($filename, '..') !== false || !file_exists($filepath)) {
            return ['success' => false, 'message' => 'Backup-fil hittades inte.'];
        }

        // Create automatic backup before restore
        $timestamp = date('Y-m-d_His');
        $preRestoreFilename = "karis_inventory_backup_prerestore_{$timestamp}.sql";
        $preRestorePath = $backupDir . $preRestoreFilename;

        $dbname = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $preRestoreContent = "-- Pre-Restore Backup for {$dbname}\n";
        $preRestoreContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $preRestoreContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $preRestoreContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $preRestoreContent .= $row[1] . ";\n\n";

            $stmt = $pdo->query("SELECT * FROM `{$table}`");
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

            $message = "Databas återställd från: {$filename}. Utförde {$executedCount} kommandon.";
            if ($preRestoreSuccess) {
                $message .= " Säkerhetsbackup skapad: {$preRestoreFilename}.";
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
        return ['success' => false, 'message' => 'Ett fel inträffade vid återställning: ' . $e->getMessage()];
    }
}

function deleteOldBackups($backupDir) {
    $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $maxFiles = 30;
    if (count($files) > $maxFiles) {
        foreach (array_slice($files, $maxFiles) as $file) {
            unlink($file);
        }
    }
}

function getProductCountFromBackup($filepath) {
    try {
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