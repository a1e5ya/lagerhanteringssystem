<?php
// Include initialization file
require_once '../init.php';

// Rate limiting for login attempts
if (!checkRateLimit('login', 5, 300)) { // 5 attempts per 5 minutes
    header('Location: ' . url('index.php', ['error' => 'För många inloggningsförsök. Vänta 5 minuter.']));
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
        // Redirect to admin dashboard using routing system
        header("Location: " . url($result['redirect']));
        exit;
    } else {
        // Redirect back to login with error using routing system
        header("Location: " . url('index.php', ['error' => urlencode($result['message'])]));
        exit;
    }
} else {
    // If not POST request, redirect to homepage using routing system
    header("Location: " . url('index.php'));
    exit;
}
?>