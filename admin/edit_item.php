<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';

// Validate input
if ($id <= 0 || empty($type) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    switch ($type) {
        case 'category':
            // check if the item exists
            $checkStmt = $pdo->prepare("SELECT category_id FROM category WHERE category_id = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Category not found']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE category SET category_name = ? WHERE category_id = ?");
            break;
            
        case 'shelf':
           
            $checkStmt = $pdo->prepare("SELECT shelf_id FROM shelf WHERE shelf_id = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Shelf not found']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE shelf SET shelf_name = ? WHERE shelf_id = ?");
            break;
            
        case 'genre':
        
            $checkStmt = $pdo->prepare("SELECT genre_id FROM genre WHERE genre_id = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Genre not found']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE genre SET genre_name = ? WHERE genre_id = ?");
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
    }
    
    // Execute the update statement
    $stmt->execute([$name, $id]);
    
    echo json_encode([
        'success' => true, 
        'message' => ucfirst($type) . ' updated successfully',
        'id' => $id,
        'name' => $name,
        'type' => $type
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}