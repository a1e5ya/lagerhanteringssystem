<?php
/**
 * Configuration File
 * 
 * Contains:
 * - Database connection settings
 * - Application configuration
 * - Error handling settings
 * 
 * @package    KarisAntikvariat
 * @subpackage Configuration
 */

// Include the routing system
require_once __DIR__ . '/../includes/routes.php';

// Initialize routes - CHANGE THIS VALUE when migrating to a new server
Routes::init('/prog23/lagerhanteringssystem');

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 during development, 0 in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Timezone setting
date_default_timezone_set('Europe/Helsinki');

// Database configuration
$db_config = [
    'host'     => 'localhost',
    'dbname'   => 'ka_lagerhanteringssystem',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];

// Application settings
$app_config = [
    'name'        => 'Karis Antikvariat',
    'version'     => '3.0',
    'admin_email' => 'admin@example.com',
    'pagination'  => [
        'default_limit' => 10,
        'max_limit'     => 200,
        'limit_options' => [10, 20, 50, 100, 200]
    ],
    'uploads' => [
        'max_size' => 5242880, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'product_images_path' => 'assets/uploads/products', // Updated path
        'product_images_url' => 'assets/uploads/products',  // URL path for frontend
        'max_images_per_product' => 10,
        'thumbnail_sizes' => [
            'small' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 600, 'height' => 600]
        ]
    ],
    'languages' => [
        'default' => 'sv',
        'available' => ['sv', 'fi']
    ]
];

// Global application constants
define('APP_NAME', $app_config['name']);
define('APP_VERSION', $app_config['version']);
define('IS_DEVELOPMENT', $_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], 'dev.') === 0);
define('BASE_PATH', Routes::getBasePath());

// Define upload paths as constants for easy access
define('UPLOAD_PATH', __DIR__ . '/../' . $app_config['uploads']['product_images_path']);
define('UPLOAD_URL', $app_config['uploads']['product_images_url']);

// Establish database connection
try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $db_config['options']);
} catch (PDOException $e) {
    // Log error and display user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}