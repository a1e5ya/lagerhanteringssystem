<?php
/**
 * Enhanced Application Initialization File
 *
 * This file serves as a secure central point for loading all required dependencies
 * and initializing security features for the Karis Antikvariat application.
 * 
 * Security Features:
 * - Proper error handling initialization
 * - CSRF protection setup
 * - Session security configuration
 * - Input validation setup
 * - Security headers configuration
 * 
 * @package    KarisAntikvariat
 * @subpackage Core
 * @author     Security Team
 * @version    2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__));
}

// Set the absolute base path for includes
$include_base = __DIR__;

// Define debug mode based on environment or config
if (!defined('DEBUG_MODE')) {
    // Check if we're in development environment
    $isDevelopment = (
        isset($_SERVER['SERVER_NAME']) && 
        (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false ||
         strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false ||
         strpos($_SERVER['SERVER_NAME'], '.local') !== false)
    );
    
    define('DEBUG_MODE', $isDevelopment);
}

// Initialize error handling early
require_once $include_base . '/includes/ErrorHandler.php';
ErrorHandler::initialize(DEBUG_MODE, $include_base . '/logs');

try {
    // Include configuration first (which also includes routing)
    if (!file_exists($include_base . '/config/config.php')) {
        throw new Exception('Configuration file not found');
    }
    require_once $include_base . '/config/config.php';

    // Include core security files first
    require_once $include_base . '/includes/security.php';
    
    // Start secure session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Include core functionality files
    $coreFiles = [
        'functions.php',
        'db_functions.php',
        'auth.php',
        'ui.php',
        'Formatter.php',
        'Database.php',
        'ImageProcessor.php',
        'Paginator.php'
    ];

    foreach ($coreFiles as $file) {
        $filepath = $include_base . '/includes/' . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        } else {
            ErrorHandler::logEvent('WARNING', "Core file not found: $file", ['expected_path' => $filepath]);
        }
    }

    // Initialize database connection if PDO is configured
    if (isset($host, $user, $pass, $dbname)) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Test database connection
            $pdo->query("SELECT 1");
            
        } catch (PDOException $e) {
            ErrorHandler::handleDatabaseError($e);
            
            // In production, show generic error
            if (!DEBUG_MODE) {
                http_response_code(503);
                echo "<!DOCTYPE html><html><head><title>Service Unavailable</title></head>";
                echo "<body><h1>Service Temporarily Unavailable</h1>";
                echo "<p>The service is temporarily unavailable. Please try again later.</p></body></html>";
                exit;
            }
            throw $e;
        }
    }

    // Determine current language with validation
    $allowedLanguages = ['sv', 'fi'];
    $language = 'sv'; // Default language

    if (isset($_SESSION['language']) && in_array($_SESSION['language'], $allowedLanguages)) {
        $language = $_SESSION['language'];
    } elseif (isset($_GET['lang']) && in_array($_GET['lang'], $allowedLanguages)) {
        // Validate and sanitize language parameter
        $language = sanitizeInput($_GET['lang'], 'string', 2);
        if (in_array($language, $allowedLanguages)) {
            $_SESSION['language'] = $language;
        } else {
            $language = 'sv';
        }
    }

    // Load language strings - if ui.php defines loadLanguageStrings()
    if (function_exists('loadLanguageStrings')) {
        try {
            $strings = loadLanguageStrings($language);
        } catch (Exception $e) {
            ErrorHandler::logEvent('ERROR', 'Failed to load language strings', [
                'language' => $language,
                'error' => $e->getMessage()
            ]);
            // Fallback to Swedish
            $strings = loadLanguageStrings('sv');
        }
    }

    // Create formatter instance - if Formatter class exists
    if (class_exists('Formatter')) {
        try {
            $locale = $language === 'fi' ? 'fi_FI' : 'sv_SE';
            $formatter = new Formatter($locale);
        } catch (Exception $e) {
            ErrorHandler::logEvent('ERROR', 'Failed to create Formatter instance', [
                'locale' => $locale ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            // Create with default locale
            $formatter = new Formatter('sv_SE');
        }
    }

    // Initialize database wrapper if classes are available
    if (isset($pdo) && class_exists('Database')) {
        try {
            $database = new Database($pdo);
        } catch (Exception $e) {
            ErrorHandler::logEvent('ERROR', 'Failed to create Database instance', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // Initialize ImageProcessor if available and configured
    if (isset($pdo) && class_exists('ImageProcessor') && isset($app_config['uploads'])) {
        try {
            $imageProcessor = new ImageProcessor($pdo, $app_config['uploads']);
        } catch (Exception $e) {
            ErrorHandler::logEvent('ERROR', 'Failed to create ImageProcessor instance', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // Log successful initialization
    ErrorHandler::logEvent('INFO', 'Application initialized successfully', [
        'language' => $language,
        'debug_mode' => DEBUG_MODE,
        'session_id' => session_id(),
        'user_id' => $_SESSION['user_id'] ?? null,
        'memory_usage' => memory_get_usage(true),
        'loaded_classes' => get_declared_classes()
    ]);

} catch (Exception $e) {
    // Log initialization error
    ErrorHandler::logEvent('CRITICAL', 'Application initialization failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    // In production, show generic error page
    if (!DEBUG_MODE) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        echo "<!DOCTYPE html><html><head><title>System Error</title>";
        echo "<meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<style>body{font-family:Arial,sans-serif;margin:0;padding:20px;background:#f8f9fa;text-align:center;}";
        echo ".container{max-width:600px;margin:50px auto;padding:20px;background:white;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
        echo ".error-icon{font-size:64px;color:#dc3545;margin-bottom:20px;}";
        echo "h1{color:#343a40;margin-bottom:20px;}p{color:#6c757d;margin-bottom:30px;}";
        echo ".btn{background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;}";
        echo ".btn:hover{background:#0056b3;}</style></head><body>";
        echo "<div class='container'><div class='error-icon'>⚠️</div>";
        echo "<h1>System kunde inte initialiseras</h1>";
        echo "<p>Ett tekniskt fel har uppstått under systemstart. Vänligen kontakta support.</p>";
        echo "<a href='/' class='btn'>Tillbaka till startsidan</a></div></body></html>";
        exit;
    }
    
    // In debug mode, re-throw the exception for detailed error display
    throw $e;
}

/**
 * Set page title - call this function before including header.php
 * 
 * @param string $title Page title
 * @return void
 */
