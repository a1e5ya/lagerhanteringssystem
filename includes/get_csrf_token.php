<?php
/**
 * CSRF Token Refresh Endpoint
 * 
 * This endpoint provides fresh CSRF tokens for AJAX requests.
 * It supports both authenticated and unauthenticated requests,
 * but requires a valid session.
 * 
 * @package    KarisAntikvariat
 * @subpackage Security
 * @author     Security Team
 * @version    1.0
 */

// Include initialization file
require_once '../init.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Rate limiting for CSRF token requests
$rateLimit = checkRateLimit('csrf_refresh', 20, 300, null, false); // 20 requests per 5 minutes, don't increment yet
if (!$rateLimit['allowed']) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many token refresh requests. Please wait before trying again.',
        'retry_after' => $rateLimit['time_until_reset']
    ]);
    exit;
}

// Now increment the rate limit counter
checkRateLimit('csrf_refresh', 20, 300);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Generate a fresh CSRF token
    $tokenData = refreshCSRFToken();
    
    // Log token refresh for security monitoring
    $logData = [
        'action' => 'csrf_token_refresh',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'session_id' => session_id(),
        'user_id' => $_SESSION['user_id'] ?? 'anonymous',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    error_log('CSRF token refreshed: ' . json_encode($logData));
    
    // Return the new token
    echo json_encode([
        'success' => true,
        'token' => $tokenData['token'],
        'expires_at' => $tokenData['expires_at'],
        'message' => 'Token refreshed successfully'
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log('CSRF token refresh error: ' . $e->getMessage());
    
    // Return generic error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to refresh token. Please reload the page.'
    ]);
}
?>