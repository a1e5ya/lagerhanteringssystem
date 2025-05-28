<?php
/**
 * Delete Image Endpoint
 * 
 * AJAX endpoint for deleting product images
 */

require_once '../init.php';

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Ensure JSON response
header('Content-Type: application/json');

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['image_id'])) {
        throw new Exception('Invalid request data');
    }
    
    $imageId = (int)$data['image_id'];
    
    if ($imageId <= 0) {
        throw new Exception('Invalid image ID');
    }
    
    // Initialize ImageProcessor
    global $app_config;
    $imageProcessor = new ImageProcessor($pdo, $app_config['uploads']);
    
    // Delete the image
    $result = $imageProcessor->deleteProductImage($imageId);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>