function setPageTitle($title) {
    global $pageTitle;
    $pageTitle = sanitizeInput($title, 'string', 200);
}

/**
 * Enhanced safe echo function with additional security
 * This is a global alias for the safeEcho function from functions.php
 * 
 * @param mixed $value The value to safely output
 * @param string $encoding Character encoding (default: UTF-8)
 * @return string Safely encoded string
 */
function safeOutput($value, $encoding = 'UTF-8') {
    if (function_exists('safeEcho')) {
        return safeEcho($value);
    }
    
    // Fallback implementation
    if ($value === null) {
        return '';
    }
    
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, $encoding);
}

/**
 * Check if current request is AJAX
 * 
 * @return bool True if AJAX request
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get current page name without extension
 * 
 * @return string Current page name
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF'], '.php');
}

/**
 * Check if user is on admin pages
 * 
 * @return bool True if on admin pages
 */
function isAdminArea() {
    $currentPath = $_SERVER['PHP_SELF'] ?? '';
    return strpos($currentPath, '/admin/') !== false || 
           basename($currentPath) === 'admin.php';
}

/**
 * Get client IP address with proxy support
 * 
 * @return string Client IP address
 */
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            
            // Handle comma-separated IPs (X-Forwarded-For)
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            
            // Validate IP address
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Validate and clean URL parameter
 * 
 * @param string $param Parameter name
 * @param string $type Expected type (string, int, float)
 * @param mixed $default Default value if not set or invalid
 * @param int $maxLength Maximum length for strings
 * @return mixed Cleaned parameter value
 */
