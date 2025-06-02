<?php
/**
 * Enhanced Error Handler Class
 * 
 * Provides comprehensive error handling, logging, and security-focused
 * error reporting for the Karis Antikvariat system.
 * 
 * Features:
 * - Secure error logging with rotation
 * - Development vs production error modes
 * - AJAX-aware error responses
 * - Database error handling
 * - Security event logging
 * - Performance monitoring
 * 
 * @package    KarisAntikvariat
 * @subpackage Core
 * @author     Security Team
 * @version    2.0
 */

class ErrorHandler {
    /**
     * @var bool Whether debug mode is enabled
     */
    private static $debugMode = false;
    
    /**
     * @var string Path to error log directory
     */
    private static $logPath = '';
    
    /**
     * @var array Error types that should be logged
     */
    private static $loggedErrorTypes = [
        E_ERROR, E_WARNING, E_PARSE, E_NOTICE, E_CORE_ERROR,
        E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING,
        E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE
    ];
    
    /**
     * @var array Security-sensitive error patterns
     */
    private static $securityPatterns = [
        'sql injection', 'xss', 'csrf', 'directory traversal',
        'file inclusion', 'code injection', 'command injection'
    ];
    
    /**
     * Initialize the error handler
     * 
     * @param bool $debugMode Whether to enable debug mode
     * @param string $logPath Path to log directory
     * @return void
     */
    public static function initialize($debugMode = false, $logPath = null) {
        self::$debugMode = $debugMode;
        self::$logPath = $logPath ?: (__DIR__ . '/../logs');
        
        // Create log directory if it doesn't exist
        if (!file_exists(self::$logPath)) {
            if (!mkdir(self::$logPath, 0755, true)) {
                error_log("Failed to create log directory: " . self::$logPath);
            }
        }
        
        // Set error handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleFatalError']);
        
        // Configure PHP error settings
        if (self::$debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        ini_set('log_errors', 1);
        ini_set('error_log', self::$logPath . '/php_errors.log');
    }
    
    /**
     * Handle PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number where error occurred
     * @return bool True if error was handled
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        // Don't handle errors that are suppressed with @
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        // Don't log certain error types in production
        if (!self::$debugMode && !in_array($errno, self::$loggedErrorTypes)) {
            return false;
        }
        
        $errorData = [
            'type' => 'php_error',
            'errno' => $errno,
            'error_type' => self::getErrorTypeName($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => session_id(),
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        // Check if this might be a security-related error
        $isSecurityRelated = self::isSecurityRelatedError($errstr . ' ' . $errfile);
        if ($isSecurityRelated) {
            $errorData['security_alert'] = true;
            self::logSecurityEvent('PHP_ERROR_SECURITY', $errorData);
        }
        
        // Log the error
        self::logError($errorData);
        
        // In debug mode, display error details
        if (self::$debugMode) {
            self::displayError($errorData);
        }
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception The uncaught exception
     * @return void
     */
    public static function handleException($exception) {
        $errorData = [
            'type' => 'uncaught_exception',
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => session_id(),
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        // Check for security implications
        $isSecurityRelated = self::isSecurityRelatedError($exception->getMessage());
        if ($isSecurityRelated) {
            $errorData['security_alert'] = true;
            self::logSecurityEvent('EXCEPTION_SECURITY', $errorData);
        }
        
        // Log the exception
        self::logError($errorData);
        
        // Handle response based on request type and debug mode
        if (self::isAjaxRequest()) {
            self::sendAjaxErrorResponse($exception);
        } else {
            self::sendHttpErrorResponse($exception);
        }
    }
    
    /**
     * Handle fatal errors
     * 
     * @return void
     */
    public static function handleFatalError() {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorData = [
                'type' => 'fatal_error',
                'errno' => $error['type'],
                'error_type' => self::getErrorTypeName($error['type']),
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
                'user_id' => $_SESSION['user_id'] ?? null,
                'session_id' => session_id(),
                'timestamp' => date('Y-m-d H:i:s'),
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true)
            ];
            
            // Log the fatal error
            self::logError($errorData);
            
            // Send appropriate response if headers not sent
            if (!headers_sent()) {
                if (self::isAjaxRequest()) {
                    http_response_code(500);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => self::$debugMode ? $error['message'] : 'A system error occurred'
                    ]);
                } else {
                    http_response_code(500);
                    header('Content-Type: text/html; charset=UTF-8');
                    
                    if (self::$debugMode) {
                        echo self::getDebugErrorPage($errorData);
                    } else {
                        echo self::getProductionErrorPage();
                    }
                }
            }
        }
    }
    
    /**
     * Handle database errors specifically
     * 
     * @param PDOException $exception Database exception
     * @param string $query The SQL query that failed (optional)
     * @param array $params Query parameters (optional)
     * @return void
     */
    public static function handleDatabaseError($exception, $query = null, $params = []) {
        $errorData = [
            'type' => 'database_error',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'sql_query' => $query,
            'sql_params' => self::$debugMode ? $params : '[HIDDEN]',
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => session_id(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Check for SQL injection attempts
        if ($query && self::containsSQLInjectionPattern($query)) {
            $errorData['security_alert'] = true;
            $errorData['suspected_attack'] = 'sql_injection';
            self::logSecurityEvent('SQL_INJECTION_ATTEMPT', $errorData);
        }
        
        self::logError($errorData);
        
        // Never expose database details to users in production
        if (!self::$debugMode) {
            throw new Exception('A database error occurred. Please try again later.');
        }
    }
    
    /**
     * Log security events
     * 
     * @param string $eventType Type of security event
     * @param array $data Event data
     * @return void
     */
    public static function logSecurityEvent($eventType, $data) {
        $securityData = [
            'event_type' => $eventType,
            'severity' => 'HIGH',
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
        ];
        
        $logFile = self::$logPath . '/security_' . date('Y-m') . '.log';
        $logEntry = date('Y-m-d H:i:s') . ' [SECURITY] ' . json_encode($securityData) . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to system log for critical events
        error_log("SECURITY ALERT [$eventType]: " . json_encode($securityData));
    }
    
    /**
     * Log application events (non-error)
     * 
     * @param string $level Log level (INFO, WARNING, ERROR)
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public static function logEvent($level, $message, $context = []) {
        $logData = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => date('Y-m-d H:i:s'),
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'localhost',
            'user_id' => $_SESSION['user_id'] ?? null
        ];
        
        $logFile = self::$logPath . '/application_' . date('Y-m') . '.log';
        $logEntry = date('Y-m-d H:i:s') . " [$level] $message " . 
                   (empty($context) ? '' : json_encode($context)) . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check if the current request is an AJAX request
     * 
     * @return bool True if AJAX request
     */
    private static function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Check if error message contains security-related patterns
     * 
     * @param string $message Error message to check
     * @return bool True if security-related
     */
    private static function isSecurityRelatedError($message) {
        $message = strtolower($message);
        
        foreach (self::$securityPatterns as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if SQL query contains injection patterns
     * 
     * @param string $query SQL query to check
     * @return bool True if suspicious patterns found
     */
    private static function containsSQLInjectionPattern($query) {
        $suspiciousPatterns = [
            '/union\s+select/i',
            '/\'\s*or\s*\'/i',
            '/\'\s*and\s*\'/i',
            '/--\s*$/m',
            '/\/\*.*?\*\//s',
            '/\bexec\s*\(/i',
            '/\beval\s*\(/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $query)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get human-readable error type name
     * 
     * @param int $errno Error number
     * @return string Error type name
     */
    private static function getErrorTypeName($errno) {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $errorTypes[$errno] ?? 'Unknown Error';
    }
    
    /**
     * Log error data to file
     * 
     * @param array $errorData Error data to log
     * @return void
     */
    private static function logError($errorData) {
        $logFile = self::$logPath . '/errors_' . date('Y-m') . '.log';
        $logEntry = date('Y-m-d H:i:s') . ' [ERROR] ' . json_encode($errorData) . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Rotate logs if they get too large (10MB)
        if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) {
            self::rotateLog($logFile);
        }
    }
    
    /**
     * Rotate log file when it gets too large
     * 
     * @param string $logFile Path to log file
     * @return void
     */
    private static function rotateLog($logFile) {
        $rotatedFile = $logFile . '.' . date('Y-m-d-H-i-s');
        rename($logFile, $rotatedFile);
        
        // Compress old log file
        if (function_exists('gzencode')) {
            $content = file_get_contents($rotatedFile);
            file_put_contents($rotatedFile . '.gz', gzencode($content));
            unlink($rotatedFile);
        }
    }
    
    /**
     * Display error in debug mode
     * 
     * @param array $errorData Error data
     * @return void
     */
    private static function displayError($errorData) {
        if (!headers_sent()) {
            echo "<div style='background: #ff6b6b; color: white; padding: 10px; margin: 10px; border-radius: 5px;'>";
            echo "<strong>DEBUG ERROR:</strong> {$errorData['error_type']} in {$errorData['file']} on line {$errorData['line']}<br>";
            echo "<strong>Message:</strong> {$errorData['message']}";
            echo "</div>";
        }
    }
    
    /**
     * Send AJAX error response
     * 
     * @param Throwable $exception The exception
     * @return void
     */
    private static function sendAjaxErrorResponse($exception) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
            
            echo json_encode([
                'success' => false,
                'message' => self::$debugMode ? 
                    $exception->getMessage() : 
                    'An error occurred while processing your request.',
                'debug' => self::$debugMode ? [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ] : null
            ]);
        }
    }
    
    /**
     * Send HTTP error response
     * 
     * @param Throwable $exception The exception
     * @return void
     */
    private static function sendHttpErrorResponse($exception) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
            
            if (self::$debugMode) {
                echo self::getDebugErrorPage([
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ]);
            } else {
                echo self::getProductionErrorPage();
            }
        }
    }
    
    /**
     * Get debug error page HTML
     * 
     * @param array $errorData Error data
     * @return string HTML content
     */
    private static function getDebugErrorPage($errorData) {
        return "<!DOCTYPE html>
        <html>
        <head>
            <title>System Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .error-box { background: #ff6b6b; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                .details { background: #f8f9fa; padding: 15px; border-radius: 5px; }
                pre { background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <h1>System Error (Debug Mode)</h1>
                <p><strong>Message:</strong> " . htmlspecialchars($errorData['message']) . "</p>
                <p><strong>File:</strong> " . htmlspecialchars($errorData['file']) . "</p>
                <p><strong>Line:</strong> " . htmlspecialchars($errorData['line']) . "</p>
            </div>
            " . (isset($errorData['trace']) ? "<div class='details'><h3>Stack Trace:</h3><pre>" . htmlspecialchars($errorData['trace']) . "</pre></div>" : "") . "
        </body>
        </html>";
    }
    
    /**
     * Get production error page HTML
     * 
     * @return string HTML content
     */
    private static function getProductionErrorPage() {
        return "<!DOCTYPE html>
        <html>
        <head>
            <title>System Error</title>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
                .container { max-width: 600px; margin: 50px auto; text-align: center; }
                .error-icon { font-size: 64px; color: #dc3545; margin-bottom: 20px; }
                h1 { color: #343a40; margin-bottom: 20px; }
                p { color: #6c757d; margin-bottom: 30px; }
                .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
                .btn:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error-icon'>⚠️</div>
                <h1>Ett oväntat fel inträffade</h1>
                <p>Vi ber om ursäkt för besväret. Ett tekniskt fel har uppstått. Vänligen försök igen senare.</p>
                <a href='/' class='btn'>Tillbaka till startsidan</a>
            </div>
        </body>
        </html>";
    }
}
?>