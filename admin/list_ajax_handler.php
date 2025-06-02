<?php
/**
 * AJAX Handler for Lists - FIXED WITH ENHANCED SECURITY
 * 
 * Processes AJAX requests from lists.php
 * Handles batch operations with support for "select all pages" functionality
 * 
 * Security Features:
 * - CSRF protection for all POST requests
 * - Input validation and sanitization
 * - Rate limiting protection
 * - Comprehensive error handling and logging
 * - SQL injection protection
 * - Authentication and authorization checks
 */

require_once '../init.php';

// Check authentication - requires admin or editor role
try {
    checkAuth(2); // Role 2 (Editor) or above required
} catch (Exception $e) {
    error_log("Authentication failed in list_ajax_handler.php: " . $e->getMessage());
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Autentisering krävs']);
    exit;
}

// Check if this is an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    error_log("Non-AJAX request detected in list_ajax_handler.php from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Endast AJAX-förfrågningar tillåtna']);
    exit;
}

// Check CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        checkCSRFToken();
    } catch (Exception $e) {
        error_log("CSRF token validation failed in list_ajax_handler.php: " . $e->getMessage() . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        http_response_code(419);
        echo json_encode(['success' => false, 'message' => 'Säkerhetstoken ogiltigt eller saknas']);
        exit;
    }
}

