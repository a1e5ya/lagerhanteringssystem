<?php
/**
 * Enhanced CSRF Protection and Security Headers Implementation
 * Updated with reCAPTCHA support
 */

function setSecurityHeaders() {
    // Common security headers for all responses
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    
    // Hide server information
    header('Server: ');
    header('X-Powered-By: ');
    
    // Force HTTPS connections (if using HTTPS)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    
    // Detect if this is an AJAX request
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    if ($isAjax) {
        // AJAX-specific headers
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Stricter CSP for AJAX/JSON responses
        header("Content-Security-Policy: default-src 'none'; script-src 'none'; style-src 'none';");
    } else {
        // HTML page headers - Content Security Policy for regular pages with reCAPTCHA support
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
               "https://cdn.jsdelivr.net " .
               "https://cdnjs.cloudflare.com " .
               "https://code.jquery.com " .
               "https://ajax.googleapis.com " .
               "https://unpkg.com " .
               "https://stackpath.bootstrapcdn.com " .
               "https://www.google.com " .
               "https://www.gstatic.com; " .
               "style-src 'self' 'unsafe-inline' " .
               "https://cdn.jsdelivr.net " .
               "https://cdnjs.cloudflare.com " .
               "https://fonts.googleapis.com " .
               "https://stackpath.bootstrapcdn.com " .
               "https://code.jquery.com; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' " .
               "https://cdnjs.cloudflare.com " .
               "https://fonts.gstatic.com " .
               "https://stackpath.bootstrapcdn.com; " .
               "connect-src 'self' https://www.google.com; " .
               "frame-src https://www.google.com; " .
               "frame-ancestors 'none';";
        header("Content-Security-Policy: $csp");
        
        // Cache control for HTML pages
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    }
}

/**
 * Generate a cryptographically secure CSRF token
 * 
 * @return string The generated CSRF token
 * @throws Exception If secure random bytes cannot be generated
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate new token if none exists or if it's expired
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > 3600) { // 1 hour expiry
        
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        } catch (Exception $e) {
            error_log("Failed to generate CSRF token: " . $e->getMessage());
            // Fallback method
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true) . time());
            $_SESSION['csrf_token_time'] = time();
        }
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token with timing attack prevention
 * 
 * @param string $token The token to validate
 * @return bool True if token is valid, false otherwise
 */
function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists in session
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token has expired (1 hour)
    if ((time() - $_SESSION['csrf_token_time']) > 3600) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token HTML input field for forms
 * 
 * @return string HTML input field with CSRF token
 */
function getCSRFTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Get CSRF token for JavaScript/AJAX requests
 * 
 * @return string The CSRF token
 */
function getCSRFToken() {
    return generateCSRFToken();
}

/**
 * Refresh CSRF token (for AJAX endpoint)
 * 
 * @return array Array with success status and new token
 */
function refreshCSRFToken() {
    // Force generation of new token
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    $newToken = generateCSRFToken();
    
    return [
        'success' => true,
        'token' => $newToken,
        'expires_at' => time() + 3600
    ];
}

/**
 * Enhanced CSRF token validation middleware
 * Supports form data, JSON requests, and multiple token sources
 * 
 * @param bool $exitOnFailure Whether to exit on CSRF failure (default: true)
 * @return bool True if validation passes, false otherwise
 * @throws Exception If CSRF validation fails and exitOnFailure is true
 */
function checkCSRFToken($exitOnFailure = true) {
    // Only check CSRF for POST, PUT, PATCH, DELETE requests
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        return true;
    }
    
    $token = '';
    
    // Try multiple sources for the CSRF token
    // 1. POST data
    if (isset($_POST['csrf_token'])) {
        $token = $_POST['csrf_token'];
    }
    // 2. HTTP headers
    elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    // 3. Alternative header format
    elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    // 4. JSON request body
    else {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            if ($input) {
                $data = json_decode($input, true);
                if (isset($data['csrf_token'])) {
                    $token = $data['csrf_token'];
                }
            }
        }
    }
    
    // Validate the token
    if (!validateCSRFToken($token)) {
        // Log the failed attempt with comprehensive details
        $logData = [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $method,
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'none',
            'time' => date('Y-m-d H:i:s'),
            'session_id' => session_id(),
            'user_id' => $_SESSION['user_id'] ?? 'anonymous'
        ];
        error_log('CSRF token validation failed: ' . json_encode($logData));
        
        if ($exitOnFailure) {
            // Handle AJAX requests differently
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                http_response_code(419); // 419 Authentication Timeout (Laravel convention)
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Säkerhetstoken ogiltigt eller saknas',
                    'error_code' => 'CSRF_TOKEN_MISMATCH'
                ]);
                exit;
            }
            
            // For regular requests, redirect with error
            header('Location: ' . url('index.php', ['error' => 'csrf_invalid']));
            exit;
        }
        
        return false;
    }
    
    return true;
}

