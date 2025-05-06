<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';

if ($id <= 0 || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    switch ($type) {
        case 'category':
        case 'shelf':
        case 'genre':
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            if ($name === '') {
                echo json_encode(['success' => false, 'message' => 'Name cannot be empty']);
                exit;
            }

            // Check if the item exists
            $tableMap = [
                'category' => ['category_id', 'category_name'],
                'shelf' => ['shelf_id', 'shelf_name'],
                'genre' => ['genre_id', 'genre_name'],
            ];
            [$idField, $nameField] = $tableMap[$type];

            $checkStmt = $pdo->prepare("SELECT $idField FROM $type WHERE $idField = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => ucfirst($type) . ' not found']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE $type SET $nameField = ? WHERE $idField = ?");
            $stmt->execute([$name, $id]);

            echo json_encode([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully',
                'id' => $id,
                'name' => $name,
                'type' => $type
            ]);
            break;

        case 'author':
            $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
            $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';

            if ($first_name === '' && $last_name === '') {
                echo json_encode(['success' => false, 'message' => 'Förnamn eller efternamn krävs']);
                exit;
            }

            $checkStmt = $pdo->prepare("SELECT * FROM author WHERE author_id = ?");
            $checkStmt->execute([$id]);
            if ($checkStmt->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => 'Författaren hittades inte']);
                exit;
            }

            $current = $checkStmt->fetch();
            if ($first_name === '') $first_name = $current['first_name'];
            if ($last_name === '') $last_name = $current['last_name'];

            $stmt = $pdo->prepare("UPDATE author SET first_name = ?, last_name = ? WHERE author_id = ?");
            $stmt->execute([$first_name, $last_name, $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Författare uppdaterad',
                'id' => $id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'type' => $type
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
