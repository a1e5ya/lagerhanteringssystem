<?php
/**
 * Backup Handler - FIXED VERSION
 * 
 * Handles database backup creation, listing, and restoration
 * RESTORED original working functionality + added auto-download
 */

require_once '../init.php';

// Check if user is authenticated with Admin permissions ONLY
checkAuth(1); // Only Admin (role 1) can access this page

// Set JSON response content type for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
}

// Default response array
$response = [
    'success' => false,
    'message' => 'Ingen åtgärd utfördes'
];

// Get action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            $result = createDatabaseBackup();
            
            if ($result['success']) {
                // If backup was successful and we have a download URL, include it in response
                $response = [
                    'success' => true,
                    'message' => $result['message'],
                    'download_url' => $result['download_url'] ?? null,
                    'filename' => $result['filename'] ?? null
                ];
            } else {
                $response['message'] = $result['message'];
            }
            break;
            
        case 'list':
            $backups = listBackups();
            $response = [
                'success' => true,
                'backups' => $backups
            ];
            break;
            
        case 'restore':
            $filename = $_POST['filename'] ?? '';
            if (empty($filename)) {
                $response['message'] = 'Filnamn krävs för återställning.';
                break;
            }
            
            $result = restoreDatabase($filename);
            $response = $result;
            break;
            
        case 'hide':
            $filename = $_POST['filename'] ?? '';
            if (empty($filename)) {
                $response['message'] = 'Filnamn krävs.';
                break;
            }
            
            $result = hideBackup($filename);
            $response = $result;
            break;
            
        case 'download':
            $filename = $_GET['filename'] ?? '';
            if (empty($filename)) {
                die('Filnamn krävs för nedladdning.');
            }
            
            downloadBackup($filename);
            exit; // downloadBackup() handles the response
            
        default:
            $response['message'] = 'Okänd åtgärd: ' . $action;
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Ett fel inträffade: ' . $e->getMessage();
    error_log('Backup handler error: ' . $e->getMessage());
}

// Return JSON response for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode($response);
    exit;
}

/**
 * Create database backup using PHP PDO (RESTORED ORIGINAL FUNCTIONALITY)
 */
function createDatabaseBackup() {
    global $pdo;
    
    try {
        // Create backups directory if it doesn't exist
        $backupDir = __DIR__ . '/../backups/';
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Generate filename
        $timestamp = date('Y-m-d_His');
        $filename = "karis_inventory_backup_{$timestamp}.sql";
        $filepath = $backupDir . $filename;
        
        // Get database name
        $dbname = $pdo->query('SELECT DATABASE()')->fetchColumn();
        
        // Start building the SQL backup content
        $sqlContent = "-- Database Backup for {$dbname}\n";
        $sqlContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- \n\n";
        
        // Disable foreign key checks
        $sqlContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        // Get all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // For each table, get structure and data
        foreach ($tables as $table) {
            $sqlContent .= "-- \n";
            $sqlContent .= "-- Table structure for table `{$table}`\n";
            $sqlContent .= "-- \n\n";
            
            // Drop table if exists
            $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Get table creation statement
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $sqlContent .= $row[1] . ";\n\n";
            
            // Get table data
            $sqlContent .= "-- \n";
            $sqlContent .= "-- Dumping data for table `{$table}`\n";
            $sqlContent .= "-- \n\n";
            
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                // Get column names
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                // Process rows in batches
                $batchSize = 100;
                $batches = array_chunk($rows, $batchSize);
                
                foreach ($batches as $batch) {
                    $sqlContent .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                    $values = [];
                    
                    foreach ($batch as $row) {
                        $escapedValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $escapedValues[] = 'NULL';
                            } else {
                                $escapedValues[] = $pdo->quote($value);
                            }
                        }
                        $values[] = '(' . implode(', ', $escapedValues) . ')';
                    }
                    
                    $sqlContent .= implode(",\n", $values) . ";\n\n";
                }
            }
        }
        
        // Re-enable foreign key checks
        $sqlContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        // Write backup to file
        $bytesWritten = file_put_contents($filepath, $sqlContent);
        
        if ($bytesWritten === false) {
            return [
                'success' => false,
                'message' => 'Kunde inte skriva backup-fil.'
            ];
        }
        
        // Compress the backup if possible
        if (function_exists('gzencode')) {
            $compressedData = gzencode(file_get_contents($filepath));
            $compressedFilename = $filename . '.gz';
            $compressedFilepath = $backupDir . $compressedFilename;
            
            if (file_put_contents($compressedFilepath, $compressedData) !== false) {
                // Remove uncompressed file
                unlink($filepath);
                $filename = $compressedFilename;
                $filepath = $compressedFilepath;
            }
        }
        
        // Log the backup creation
        logBackupEvent($filename);
        
        // Clean up old backups (keep only last 30)
        cleanupOldBackups();
        
        // Create download URL
        $downloadUrl = url('admin/backup_handler.php', ['action' => 'download', 'filename' => $filename]);
        
        return [
            'success' => true,
            'message' => 'Backup skapad framgångsrikt! Nedladdning startar automatiskt.',
            'filename' => $filename,
            'download_url' => $downloadUrl
        ];
        
    } catch (Exception $e) {
        error_log('Backup creation error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Ett fel inträffade vid skapande av backup: ' . $e->getMessage()
        ];
    }
}