/**
 * Enhanced rate limiting function with sliding window algorithm
 * 
 * @param string $action Action being rate limited
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $timeWindow Time window in seconds
 * @param string|null $identifier Custom identifier (defaults to IP)
 * @param bool $increment Whether to increment the counter (default: true)
 * @return array Rate limit status with details
 */
function checkRateLimit($action = 'general', $maxAttempts = 10, $timeWindow = 300, $identifier = null, $increment = true) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Use IP address if no custom identifier provided
    if ($identifier === null) {
        $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    $key = 'rate_limit_' . $action . '_' . md5($identifier);
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }
    
    // Clean old attempts (sliding window)
    $_SESSION[$key] = array_filter($_SESSION[$key], function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });
    
    $currentAttempts = count($_SESSION[$key]);
    
    // Check if limit exceeded
    if ($currentAttempts >= $maxAttempts) {
        // Log rate limit violation
        error_log("Rate limit exceeded for action '$action' by identifier '$identifier'. " .
                 "Attempts: $currentAttempts/$maxAttempts in {$timeWindow}s window");
        
        $oldestAttempt = min($_SESSION[$key]);
        $timeUntilReset = $timeWindow - ($now - $oldestAttempt);
        
        return [
            'allowed' => false,
            'attempts' => $currentAttempts,
            'max_attempts' => $maxAttempts,
            'time_window' => $timeWindow,
            'time_until_reset' => max(0, $timeUntilReset),
            'message' => "Rate limit exceeded. Try again in " . ceil($timeUntilReset / 60) . " minutes."
        ];
    }
    
    // Add current attempt if incrementing
    if ($increment) {
        $_SESSION[$key][] = $now;
    }
    
    return [
        'allowed' => true,
        'attempts' => $currentAttempts + ($increment ? 1 : 0),
        'max_attempts' => $maxAttempts,
        'time_window' => $timeWindow,
        'remaining_attempts' => $maxAttempts - $currentAttempts - ($increment ? 1 : 0)
    ];
}

/**
 * Configure secure session settings
 * 
 * @return void
 */
function configureSecureSession() {
    // Set secure session parameters before starting session
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_lifetime', 0); // Session cookies only
    
    // Set session name to something non-default
    session_name('KA_SECURE_SESSION');
    
    // Regenerate session ID periodically and on privilege changes
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
        
        // Also regenerate on login state changes
        if (!isset($_SESSION['regenerated'])) {
            $_SESSION['regenerated'] = time();
        }
    }
}

/**
 * Enhanced input sanitization with type-specific validation
 * 
 * @param mixed $input The input to sanitize
 * @param string $type The expected type (string, int, float, email, json, html, filename)
 * @param int $maxLength Maximum length for strings
 * @param array $options Additional options for specific types
 * @return mixed Sanitized input
 * @throws InvalidArgumentException If input fails validation
 */