function getCleanParam($param, $type = 'string', $default = null, $maxLength = 100) {
    $value = $_GET[$param] ?? $_POST[$param] ?? $default;
    
    if ($value === null || $value === '') {
        return $default;
    }
    
    try {
        return sanitizeInput($value, $type, $maxLength);
    } catch (InvalidArgumentException $e) {
        ErrorHandler::logEvent('WARNING', 'Invalid parameter provided', [
            'param' => $param,
            'value' => $value,
            'type' => $type,
            'error' => $e->getMessage(),
            'ip' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        return $default;
    }
}

/**
 * Generate secure random string
 * 
 * @param int $length Length of string to generate
 * @param string $chars Characters to use
 * @return string Random string
 * @throws Exception If secure random generation fails
 */
function generateRandomString($length = 32, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
    if ($length <= 0) {
        throw new InvalidArgumentException('Length must be positive');
    }
    
    try {
        $randomBytes = random_bytes($length);
        $string = '';
        $charsLength = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[ord($randomBytes[$i]) % $charsLength];
        }
        
        return $string;
    } catch (Exception $e) {
        ErrorHandler::logEvent('WARNING', 'Failed to generate secure random string, using fallback', [
            'error' => $e->getMessage()
        ]);
        
        // Fallback to less secure but functional method
        $string = '';
        $charsLength = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, $charsLength - 1)];
        }
        
        return $string;
    }
}

/**
 * Check if SSL/HTTPS is enabled
 * 
 * @return bool True if HTTPS
 */
function isHTTPS() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
           (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
           (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
           (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
}

/**
 * Force HTTPS redirect if not already using HTTPS
 * Call this function early in your application if you want to enforce HTTPS
 * 
 * @param bool $permanent Whether to use 301 (permanent) or 302 (temporary) redirect
 * @return void
 */
function forceHTTPS($permanent = true) {
    if (!isHTTPS() && !DEBUG_MODE) {
        $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $statusCode = $permanent ? 301 : 302;
        
        ErrorHandler::logEvent('INFO', 'HTTPS redirect performed', [
            'from' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'to' => $redirectURL,
            'status_code' => $statusCode
        ]);
        
        header("Location: $redirectURL", true, $statusCode);
        exit;
    }
}

/**
 * Clean and validate file path to prevent directory traversal
 * 
 * @param string $path File path to clean
 * @param string $basePath Base path to restrict to (optional)
 * @return string|false Cleaned path or false if invalid
 */
function cleanFilePath($path, $basePath = null) {
    if (!is_string($path) || empty($path)) {
        return false;
    }
    
    // Remove any null bytes and control characters
    $path = str_replace(chr(0), '', $path);
    $path = preg_replace('/[\x00-\x1F\x7F]/', '', $path);
    
    // Normalize path separators
    $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    
    // Remove directory traversal attempts
    $path = preg_replace('/\.\.+/', '', $path);
    
    // Remove multiple consecutive separators
    $path = preg_replace('/[' . preg_quote(DIRECTORY_SEPARATOR) . ']+/', DIRECTORY_SEPARATOR, $path);
    
    // Get real path if file exists
    if (file_exists($path)) {
        $realPath = realpath($path);
    } else {
        // For non-existent files, clean the path manually
        $realPath = $path;
    }
    
    // If base path is specified, ensure file is within it
    if ($basePath !== null && $realPath) {
        $realBasePath = realpath($basePath);
        if (!$realBasePath || strpos($realPath, $realBasePath) !== 0) {
            ErrorHandler::logEvent('WARNING', 'File path outside allowed directory', [
                'requested_path' => $path,
                'real_path' => $realPath,
                'base_path' => $basePath,
                'real_base_path' => $realBasePath
            ]);
            return false;
        }
    }
    
    return $realPath;
}

/**
 * Validate email address with additional security checks
 * 
 * @param string $email Email address to validate
 * @return bool True if valid email
 */
function isValidEmail($email) {
    if (!is_string($email) || strlen($email) > 254) {
        return false;
    }
    
    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Additional checks for common attack patterns
    $suspiciousPatterns = [
        '/[<>]/',           // HTML tags
        '/javascript:/i',   // JavaScript protocols
        '/data:/i',         // Data URLs
        '/\0/',            // Null bytes
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $email)) {
            return false;
        }
    }
    
    return true;
}

