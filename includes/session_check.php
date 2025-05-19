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

// Return JSON response
header('Content-Type: application/json');

// Check if session is valid
$isValid = validateSession();

// Return result
echo json_encode(['valid' => $isValid]);
?>