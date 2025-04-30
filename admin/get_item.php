<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once '../config/config.php';

header('Content-Type: application/json');

// Check if this is a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get parameters
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Validate input
if ($id <= 0 || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Prepare the SQL statement based on type
    switch ($type) {
        case 'category':
            $stmt = $pdo->prepare("SELECT category_id as id, category_name as name FROM category WHERE category_id = ?");
            break;
            
        case 'shelf':
            $stmt = $pdo->prepare("SELECT shelf_id as id, shelf_name as name FROM shelf WHERE shelf_id = ?");
            break;
            
        case 'genre':
            $stmt = $pdo->prepare("SELECT genre_id as id, genre_name as name FROM genre WHERE genre_id = ?");
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
    }
    
    // Execute the statement
    $stmt->execute([$id]);
    
    // Get the result
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        echo json_encode([
            'success' => true, 
            'item' => $item
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => ucfirst($type) . ' not found'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}