/**
 * Download backup file to user's computer
 */
function downloadBackup($filename) {
    $backupDir = __DIR__ . '/../backups/';
    $filepath = $backupDir . $filename;
    
    // Security check - ensure filename doesn't contain path traversal
    if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
        http_response_code(400);
        die('Ogiltigt filnamn.');
    }
    
    // Check if file exists
    if (!file_exists($filepath)) {
        http_response_code(404);
        die('Backup-fil hittades inte.');
    }
    
    // Get file size
    $filesize = filesize($filepath);
    
    // Set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $filesize);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Clear any previous output
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Read and output the file
    readfile($filepath);
    exit;
}

/**
 * List available backups
 */
function listBackups() {
    global $pdo;
    
    $backupDir = __DIR__ . '/../backups/';
    $backups = [];
    
    if (!file_exists($backupDir)) {
        return $backups;
    }
    
    // Get all backup files
    $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Check if backup is hidden (skip check if backup_metadata table doesn't exist)
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM backup_metadata WHERE filename = ? AND hidden = 1");
            $stmt->execute([$filename]);
            $isHidden = $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // If table doesn't exist, backup is not hidden
            $isHidden = false;
        }
        
        // Skip hidden backups
        if ($isHidden) {
            continue;
        }
        
        // Extract timestamp from filename - improved regex to handle .gz extension
        $baseFilename = str_replace('.gz', '', $filename);
        if (preg_match('/karis_inventory_backup_(\d{4}-\d{2}-\d{2})_(\d{6})/', $baseFilename, $matches)) {
            $date = $matches[1];
            $timeString = $matches[2];
            $time = substr($timeString, 0, 2) . ':' . substr($timeString, 2, 2) . ':' . substr($timeString, 4, 2);
        } else {
            // Fallback - try to get from file modification time
            $fileTime = filemtime($file);
            $date = date('Y-m-d', $fileTime);
            $time = date('H:i:s', $fileTime);
        }
        
        // Get file size
        $size = formatFileSize(filesize($file));
        
        // Get product count from backup metadata or estimate
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
    
    // Sort by date and time - newest first
    usort($backups, function($a, $b) {
        // Combine date and time for sorting
        $dateTimeA = $a['date'] . ' ' . $a['time'];
        $dateTimeB = $b['date'] . ' ' . $b['time'];
        
        // Compare timestamps - newer first (descending)
        return strcmp($dateTimeB, $dateTimeA);
    });
    
    return $backups;
}

/**
 * Restore database from backup (FIXED - actually restores the database)
 */
