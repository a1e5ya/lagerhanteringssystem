<?php
// error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../init.php';

header('Content-Type: application/json');

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get parameters
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Validate input
if ($id <= 0 || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Prepare the SQL statement based on type
    switch ($type) {
        case 'category':
            // Check if the category is used in any products
            $check = $pdo->prepare("SELECT COUNT(*) FROM product WHERE category_id = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete this category because it is being used by one or more products']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = ?");
            break;

        case 'shelf':
            // Check if the shelf is used in any products
            $check = $pdo->prepare("SELECT COUNT(*) FROM product WHERE shelf_id = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete this shelf because it is being used by one or more products']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM shelf WHERE shelf_id = ?");
            break;

        case 'genre':
            // Check if the genre is used in any products
            $check = $pdo->prepare("SELECT COUNT(*) FROM product_genre WHERE genre_id = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete this genre because it is being used by one or more products']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM genre WHERE genre_id = ?");
            break;

        case 'author':
             $check = $pdo->prepare("SELECT COUNT(*) FROM product_author WHERE author_id = ?");
             $check->execute([$id]);
             if ($check->fetchColumn() > 0) {
                 echo json_encode(['success' => false, 'message' => 'Cannot delete this author because they are used in one or more products']);
                 exit;
             }

            $stmt = $pdo->prepare("DELETE FROM author WHERE author_id = ?");
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
    }

    $stmt->execute([$id]);

    // Always return success if no exception occurred
    echo json_encode(['success' => true, 'message' => ucfirst($type) . ' deleted successfully']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}