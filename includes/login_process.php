<?php
// Include initialization file
require_once '../init.php';

// Rate limiting for login attempts
if (!checkRateLimit('login', 5, 300)) { // 5 attempts per 5 minutes
    $_SESSION['message'] = [
        'success' => false,
        'message' => 'För många inloggningsförsök. Vänta 5 minuter.'
    ];
    header('Location: ' . url('index.php'));
    exit;
}

// Check CSRF token
checkCSRFToken();

/**
 * Login Process
 * 
 * Handles login form submissions
 */

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Perform login
    $result = login($username, $password, $remember);
    
    // Handle result
    if ($result['success']) {
        // Successful login - redirect to admin dashboard
        header("Location: " . url($result['redirect']));
        exit;
    } else {
        // Failed login - store message in session to avoid URL encoding
        $_SESSION['message'] = [
            'success' => false,
            'message' => $result['message']
        ];
        header("Location: " . url('index.php'));
        exit;
    }
} else {
    // If not POST request, redirect to homepage
    header("Location: " . url('index.php'));
    exit;
}
?>