function restoreDatabase($filename) {
    global $pdo;
    
    try {
        $backupDir = __DIR__ . '/../backups/';
        $filepath = $backupDir . $filename;
        
        // Security check
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return [
                'success' => false,
                'message' => 'Ogiltigt filnamn.'
            ];
        }
        
        // Check if backup file exists
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'message' => 'Backup-fil hittades inte.'
            ];
        }
        
        // Create automatic backup before restore
        $preRestoreBackup = createDatabaseBackup();
        if (!$preRestoreBackup['success']) {
            return [
                'success' => false,
                'message' => 'Kunde inte skapa säkerhetsbackup innan återställning: ' . $preRestoreBackup['message']
            ];
        }
        
        // Prepare file content
        $sqlContent = '';
        
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'gz') {
            // Decompress gzipped file
            $sqlContent = gzdecode(file_get_contents($filepath));
        } else {
            $sqlContent = file_get_contents($filepath);
        }
        
        if ($sqlContent === false || empty($sqlContent)) {
            return [
                'success' => false,
                'message' => 'Kunde inte läsa backup-fil eller filen är tom.'
            ];
        }
        
        // Begin transaction for safety
        $pdo->beginTransaction();
        
        try {
            // Disable foreign key checks and autocommit
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('SET AUTOCOMMIT = 0');
            
            // Split SQL content into individual statements
            // Remove comments and empty lines first
            $lines = explode("\n", $sqlContent);
            $sqlStatements = [];
            $currentStatement = '';
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip empty lines and comments
                if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                    continue;
                }
                
                // Add line to current statement
                $currentStatement .= $line . ' ';
                
                // If line ends with semicolon, we have a complete statement
                if (substr($line, -1) === ';') {
                    $sqlStatements[] = trim($currentStatement);
                    $currentStatement = '';
                }
            }
            
            // Execute each statement
            $executedCount = 0;
            $errorCount = 0;
            
            foreach ($sqlStatements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $result = $pdo->exec($statement);
                        $executedCount++;
                        
                        // Log successful table operations
                        if (stripos($statement, 'DROP TABLE') === 0) {
                            error_log("Restore: Dropped table");
                        } elseif (stripos($statement, 'CREATE TABLE') === 0) {
                            error_log("Restore: Created table");
                        } elseif (stripos($statement, 'INSERT INTO') === 0) {
                            error_log("Restore: Inserted data ($result rows affected)");
                        }
                        
                    } catch (PDOException $e) {
                        $errorCount++;
                        error_log("Error executing statement during restore: " . $e->getMessage());
                        error_log("Statement: " . substr($statement, 0, 200) . "...");
                        
                        // Don't fail on certain expected errors
                        if (strpos($e->getMessage(), 'already exists') === false && 
                            strpos($e->getMessage(), "doesn't exist") === false) {
                            // This is a serious error, rollback
                            throw $e;
                        }
                    }
                }
            }
            
            // Re-enable foreign key checks
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $pdo->exec('SET AUTOCOMMIT = 1');
            
            // Commit the transaction
            $pdo->commit();
            
            // Log the restore event
            logRestoreEvent($filename);
            
            return [
                'success' => true,
                'message' => "Databas återställd framgångsrikt från: {$filename}. Utförde {$executedCount} SQL-kommandon" . 
                           ($errorCount > 0 ? " ({$errorCount} varningar)" : "")
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        // Re-enable foreign key checks and autocommit in case of error
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $pdo->exec('SET AUTOCOMMIT = 1');
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
        } catch (Exception $cleanupError) {
            // Ignore cleanup errors
        }
        
        error_log('Database restore error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Ett fel inträffade vid återställning: ' . $e->getMessage()
        ];
    }
}

/**
 * Hide backup from list
 */
function hideBackup($filename) {
    global $pdo;
    
    try {
        // Security check
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return [
                'success' => false,
                'message' => 'Ogiltigt filnamn.'
            ];
        }
        
        // Create backup_metadata table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS backup_metadata (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            hidden BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Insert or update metadata
        $stmt = $pdo->prepare("INSERT INTO backup_metadata (filename, hidden) VALUES (?, 1) 
                              ON DUPLICATE KEY UPDATE hidden = 1, updated_at = CURRENT_TIMESTAMP");
        $stmt->execute([$filename]);
        
        return [
            'success' => true,
            'message' => 'Backup har dolts från listan.'
        ];
        
    } catch (Exception $e) {
        error_log('Hide backup error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Ett fel inträffade vid döljning av backup.'
        ];
    }
}

/**
 * Log backup creation event
 */
function logBackupEvent($filename) {
    global $pdo;
    
    try {
        $userId = $_SESSION['user_id'] ?? 1;
        $stmt = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description, created_at) 
                              VALUES (?, 'database_backup', ?, NOW())");
        $stmt->execute([$userId, "Database backup created: {$filename}"]);
    } catch (Exception $e) {
        error_log('Error logging backup event: ' . $e->getMessage());
    }
}

/**
 * Log restore event
 */
function logRestoreEvent($filename) {
    global $pdo;
    
    try {
        $userId = $_SESSION['user_id'] ?? 1;
        $stmt = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description, created_at) 
                              VALUES (?, 'database_restore', ?, NOW())");
        $stmt->execute([$userId, "Database restored from backup: {$filename}"]);
    } catch (Exception $e) {
        error_log('Error logging restore event: ' . $e->getMessage());
    }
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Clean up old backups - keep only the last 30 backups
 */
