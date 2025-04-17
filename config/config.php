
<?php

/**
 * Configuration File
 * 
 * Contains:
 * - Database connection parameters
 * - Site configuration settings
 * - Error reporting settings
 * - Path definitions
 */

// File: include/config.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$host = '127.0.0.1';
$db   = 'ka_lagerhanteringssystem';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES      => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log the error and display a user-friendly message
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Could not connect to the database. Please try again later.');
}
?>