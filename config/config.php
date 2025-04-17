<?php
/**
 * Configuration File
 * 
 * Contains:
 * - Database connection parameters
 * - Site configuration settings
 * - Error reporting settings
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'ka_lagerhanteringssystem');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME', 'Karis Antikvariat');

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// Initialize database connection
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the error
    error_log('Database Connection Error: ' . $e->getMessage());
    
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>Database Error:</strong> Unable to connect to the database.<br>";
    echo "Details: " . $e->getMessage();
    echo "</div>";
}
?>