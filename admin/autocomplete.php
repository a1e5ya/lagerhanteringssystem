<?php
require_once '../config/config.php';

$type = $_GET['type'] ?? '';
$query = $_GET['query'] ?? '';

if (!$query || !$type) {
    http_response_code(400);
    exit;
}

$query = trim($query);

switch ($type) {
    case 'author':
        $stmt = $pdo->prepare("SELECT DISTINCT author_name FROM author WHERE author_name LIKE ? ORDER BY author_name LIMIT 10");
        break;
    case 'publisher':
        $stmt = $pdo->prepare("SELECT DISTINCT publisher FROM product WHERE publisher LIKE ? ORDER BY publisher LIMIT 10");
        break;
    default:
        http_response_code(400);
        exit;
}

$stmt->execute(["{$query}%"]);
$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

header('Content-Type: application/json');
echo json_encode($results);
?>