/**
 * Format file size in human readable format
 * 
 * @param int $bytes File size in bytes
 * @param int $precision Number of decimal places
 * @return string Formatted file size
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Check if the current user has a specific permission level
 * 
 * @param int $requiredLevel Required permission level (1=Admin, 2=Editor, 3=Guest)
 * @return bool True if user has required permission
 */
function hasPermission($requiredLevel) {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        return false;
    }
    
    $userRole = $_SESSION['user_role'] ?? 999;
    return $userRole <= $requiredLevel;
}

/**
 * Validate and format date input
 * 
 * @param string $date Date string to validate
 * @param string $format Expected format (default: Y-m-d)
 * @param string $outputFormat Output format (default: same as input format)
 * @return string|false Formatted date or false if invalid
 */
function validateAndFormatDate($date, $format = 'Y-m-d', $outputFormat = null) {
    if (!validateDate($date, $format)) {
        return false;
    }
    
    $outputFormat = $outputFormat ?: $format;
    $dateObj = DateTime::createFromFormat($format, $date);
    return $dateObj->format($outputFormat);
}

/**
 * Create a secure file name from user input
 * 
 * @param string $filename Original filename
 * @param string $extension Extension to force (optional)
 * @return string Safe filename
 */
function createSafeFilename($filename, $extension = null) {
    // Remove path information
    $filename = basename($filename);
    
    // Get file extension if not provided
    if ($extension === null) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
    }
    
    // Get filename without extension
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    // Remove dangerous characters and sanitize
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    $extension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
    
    // Limit length
    $name = substr($name, 0, 100);
    $extension = substr($extension, 0, 10);
    
    // Ensure we have a name
    if (empty($name)) {
        $name = 'file_' . time();
    }
    
    return $name . ($extension ? '.' . $extension : '');
}

// Set global error handler for any remaining uncaught errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (class_exists('ErrorHandler')) {
        return ErrorHandler::handleError($errno, $errstr, $errfile, $errline);
    }
    
    // Fallback error handling
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    return false;
});

// Set exception handler
set_exception_handler(function($exception) {
    if (class_exists('ErrorHandler')) {
        ErrorHandler::handleException($exception);
    } else {
        error_log("Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
        
        if (DEBUG_MODE) {
            echo "<div style='background:#ffebee;color:#c62828;padding:20px;margin:10px;border-left:5px solid #f44336;'>";
            echo "<h3>Uncaught Exception</h3>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " on line " . $exception->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            echo "<h1>System Error</h1><p>An unexpected error occurred. Please try again later.</p>";
        }
    }
});

// Register shutdown function for fatal errors
register_shutdown_function(function() {
    if (class_exists('ErrorHandler')) {
        ErrorHandler::handleFatalError();
    } else {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            error_log("Fatal error: {$error['message']} in {$error['file']} on line {$error['line']}");
            
            if (!headers_sent()) {
                http_response_code(500);
                if (!DEBUG_MODE) {
                    echo "<h1>System Error</h1><p>A fatal error occurred. Please contact support.</p>";
                }
            }
        }
    }
});

// Memory limit and execution time monitoring
if (DEBUG_MODE) {
    register_shutdown_function(function() {
        $memoryUsage = memory_get_peak_usage(true);
        $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        
        if (class_exists('ErrorHandler')) {
            ErrorHandler::logEvent('DEBUG', 'Request completed', [
                'memory_peak' => formatFileSize($memoryUsage),
                'execution_time' => round($executionTime, 3) . 's',
                'url' => $_SERVER['REQUEST_URI'] ?? 'CLI'
            ]);
        }
    });
}
?>