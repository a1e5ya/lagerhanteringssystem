<?php
/**
 * Autocomplete API endpoint for authors and publishers
 * 
 * Provides JSON responses for autocomplete functionality in forms.
 * Supports author names and publisher names with search suggestions.
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

require_once '../init.php';

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

try {
    // Input validation and sanitization using existing security functions
    $type = sanitizeInput($_GET['type'] ?? '', 'string', 20);
    $query = sanitizeInput($_GET['query'] ?? '', 'string', 100);
    
    // Validate required parameters
    if (empty($query) || empty($type)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }
    
    // Validate minimum query length for performance
    if (strlen($query) < 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Query must be at least 2 characters long']);
        exit;
    }
    
    // Define allowed autocomplete types for security
    $allowedTypes = ['author', 'publisher'];
    if (!in_array($type, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid autocomplete type']);
        exit;
    }
    
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
            $stmt = $pdo->prepare("SELECT DISTINCT publisher FROM product WHERE publisher LIKE :query AND publisher IS NOT NULL AND publisher != '' ORDER BY publisher LIMIT 10");
            $stmt->bindValue(':query', $query . '%', PDO::PARAM_STR);
            break;
    }
    
    // Execute query and fetch results
    $stmt->execute();
    $rawResults = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Sanitize output data and filter empty values
    foreach ($rawResults as $result) {
        if (!empty(trim($result))) {
            $results[] = htmlspecialchars(trim($result), ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Return results as a simple array (not wrapped in an object)
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log("Autocomplete database error: " . $e->getMessage());
    
    // Send error response
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
    
} catch (InvalidArgumentException $e) {
    // Handle validation errors
    http_response_code(400);
    echo json_encode(['error' => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]);
    
} catch (Exception $e) {
    // Log unexpected errors
    error_log("Autocomplete unexpected error: " . $e->getMessage());
    
    // Send generic error response
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}
?>