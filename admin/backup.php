<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/Database.php';

// Access control
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] > 2) {
    // Redirect non-admin users or show error
    header('Location: login.php?error=unauthorized');
    exit();
}

// Initialize database connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $database = new Database($pdo);
} catch (PDOException $e) {
    // Log error
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed.");
}

// Handle backup request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
    try {
        // Optional: Allow custom backup path
        $backupPath = isset($_POST['backup_path']) && !empty($_POST['backup_path']) 
            ? $_POST['backup_path'] 
            : null;
        
        // Perform backup
        $backupResult = $database->backupDatabase($backupPath);
        
        if ($backupResult['success']) {
            $successMessage = "Backup created successfully: " . $backupResult['filename'];
        } else {
            $errorMessage = "Backup creation failed: " . ($backupResult['message'] ?? 'Unknown error');
        }
    } catch (Exception $e) {
        $errorMessage = "Backup error: " . $e->getMessage();
        error_log($errorMessage);
    }
}

// List existing backups
$backupDir = BASE_PATH . '/backups/';
$existingBackups = [];
if (is_dir($backupDir)) {
    $existingBackups = array_diff(scandir($backupDir), ['.', '..']);
    // Sort backups by date (newest first)
    usort($existingBackups, function($a, $b) {
        return filemtime($backupDir . $b) - filemtime($backupDir . $a);
    });
}

// Handle backup download
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $filename = basename($_GET['download']);
    $filepath = $backupDir . $filename;
    
    if (file_exists($filepath)) {
        // Security: Verify the file is a backup file
        if (strpos($filename, 'karis_inventory_backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'gz') {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            $errorMessage = "Invalid backup file.";
        }
    } else {
        $errorMessage = "Backup file not found.";
    }
}

// Handle backup deletion
if (isset($_POST['delete_backup']) && isset($_POST['backup_filename'])) {
    $filename = basename($_POST['backup_filename']);
    $filepath = $backupDir . $filename;
    
    if (file_exists($filepath)) {
        // Additional security checks
        if (strpos($filename, 'karis_inventory_backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'gz') {
            if (unlink($filepath)) {
                $successMessage = "Backup deleted successfully.";
            } else {
                $errorMessage = "Could not delete backup file.";
            }
        } else {
            $errorMessage = "Invalid backup file.";
        }
    } else {
        $errorMessage = "Backup file not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup - Karis Antikvariat</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="admin-container">
        <h1>Database Backup Management</h1>
        
        <?php 
        // Display success or error messages
        if (isset($successMessage)) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($successMessage) . "</div>";
        }
        if (isset($errorMessage)) {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($errorMessage) . "</div>";
        }
        ?>
        
        <section class="backup-creation">
            <h2>Create New Backup</h2>
            <form method="post" class="form-backup">
                <div class="form-group">
                    <label for="backup_path">Backup Path (optional)</label>
                    <input type="text" id="backup_path" name="backup_path" 
                           placeholder="Leave blank for default location">
                </div>
                <button type="submit" name="create_backup" class="btn btn-primary">
                    Create Database Backup
                </button>
            </form>
        </section>
        
        <section class="existing-backups">
            <h2>Existing Backups</h2>
            <?php if (empty($existingBackups)): ?>
                <p>No existing backups found.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($existingBackups as $backup): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($backup); ?></td>
                                <td><?php echo formatFileSize($backupDir . $backup); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', filemtime($backupDir . $backup)); ?></td>
                                <td>
                                    <a href="?download=<?php echo urlencode($backup); ?>" 
                                       class="btn btn-small btn-download">Download</a>
                                    <form method="post" class="form-inline" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                        <input type="hidden" name="backup_filename" value="<?php echo htmlspecialchars($backup); ?>">
                                        <button type="submit" name="delete_backup" 
                                                class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/admin-footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add client-side validation or enhanced interactions
        const backupForm = document.querySelector('.form-backup');
        backupForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Backup...';
        });
    });
    </script>
</body>
</html>

<?php
// Helper function to format file size
function formatFileSize($filepath) {
    $bytes = filesize($filepath);
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
?>