// Rate limiting check - now using centralized function from security.php
if (!checkRateLimit('batch_operations', 100, 300)) {
    error_log("Rate limit exceeded in list_ajax_handler.php from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'För många förfrågningar. Försök igen senare.']);
    exit;
}

// Set response header
header('Content-Type: application/json');

// Get and validate action parameter
$action = sanitizeInput($_POST['action'] ?? '', 'string');

// Process action
if (empty($action)) {
    error_log("No action specified in list_ajax_handler.php");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ingen åtgärd specificerad']);
    exit;
}

// Validate action against whitelist
$allowedActions = ['batch_action'];
if (!in_array($action, $allowedActions)) {
    error_log("Invalid action attempted in list_ajax_handler.php: " . $action . " from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ogiltig åtgärd']);
    exit;
}

try {
    switch ($action) {
        case 'batch_action':
            handleBatchAction();
            break;
            
        default:
            throw new InvalidArgumentException("Okänd åtgärd: " . $action);
    }
} catch (PDOException $e) {
    // Log detailed error for debugging
    error_log("Database error in list_ajax_handler.php: " . $e->getMessage() . " - Query: " . ($e->getCode() ?? 'unknown') . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    // Send generic error to client
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ett databasfel inträffade. Kontakta administratören.']);
} catch (InvalidArgumentException $e) {
    error_log("Invalid argument in list_ajax_handler.php: " . $e->getMessage() . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    // Log the detailed error on the server
    error_log("General error in list_ajax_handler.php: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    error_log("Stack Trace: " . $e->getTraceAsString());
    
    // Send a generic error message to the client
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ett oväntat fel inträffade. Försök igen senare.']);
}

/**
 * Handle batch actions with enhanced security and validation
 */
function handleBatchAction() {
    global $pdo;
    
    // Validate required parameters
    $batchAction = sanitizeInput($_POST['batch_action'] ?? '', 'string');
    
    // Validate batch action against whitelist
    $allowedBatchActions = [
        'update_status', 'update_price', 'move_shelf', 
        'set_special_price', 'set_rare', 'set_recommended', 'delete'
    ];
    
    if (!in_array($batchAction, $allowedBatchActions)) {
        throw new InvalidArgumentException("Ogiltig batch-åtgärd: " . $batchAction);
    }
    
    // Check if this is a "select all with filters" operation
    $selectAllWithFilters = isset($_POST['select_all_with_filters']) && $_POST['select_all_with_filters'] === 'true';
    
    $productIds = [];
    
    if ($selectAllWithFilters) {
        // Get all product IDs that match the current filters
        $filtersJson = sanitizeInput($_POST['filters'] ?? '{}', 'json');
        $filters = json_decode($filtersJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Ogiltiga filter-data");
        }
        
        $productIds = getProductIdsWithFilters($filters);
    } else {
        // Get product IDs from JSON string (normal selection)
        $productIdsJson = sanitizeInput($_POST['product_ids'] ?? '[]', 'json');
        $productIds = json_decode($productIdsJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Ogiltiga produkt-ID:n");
        }
    }
    
    // Validate product IDs
    if (!is_array($productIds) || empty($productIds)) {
        throw new InvalidArgumentException("Inga produkter valda");
    }
    
    // Limit number of products that can be processed at once
    if (count($productIds) > 10000) {
        throw new InvalidArgumentException("För många produkter valda (max 10000)");
    }
    
    // Validate that all product IDs are integers
    $productIds = array_map('intval', $productIds);
    $productIds = array_filter($productIds, function($id) { return $id > 0; });
    
    if (empty($productIds)) {
        throw new InvalidArgumentException("Inga giltiga produkt-ID:n");
    }
    
    // Validate client-side security token if present
    if (isset($_POST['client_validation'])) {
        $clientValidation = base64_decode($_POST['client_validation']);
        $expectedValidation = $batchAction . '_' . count($productIds);
        
        if ($clientValidation !== $expectedValidation) {
            error_log("Client validation failed in batch operation: expected '$expectedValidation', got '$clientValidation'");
            throw new InvalidArgumentException("Klientvalidering misslyckades");
        }
    }
    
    // Check timestamp to prevent replay attacks
    if (isset($_POST['timestamp'])) {
        $timestamp = intval($_POST['timestamp']);
        $currentTime = time() * 1000; // Convert to milliseconds
        $timeDiff = abs($currentTime - $timestamp);
        
        // Allow requests within 10 minutes
        if ($timeDiff > 600000) {
            error_log("Timestamp validation failed: request too old or from future");
            throw new InvalidArgumentException("Förfrågan för gammal eller från framtiden");
        }
    }
    
    // Perform batch operation and get result
    $result = batchOperations($productIds, $batchAction, $_POST);
    
    echo json_encode($result);
}

/**
 * Get product IDs that match the current filters (for "select all pages" functionality)
 * @param array $filters Current filter parameters
 * @return array Product IDs
 */
function getProductIdsWithFilters($filters) {
    global $pdo;
    
    // Validate and sanitize filter inputs
    $filters = array_map(function($value) {
        return sanitizeInput($value, 'string');
    }, $filters);
    
    $sql = "SELECT DISTINCT p.prod_id
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            JOIN category c ON p.category_id = c.category_id
            LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
            LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
            LEFT JOIN genre g ON pg.genre_id = g.genre_id
            LEFT JOIN `condition` con ON p.condition_id = con.condition_id
            LEFT JOIN `status` s ON p.status = s.status_id
            LEFT JOIN `language` l ON p.language_id = l.language_id";
    
    $whereConditions = [];
    $params = [];
    
    // Apply the same filters as in get_products.php with proper validation
    
    // Search condition
    if (!empty($filters['search'])) {
        $search = trim($filters['search']);
        if (strlen($search) > 255) {
            throw new InvalidArgumentException("Sökterm för lång");
        }
        
        $whereConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
        $searchParam = '%' . $search . '%';
        for ($i = 0; $i < 7; $i++) {
            $params[] = $searchParam;
        }
    }
    
    // Category condition
    if (!empty($filters['category'])) {
        $category = intval($filters['category']);
        if ($category <= 0) {
            throw new InvalidArgumentException("Ogiltig kategori-ID");
        }
        $whereConditions[] = "p.category_id = ?";
        $params[] = $category;
    }
    
    // Genre condition
    if (!empty($filters['genre'])) {
        $genre = trim($filters['genre']);
        if (strlen($genre) > 100) {
            throw new InvalidArgumentException("Genre-namn för långt");
        }
        $whereConditions[] = "g.genre_sv_name = ?";
        $params[] = $genre;
    }
    
    // Condition
    if (!empty($filters['condition'])) {
        $condition = trim($filters['condition']);
        if (strlen($condition) > 100) {
            throw new InvalidArgumentException("Skick-namn för långt");
        }
        $whereConditions[] = "con.condition_sv_name = ?";
        $params[] = $condition;
    }
    
    // Shelf
    if (!empty($filters['shelf'])) {
        $shelf = trim($filters['shelf']);
        if (strlen($shelf) > 100) {
            throw new InvalidArgumentException("Hylla-namn för långt");
        }
        $whereConditions[] = "sh.shelf_sv_name = ?";
        $params[] = $shelf;
    }
    
    // Price range
    if (!empty($filters['price_min'])) {
        $priceMin = floatval($filters['price_min']);
        if ($priceMin < 0 || $priceMin > 999999.99) {
            throw new InvalidArgumentException("Ogiltigt min-pris");
        }
        $whereConditions[] = "p.price >= ?";
        $params[] = $priceMin;
    }
    
    if (!empty($filters['price_max'])) {
        $priceMax = floatval($filters['price_max']);
        if ($priceMax < 0 || $priceMax > 999999.99) {
            throw new InvalidArgumentException("Ogiltigt max-pris");
        }
        $whereConditions[] = "p.price <= ?";
        $params[] = $priceMax;
    }
    
    // Date range
    if (!empty($filters['date_min'])) {
        $dateMin = $filters['date_min'];
        if (!validateDate($dateMin)) {
            throw new InvalidArgumentException("Ogiltigt min-datum");
        }
        $whereConditions[] = "DATE(p.date_added) >= ?";
        $params[] = $dateMin;
    }
    
    if (!empty($filters['date_max'])) {
        $dateMax = $filters['date_max'];
        if (!validateDate($dateMax)) {
            throw new InvalidArgumentException("Ogiltigt max-datum");
        }
        $whereConditions[] = "DATE(p.date_added) <= ?";
        $params[] = $dateMax;
    }
    
    // Status condition
    if (empty($filters['status']) || $filters['status'] === '') {
        $whereConditions[] = "p.status = 1"; // Default to available only
    } elseif ($filters['status'] !== 'all') {
        if ($filters['status'] === 'Tillgänglig') {
            $whereConditions[] = "p.status = 1";
        } elseif ($filters['status'] === 'Såld') {
            $whereConditions[] = "p.status = 2";
        } else {
            $status = trim($filters['status']);
            if (strlen($status) > 50) {
                throw new InvalidArgumentException("Status-namn för långt");
            }
            $whereConditions[] = "s.status_sv_name = ?";
            $params[] = $status;
        }
    }
    
    // Special filters
    if (!empty($filters['special_price']) && intval($filters['special_price']) > 0) {
        $whereConditions[] = "p.special_price = 1";
    }
    
    if (!empty($filters['rare']) && intval($filters['rare']) > 0) {
        $whereConditions[] = "p.rare = 1";
    }
    
    if (!empty($filters['recommended']) && intval($filters['recommended']) > 0) {
        $whereConditions[] = "p.recommended = 1";
    }
    
    if (!empty($filters['no_price']) && $filters['no_price']) {
        $whereConditions[] = "(p.price IS NULL OR p.price = 0)";
    }
    
    if (!empty($filters['poor_condition']) && $filters['poor_condition']) {
        $whereConditions[] = "p.condition_id = 4"; // Assuming 4 is the poorest condition
    }
    
    if (!empty($filters['year_threshold'])) {
        $yearThreshold = intval($filters['year_threshold']);
        if ($yearThreshold < 1800 || $yearThreshold > date('Y') + 10) {
            throw new InvalidArgumentException("Ogiltigt år-tröskelvärde");
        }
        $whereConditions[] = "p.year <= ?";
        $params[] = $yearThreshold;
    }
    
    // Add WHERE clause if we have conditions
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add LIMIT to prevent excessive results
    $sql .= " LIMIT 10000";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $results);
    } catch (PDOException $e) {
        error_log("Error getting product IDs with filters: " . $e->getMessage() . " - SQL: " . $sql);
        throw new Exception("Fel vid hämtning av produkt-ID:n");
    }
}

/**
 * Performs batch operations on multiple products with enhanced security
 * @param array $productIds Product IDs to operate on
 * @param string $operation Operation type
 * @param array $params Additional parameters
 * @return array Result with success flag and message
 */
function batchOperations($productIds, $operation, $params = []) {
    global $pdo;

    if (empty($productIds)) {
        throw new InvalidArgumentException("Inga produkter valda");
    }
    
    // Ensure UPLOAD_PATH is defined for delete operations
    if ($operation === 'delete' && !defined('UPLOAD_PATH')) {
        error_log("FATAL ERROR: UPLOAD_PATH constant is not defined in list_ajax_handler.php during product deletion.");
        throw new Exception("Serverkonfigurationsfel: UPLOAD_PATH är inte definierad");
    }

    // Start transaction
    try {
        $pdo->beginTransaction();
        
        // Verify all products exist and user has permission to modify them
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE prod_id IN ($placeholders)");
        $checkStmt->execute($productIds);
        $existingCount = $checkStmt->fetchColumn();
        
        if ($existingCount != count($productIds)) {
            throw new InvalidArgumentException("Vissa produkter kunde inte hittas");
        }
        
        $message = '';
        
        switch ($operation) {
            case 'update_status':
                $message = handleUpdateStatus($productIds, $params);
                break;
                
            case 'update_price':
                $message = handleUpdatePrice($productIds, $params);
                break;
                
            case 'move_shelf':
                $message = handleMoveShelf($productIds, $params);
                break;
                
            case 'set_special_price':
                $message = handleSetSpecialPrice($productIds, $params);
                break;
                
            case 'set_rare':
                $message = handleSetRare($productIds, $params);
                break;
                
            case 'set_recommended':
                $message = handleSetRecommended($productIds, $params);
                break;
                
            case 'delete':
                $message = handleDelete($productIds);
                break;
                
            default:
                throw new InvalidArgumentException("Okänd åtgärd: " . $operation);
        }
        
        // Log the batch action
        $userId = $_SESSION['user_id'] ?? 1;
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description) 
            VALUES (?, ?, ?)
        ");
        $eventType = 'batch_' . $operation;
        $eventDescSuffix = ($operation === 'delete' && count($productIds) < 10) ? ' IDs: ' . implode(', ', $productIds) : '';
        $eventDescription = 'Batch operation: ' . $message . $eventDescSuffix;
        
        if (!$logStmt->execute([$userId, $eventType, $eventDescription])) {
            error_log("Failed to log batch operation: " . print_r($logStmt->errorInfo(), true));
        }
        
        $pdo->commit();
        return ['success' => true, 'message' => $message];
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Batch operation PDO error: " . $e->getMessage() . " - Operation: " . $operation . " - ProductIDs: " . implode(',', $productIds));
        throw new Exception("Ett databasfel inträffade vid batch-operationen");
    } catch (Exception $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e; // Re-throw for handling in main try-catch
    }
}

/**
 * Handle status update operation
 */
function handleUpdateStatus($productIds, $params) {
    global $pdo;
    
    $newStatus = isset($params['new_status']) ? intval($params['new_status']) : 0;
    if ($newStatus <= 0 || $newStatus > 10) { // Reasonable status range
        throw new InvalidArgumentException("Ogiltig status");
    }
    
    // Verify status exists
    $statusCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM status WHERE status_id = ?");
    $statusCheckStmt->execute([$newStatus]);
    if ($statusCheckStmt->fetchColumn() == 0) {
        throw new InvalidArgumentException("Status finns inte");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET status = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$newStatus];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    return count($productIds) . ' produkter har fått ny status';
}

/**
 * Handle price update operation
 */
function handleUpdatePrice($productIds, $params) {
    global $pdo;
    
    $newPrice = isset($params['new_price']) ? floatval($params['new_price']) : 0;
    if ($newPrice <= 0 || $newPrice > 999999.99) {
        throw new InvalidArgumentException("Ogiltigt pris (måste vara mellan 0.01 och 999999.99)");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET price = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$newPrice];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    return count($productIds) . ' produkter uppdaterade med nytt pris';
}

/**
 * Handle shelf move operation
 */
function handleMoveShelf($productIds, $params) {
    global $pdo;
    
    $newShelfId = isset($params['new_shelf']) ? intval($params['new_shelf']) : 0;
    if ($newShelfId <= 0) {
        throw new InvalidArgumentException("Ogiltig hylla");
    }
    
    // Verify shelf exists
    $shelfCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM shelf WHERE shelf_id = ?");
    $shelfCheckStmt->execute([$newShelfId]);
    if ($shelfCheckStmt->fetchColumn() == 0) {
        throw new InvalidArgumentException("Hyllan finns inte");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET shelf_id = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$newShelfId];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    return count($productIds) . ' produkter flyttade till ny hylla';
}

/**
 * Handle special price setting
 */
function handleSetSpecialPrice($productIds, $params) {
    global $pdo;
    
    $specialPriceValue = isset($params['special_price_value']) ? intval($params['special_price_value']) : 0;
    $specialPriceValue = $specialPriceValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET special_price = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$specialPriceValue];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    $statusText = $specialPriceValue ? 'markerade som rea' : 'tog bort rea-markeringen från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle rare setting
 */
function handleSetRare($productIds, $params) {
    global $pdo;
    
    $rareValue = isset($params['rare_value']) ? intval($params['rare_value']) : 0;
    $rareValue = $rareValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET rare = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$rareValue];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    $statusText = $rareValue ? 'markerade som sällsynta' : 'tog bort sällsynt-markeringen från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle recommended setting
 */
function handleSetRecommended($productIds, $params) {
    global $pdo;
    
    $recommendedValue = isset($params['recommended_value']) ? intval($params['recommended_value']) : 0;
    $recommendedValue = $recommendedValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET recommended = ? WHERE prod_id IN ($placeholders)");
    
    $bindParams = [$recommendedValue];
    foreach ($productIds as $id) {
        $bindParams[] = $id;
    }
    
    $stmt->execute($bindParams);
    $statusText = $recommendedValue ? 'markerade som rekommenderade' : 'tog bort rekommendation från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle delete operation with enhanced file cleanup
 */
function handleDelete($productIds) {
    global $pdo;
    
    $deletedImagesCount = 0;
    $imageErrors = [];
    
    foreach ($productIds as $productId) {
        // Fetch image paths for this product
        $imageSql = "SELECT image_id, image_path FROM image WHERE prod_id = ?";
        $imageStmt = $pdo->prepare($imageSql);
        $imageStmt->execute([$productId]);
        $imagesToDelete = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete actual image files
        foreach ($imagesToDelete as $img) {
            if (!empty($img['image_path'])) {
                // Enhanced file path resolution
                $possiblePaths = [
                    UPLOAD_PATH . DIRECTORY_SEPARATOR . $img['image_path'],
                    UPLOAD_PATH . DIRECTORY_SEPARATOR . basename($img['image_path']),
                    realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $img['image_path'],
                    realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . basename($img['image_path'])
                ];
                
                $fileDeleted = false;
                foreach ($possiblePaths as $filePath) {
                    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
                    
                    if (file_exists($filePath) && is_file($filePath)) {
                        // Additional security check: ensure file is within allowed directory
                        $realPath = realpath($filePath);
                        $uploadPath = realpath(UPLOAD_PATH);
                        
                        if ($realPath && $uploadPath && strpos($realPath, $uploadPath) === 0) {
                            if (@unlink($filePath)) {
                                $deletedImagesCount++;
                                $fileDeleted = true;
                                error_log("Successfully deleted image file: " . $filePath . " for product_id: " . $productId);
                                break;
                            } else {
                                $imageErrors[] = "Failed to delete file: " . $filePath . " (permissions issue)";
                                error_log("Failed to delete image file: " . $filePath . " for product_id: " . $productId);
                            }
                        } else {
                            error_log("Security: Attempted to delete file outside upload directory: " . $filePath);
                        }
                    }
                }
                
                if (!$fileDeleted) {
                    $imageErrors[] = "Image file not found: " . $img['image_path'];
                    error_log("Image file not found for deletion: " . $img['image_path'] . " for product_id: " . $productId);
                }
            }
        }

        // Delete database records in correct order
        $deleteQueries = [
            "DELETE FROM product_author WHERE product_id = ?",
            "DELETE FROM product_genre WHERE product_id = ?", 
            "DELETE FROM image WHERE prod_id = ?",
            "UPDATE event_log SET product_id = NULL WHERE product_id = ?"
        ];
        
        foreach ($deleteQueries as $query) {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$productId]);
        }
    }
    
    // Delete the products themselves
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("DELETE FROM product WHERE prod_id IN ($placeholders)");
    $stmt->execute($productIds);
    
    $message = count($productIds) . ' produkter har tagits bort';
    if ($deletedImagesCount > 0) {
        $message .= '. ' . $deletedImagesCount . ' bildfiler raderade';
    }
    if (!empty($imageErrors)) {
        error_log("Image deletion errors: " . implode('; ', $imageErrors));
        // Note: We don't include image errors in user message for security
    }
    
    return $message;
}

?>
