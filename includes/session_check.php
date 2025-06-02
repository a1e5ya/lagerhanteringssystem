<?php
/**
 * Session Check
 * 
 * AJAX endpoint to check if the user's session is still valid
 * This file is called periodically from JavaScript to ensure the user
 * is automatically logged out across all tabs if they log out in one tab
 */

// Include initialization file instead of multiple requires
require_once '../init.php';

// Check CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRFToken();
}

header('Content-Type: application/json');

// Check if session is valid
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isValid = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

echo json_encode(['valid' => $isValid]);
?>