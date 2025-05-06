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
    case 'authorFirst':
        $stmt = $pdo->prepare("SELECT DISTINCT first_name FROM author WHERE first_name LIKE ? ORDER BY first_name LIMIT 10");
        break;
    case 'authorLast':
        $stmt = $pdo->prepare("SELECT DISTINCT last_name FROM author WHERE last_name LIKE ? ORDER BY last_name LIMIT 10");
        break;
    case 'publisher':
        $stmt = $pdo->prepare("SELECT DISTINCT publisher FROM product WHERE publisher LIKE ? ORDER BY publisher LIMIT 10");
        break;
    // Add more cases here if you want to expand
    default:
        http_response_code(400);
        exit;
}

$stmt->execute(["{$query}%"]);
$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

header('Content-Type: application/json');
echo json_encode($results);
?>