function cleanupOldBackups() {
    global $pdo;
    
    try {
        $backupDir = __DIR__ . '/../backups/';
        
        if (!file_exists($backupDir)) {
            return;
        }
        
        // Get all backup files
        $files = glob($backupDir . 'karis_inventory_backup_*.{sql,gz}', GLOB_BRACE);
        
        if (count($files) <= 30) {
            // We have 30 or fewer backups, no cleanup needed
            return;
        }
        
        // Create array with file info for sorting
        $backupFiles = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $filemtime = filemtime($file);
            
            $backupFiles[] = [
                'filepath' => $file,
                'filename' => $filename,
                'timestamp' => $filemtime
            ];
        }
        
        // Sort by timestamp - newest first
        usort($backupFiles, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Keep only the first 30 (newest), mark the rest for deletion
        $filesToDelete = array_slice($backupFiles, 30);
        
        $deletedCount = 0;
        $errors = [];
        
        foreach ($filesToDelete as $fileInfo) {
            try {
                // Delete the physical file
                if (unlink($fileInfo['filepath'])) {
                    $deletedCount++;
                    
                    // Also remove from backup_metadata if exists
                    try {
                        $stmt = $pdo->prepare("DELETE FROM backup_metadata WHERE filename = ?");
                        $stmt->execute([$fileInfo['filename']]);
                    } catch (PDOException $e) {
                        // Table might not exist, ignore
                    }
                    
                    // Log the deletion
                    error_log("Cleanup: Deleted old backup: " . $fileInfo['filename']);
                    
                } else {
                    $errors[] = "Could not delete: " . $fileInfo['filename'];
                }
                
            } catch (Exception $e) {
                $errors[] = "Error deleting " . $fileInfo['filename'] . ": " . $e->getMessage();
                error_log("Backup cleanup error: " . $e->getMessage());
            }
        }
        
        // Log cleanup summary
        if ($deletedCount > 0) {
            $userId = $_SESSION['user_id'] ?? 1;
            try {
                $stmt = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description, created_at) 
                                      VALUES (?, 'backup_cleanup', ?, NOW())");
                $stmt->execute([$userId, "Automatic cleanup: deleted {$deletedCount} old backup(s)"]);
            } catch (Exception $e) {
                error_log('Error logging cleanup event: ' . $e->getMessage());
            }
        }
        
        // Log any errors
        if (!empty($errors)) {
            error_log("Backup cleanup errors: " . implode(", ", $errors));
        }
        
    } catch (Exception $e) {
        error_log('Backup cleanup error: ' . $e->getMessage());
    }
}

/**
 * Get product count from backup (fixed to count actual products correctly)
 */
function getProductCountFromBackup($filepath) {
    try {
        $content = '';
        
        if (pathinfo($filepath, PATHINFO_EXTENSION) === 'gz') {
            $content = gzdecode(file_get_contents($filepath));
            if ($content === false) {
                return 'Okänt';
            }
        } else {
            $content = file_get_contents($filepath);
            if ($content === false) {
                return 'Okänt';
            }
        }
        
        $productCount = 0;
        
        // Look for INSERT INTO product statements - be more specific
        // Match: INSERT INTO `product` (columns) VALUES (data), (data), (data);
        if (preg_match_all('/INSERT INTO `?product`?\s*\([^)]+\)\s*VALUES\s*(.+?);/is', $content, $matches)) {
            foreach ($matches[1] as $valuesSection) {
                // Clean up the values section
                $valuesSection = trim($valuesSection);
                
                // Split by '), (' to count individual rows
                // Each row is like: (1, 'Title', 'Author', ...)
                $rows = preg_split('/\),\s*\(/s', $valuesSection);
                
                // Count actual rows
                $rowsInThisInsert = count($rows);
                $productCount += $rowsInThisInsert;
            }
        }
        
        // Alternative method: if the above doesn't work, try a different approach
        if ($productCount === 0) {
            // Count opening parentheses that start data rows for product table
            $lines = explode("\n", $content);
            $inProductInsert = false;
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Start of product INSERT statement
                if (preg_match('/^INSERT INTO `?product`?/i', $line)) {
                    $inProductInsert = true;
                    
                    // If VALUES is on the same line, start counting
                    if (strpos($line, 'VALUES') !== false) {
                        // Count rows in this line
                        preg_match_all('/\([^)]*\)/', $line, $rowMatches);
                        $productCount += count($rowMatches[0]);
                    }
                    continue;
                }
                
                // If we're in a product INSERT and find a new INSERT, stop
                if ($inProductInsert && preg_match('/^INSERT INTO `?(?!product)/i', $line)) {
                    $inProductInsert = false;
                    continue;
                }
                
                // If we're in product INSERT, count rows
                if ($inProductInsert && !empty($line) && !preg_match('/^--/', $line)) {
                    // Count opening parentheses that start new rows
                    preg_match_all('/\([^)]*\)/', $line, $rowMatches);
                    $productCount += count($rowMatches[0]);
                }
                
                // End of statement
                if ($inProductInsert && substr($line, -1) === ';') {
                    $inProductInsert = false;
                }
            }
        }
        
        return $productCount > 0 ? $productCount : 'Okänt';
        
    } catch (Exception $e) {
        error_log('Error counting products in backup: ' . $e->getMessage());
        return 'Okänt';
    }
}