function sanitizeInput($input, $type = 'string', $maxLength = 1000, $options = []) {
    // Handle null input
    if ($input === null) {
        return $options['allow_null'] ?? false ? null : '';
    }
    
    switch ($type) {
        case 'int':
            $sanitized = filter_var($input, FILTER_VALIDATE_INT);
            if ($sanitized === false) {
                throw new InvalidArgumentException("Invalid integer value");
            }
            
            // Check min/max if specified
            if (isset($options['min']) && $sanitized < $options['min']) {
                throw new InvalidArgumentException("Value below minimum: {$options['min']}");
            }
            if (isset($options['max']) && $sanitized > $options['max']) {
                throw new InvalidArgumentException("Value above maximum: {$options['max']}");
            }
            
            return $sanitized;
            
        case 'float':
            $sanitized = filter_var($input, FILTER_VALIDATE_FLOAT);
            if ($sanitized === false) {
                throw new InvalidArgumentException("Invalid float value");
            }
            return $sanitized;
            
        case 'email':
            $sanitized = filter_var($input, FILTER_VALIDATE_EMAIL);
            if ($sanitized === false) {
                throw new InvalidArgumentException("Invalid email format");
            }
            return $sanitized;
            
        case 'url':
            $sanitized = filter_var($input, FILTER_VALIDATE_URL);
            if ($sanitized === false) {
                throw new InvalidArgumentException("Invalid URL format");
            }
            return $sanitized;
            
        case 'json':
            if (!is_string($input)) {
                throw new InvalidArgumentException("JSON data must be a string");
            }
            if (strlen($input) > $maxLength) {
                throw new InvalidArgumentException("JSON data exceeds maximum length");
            }
            
            // Validate JSON syntax
            json_decode($input);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException("Invalid JSON syntax: " . json_last_error_msg());
            }
            
            return $input;
            
        case 'filename':
            if (!is_string($input)) {
                throw new InvalidArgumentException("Filename must be a string");
            }
            
            // Remove dangerous characters and path traversal attempts
            $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($input));
            $sanitized = str_replace(['..', './'], '', $sanitized);
            
            if (empty($sanitized)) {
                throw new InvalidArgumentException("Invalid filename");
            }
            
            return $sanitized;
            
        case 'html':
            if (!is_string($input) && !is_numeric($input)) {
                return '';
            }
            
            // Allow specific HTML tags if specified
            $allowedTags = $options['allowed_tags'] ?? '';
            return strip_tags((string)$input, $allowedTags);
            
        case 'string':
        default:
            if (!is_string($input) && !is_numeric($input)) {
                return '';
            }
            
            // Remove null bytes and control characters
            $sanitized = str_replace(chr(0), '', (string)$input);
            $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);
            
            // Trim and limit length
            $sanitized = trim($sanitized);
            if (mb_strlen($sanitized) > $maxLength) {
                $sanitized = mb_substr($sanitized, 0, $maxLength);
            }
            
            return $sanitized;
    }
}

/**
 * Validate date format with additional security checks
 * 
 * @param string $date Date string to validate
 * @param string $format Expected date format (default: Y-m-d)
 * @return bool True if valid, false otherwise
 */
function validateDate($date, $format = 'Y-m-d') {
    if (!is_string($date) || empty($date)) {
        return false;
    }
    
    // Basic length check
    if (strlen($date) > 20) {
        return false;
    }
    
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate and sanitize file upload
 * 
 * @param array $file $_FILES array element
 * @param array $options Upload validation options
 * @return array Validation result with sanitized data
 */
function validateFileUpload($file, $options = []) {
    $defaultOptions = [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'check_content' => true
    ];
    
    $options = array_merge($defaultOptions, $options);
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'valid' => false,
            'error' => 'File upload error: ' . $file['error']
        ];
    }
    
    // Check file size
    if ($file['size'] > $options['max_size']) {
        return [
            'valid' => false,
            'error' => 'File too large. Maximum size: ' . formatBytes($options['max_size'])
        ];
    }
    
    // Check MIME type
    if (!in_array($file['type'], $options['allowed_types'])) {
        return [
            'valid' => false,
            'error' => 'Invalid file type. Allowed: ' . implode(', ', $options['allowed_types'])
        ];
    }
    
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $options['allowed_extensions'])) {
        return [
            'valid' => false,
            'error' => 'Invalid file extension. Allowed: ' . implode(', ', $options['allowed_extensions'])
        ];
    }
    
    // Check actual file content if required
    if ($options['check_content']) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $actualType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($actualType, $options['allowed_types'])) {
            return [
                'valid' => false,
                'error' => 'File content does not match declared type'
            ];
        }
    }
    
    return [
        'valid' => true,
        'sanitized_name' => sanitizeInput(basename($file['name']), 'filename'),
        'size' => $file['size'],
        'type' => $file['type'],
        'extension' => $extension
    ];
}

/**
 * Format bytes to human readable format
 * 
 * @param int $bytes Number of bytes
 * @return string Formatted string
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Initialize security settings
 * Should be called early in application bootstrap
 * 
 * @return void
 */
function initializeSecurity() {
    // Configure secure session settings
    configureSecureSession();
    
    // Set security headers
    setSecurityHeaders();
    
    // Set error reporting based on environment
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
    } else {
        error_reporting(0);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
    }
    
    // Set secure PHP configuration
    ini_set('expose_php', 0);
    ini_set('allow_url_fopen', 0);
    ini_set('allow_url_include', 0);
    
    // Register shutdown function to handle fatal errors
    register_shutdown_function('handleFatalError');
}

/**
 * Handle fatal errors securely
 * 
 * @return void
 */
function handleFatalError() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log the error
        error_log("Fatal error: " . json_encode($error));
        
        // In production, show generic error page
        if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=UTF-8');
            }
            echo '<!DOCTYPE html><html><head><title>System Error</title></head>';
            echo '<body><h1>System Error</h1><p>An unexpected error occurred. Please try again later.</p></body></html>';
        }
    }
}

// Initialize security when this file is included
initializeSecurity();
?>