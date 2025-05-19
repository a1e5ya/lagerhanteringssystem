<?php
/**
 * Login Process
 * 
 * Handles login form submissions
 */

// Include initialization file
require_once '../init.php';

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