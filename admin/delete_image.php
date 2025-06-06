<?php
/**
 * Delete Image Endpoint
 * 
 * AJAX endpoint for deleting product images with comprehensive security validation
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

require_once '../init.php';

// Ensure JSON response
header('Content-Type: application/json');

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Endast POST-förfrågningar tillåtna'
    ]);
    exit;
}

try {
    // Get and validate JSON input
    $input = file_get_contents('php://input');
    
    if (empty($input)) {
        throw new InvalidArgumentException('Ingen data mottagen');
    }
    
    // Validate JSON syntax and decode
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException('Ogiltig JSON-data: ' . json_last_error_msg());
    }
    
    if (!is_array($data)) {
        throw new InvalidArgumentException('Data måste vara ett JSON-objekt');
    }
    
    // Validate CSRF token from JSON data or headers
    $csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!validateCSRFToken($csrfToken)) {
        http_response_code(419);
        echo json_encode([
            'success' => false,
            'message' => 'Säkerhetstoken ogiltigt eller saknas',
            'error_code' => 'CSRF_TOKEN_MISMATCH'
        ]);
        exit;
    }
    
    // Validate and sanitize image ID
    if (!isset($data['image_id'])) {
        throw new InvalidArgumentException('Bild-ID krävs');
    }
    
    $imageId = sanitizeInput($data['image_id'], 'int', null, ['min' => 1]);
    
    if ($imageId <= 0) {
        throw new InvalidArgumentException('Ogiltigt bild-ID');
    }
    
    // Initialize ImageProcessor with error handling
    global $app_config;
    
    if (!isset($app_config['uploads']) || !is_array($app_config['uploads'])) {
        throw new Exception('Uppladdningskonfiguration saknas');
    }
    
    $imageProcessor = new ImageProcessor($pdo, $app_config['uploads']);
    
    // Delete the image
    $result = $imageProcessor->deleteProductImage($imageId);
    
    // Validate result format
    if (!is_array($result) || !isset($result['success'])) {
        throw new Exception('Ogiltigt svar från bildprocessor');
    }
    
    // Log successful deletion for audit trail
    if ($result['success']) {
        error_log("Image deleted successfully: ID {$imageId} by user " . ($_SESSION['user_id'] ?? 'unknown'));
    }
    
    echo json_encode($result);
    
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
        'error_code' => 'VALIDATION_ERROR'
    ]);
} catch (Exception $e) {
    // Log the detailed error for debugging
    error_log('Error in delete_image.php: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ett internt fel inträffade vid borttagning av bilden',
        'error_code' => 'INTERNAL_ERROR'
    ]);
}
?>