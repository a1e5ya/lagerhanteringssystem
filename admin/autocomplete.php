<?php
/**
 * Autocomplete API endpoint for authors and publishers
 * 
 * Provides JSON responses for autocomplete functionality in forms.
 * Supports author names and publisher names with search suggestions.
 * 
 * @package BookManagement
 * @author  Web Development Team
 * @version 1.2.0
 * @since   1.0.0
 */

require_once '../init.php';

/**
 * Sanitize string input with proper validation
 * 
 * @param string $input Input string to sanitize
 * @param int $maxLength Maximum allowed length
 * @return string Sanitized string
 */
function sanitizeString($input, $maxLength = 255) {
    if (!is_string($input)) return '';
    // Remove null bytes and control characters
    $input = str_replace(chr(0), '', $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    return substr(trim($input), 0, $maxLength);
}

/**
 * Send JSON error response
 * 
 * @param int $code HTTP status code
 * @param string $message Error message
 * @return void
 */
function sendJsonError($code, $message) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $message, 'success' => false]);
    exit;
}

/**
 * Send JSON success response
 * 
 * @param array $data Response data
 * @return void
 */
function sendJsonSuccess($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => $data, 'success' => true]);
    exit;
}

// Input validation and sanitization
$type = sanitizeString($_GET['type'] ?? '', 20);
$query = sanitizeString($_GET['query'] ?? '', 100);

// Validate required parameters
if (empty($query) || empty($type)) {
    sendJsonError(400, 'Missing required parameters');
}

// Validate minimum query length for performance
if (strlen($query) < 2) {
    sendJsonError(400, 'Query must be at least 2 characters long');
}

// Define allowed autocomplete types for security
$allowedTypes = ['author', 'publisher'];
if (!in_array($type, $allowedTypes)) {
    sendJsonError(400, 'Invalid autocomplete type');
}

try {
    $results = [];
    
    switch ($type) {
        case 'author':
            /**
             * Search for author names matching the query
             * Uses LIKE with wildcard for prefix matching
             */
            $stmt = $pdo->prepare("SELECT DISTINCT author_name FROM author WHERE author_name LIKE :query ORDER BY author_name LIMIT 10");
            $stmt->bindValue(':query', $query . '%', PDO::PARAM_STR);
            break;
            
        case 'publisher':
            /**
             * Search for publisher names matching the query
             * Uses LIKE with wildcard for prefix matching
             */
            $stmt = $pdo->prepare("SELECT DISTINCT publisher FROM product WHERE publisher LIKE :query ORDER BY publisher LIMIT 10");
            $stmt->bindValue(':query', $query . '%', PDO::PARAM_STR);
            break;
    }
    
    // Execute query and fetch results
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Sanitize output data
    $sanitizedResults = [];
    foreach ($results as $result) {
        if (!empty($result)) {
            $sanitizedResults[] = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Send successful response
    sendJsonSuccess($sanitizedResults);
    
} catch (PDOException $e) {
    // Log error for debugging (in production, log to file)
    error_log("Autocomplete database error: " . $e->getMessage());
    
    // Send generic error to user
    sendJsonError(500, 'Database error occurred');
    
} catch (Exception $e) {
    // Log unexpected errors
    error_log("Autocomplete unexpected error: " . $e->getMessage());
    
    // Send generic error to user
    sendJsonError(500, 'An unexpected error occurred');
}
?>