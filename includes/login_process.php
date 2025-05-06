<?php
/**
 * Login Process
 * 
 * Handles login form submissions
 */

// Include necessary files
require_once '../config/config.php';
require_once '../includes/db_functions.php';
require_once '../includes/auth.php';

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
        // Redirect to admin dashboard
        header("Location: ../" . $result['redirect']);
        exit;
    } else {
        // Redirect back to login with error
        header("Location: ../index.php?error=" . urlencode($result['message']));
        exit;
    }
} else {
    // If not POST request, redirect to homepage
    header("Location: ../index.php");
    exit;
}
?>