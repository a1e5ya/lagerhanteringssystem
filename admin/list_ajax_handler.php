<?php
/**
 * AJAX Handler for Lists - Secured Version
 * 
 * Processes AJAX requests from lists.php with enhanced security features.
 * Handles batch operations with support for "select all pages" functionality.
 * 
 * Security Features:
 * - Comprehensive input validation and sanitization
 * - CSRF protection for all POST requests
 * - Rate limiting protection against abuse
 * - Authentication and authorization checks
 * - SQL injection protection with prepared statements
 * - File system security for delete operations
 * - Comprehensive error handling
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    2.0
 * @since      2024-01-01
 */

require_once '../init.php';

/**
 * Validate authentication and authorization
 * Requires editor role (2) or higher
 */
try {
    checkAuth(2);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Autentisering krävs'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Validate AJAX request
 */
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Endast AJAX-förfrågningar tillåtna'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Validate request method
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Endast POST-förfrågningar tillåtna'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Validate CSRF token for POST requests
 */
try {
    checkCSRFToken();
} catch (Exception $e) {
    http_response_code(419);
    echo json_encode([
        'success' => false, 
        'message' => 'Säkerhetstoken ogiltigt eller saknas'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Rate limiting check using centralized security function
 */
if (!checkRateLimit('batch_operations', 100, 300)) {
    http_response_code(429);
    echo json_encode([
        'success' => false, 
        'message' => 'För många förfrågningar. Försök igen senare.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

// Sanitize and validate action parameter
$action = sanitizeInput($_POST['action'] ?? '', 'string', 50);

if (empty($action)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Ingen åtgärd specificerad'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate action against whitelist
$allowedActions = ['batch_action'];
if (!in_array($action, $allowedActions)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Ogiltig åtgärd'
    ], JSON_UNESCAPED_UNICODE);
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
    
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Ett databasfel inträffade. Kontakta administratören.'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Ett oväntat fel inträffade. Försök igen senare.'
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Handle batch actions with enhanced security and validation
 * 
 * Processes batch operations on multiple products with comprehensive
 * validation, security checks, and proper error handling.
 * 
 * @return void
 * @throws InvalidArgumentException If invalid parameters provided
 * @throws PDOException If database error occurs
 */
function handleBatchAction() {
    global $pdo;
    
    // Sanitize and validate batch action parameter
    $batchAction = sanitizeInput($_POST['batch_action'] ?? '', 'string', 50);
    
    // Validate batch action against whitelist
    $allowedBatchActions = [
        'update_status', 'update_price', 'move_shelf', 
        'set_special_price', 'set_rare', 'set_recommended', 'delete'
    ];
    
    if (!in_array($batchAction, $allowedBatchActions)) {
        throw new InvalidArgumentException("Ogiltig batch-åtgärd: " . $batchAction);
    }
    
    // Check if this is a "select all with filters" operation
    $selectAllWithFilters = isset($_POST['select_all_with_filters']) && 
                           $_POST['select_all_with_filters'] === 'true';
    
    $productIds = [];
    
    if ($selectAllWithFilters) {
        // Get all product IDs that match the current filters
        $filtersJson = sanitizeInput($_POST['filters'] ?? '{}', 'json', 10000);
        $filters = json_decode($filtersJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Ogiltiga filter-data");
        }
        
        $productIds = getProductIdsWithFilters($filters);
    } else {
        // Get product IDs from JSON string (normal selection)
        $productIdsJson = sanitizeInput($_POST['product_ids'] ?? '[]', 'json', 50000);
        $productIds = json_decode($productIdsJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Ogiltiga produkt-ID:n");
        }
    }
    
    // Validate product IDs array
    if (!is_array($productIds) || empty($productIds)) {
        throw new InvalidArgumentException("Inga produkter valda");
    }
    
    // Limit number of products that can be processed at once
    if (count($productIds) > 10000) {
        throw new InvalidArgumentException("För många produkter valda (max 10000)");
    }
    
    // Sanitize and validate that all product IDs are positive integers
    $productIds = array_map(function($id) {
        $sanitized = sanitizeInput($id, 'int');
        return $sanitized > 0 ? $sanitized : null;
    }, $productIds);
    
    $productIds = array_filter($productIds, function($id) { 
        return $id !== null; 
    });
    
    if (empty($productIds)) {
        throw new InvalidArgumentException("Inga giltiga produkt-ID:n");
    }
    
    // Validate client-side security token if present
    if (isset($_POST['client_validation'])) {
        $clientValidation = base64_decode($_POST['client_validation']);
        $expectedValidation = $batchAction . '_' . count($productIds);
        
        if ($clientValidation !== $expectedValidation) {
            throw new InvalidArgumentException("Klientvalidering misslyckades");
        }
    }
    
    // Check timestamp to prevent replay attacks
    if (isset($_POST['timestamp'])) {
        $timestamp = sanitizeInput($_POST['timestamp'], 'int');
        $currentTime = time() * 1000; // Convert to milliseconds
        $timeDiff = abs($currentTime - $timestamp);
        
        // Allow requests within 10 minutes
        if ($timeDiff > 600000) {
            throw new InvalidArgumentException("Förfrågan för gammal eller från framtiden");
        }
    }
    
    // Perform batch operation and get result
    $result = batchOperations($productIds, $batchAction, $_POST);
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

/**
 * Get product IDs that match the current filters for "select all pages" functionality
 * 
 * Retrieves product IDs based on the same filter criteria used in the main
 * product listing to support bulk operations across all filtered results.
 * 
 * @param array $filters Current filter parameters
 * @return array Product IDs matching the filters
 * @throws InvalidArgumentException If invalid filter data provided
 * @throws PDOException If database error occurs
 */
function getProductIdsWithFilters($filters) {
    global $pdo;
    
    // Validate filters array
    if (!is_array($filters)) {
        throw new InvalidArgumentException("Filters måste vara en array");
    }
    
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
    
    // Search condition with length validation
    if (!empty($filters['search'])) {
        $search = sanitizeInput($filters['search'], 'string', 255);
        
        $whereConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
        $searchParam = '%' . $search . '%';
        for ($i = 0; $i < 7; $i++) {
            $params[] = $searchParam;
        }
    }
    
    // Category condition
    if (!empty($filters['category'])) {
        $category = sanitizeInput($filters['category'], 'int');
        if ($category > 0) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $category;
        }
    }
    
    // Genre condition
    if (!empty($filters['genre'])) {
        $genre = sanitizeInput($filters['genre'], 'string', 100);
        $whereConditions[] = "g.genre_sv_name = ?";
        $params[] = $genre;
    }
    
    // Condition filter
    if (!empty($filters['condition'])) {
        $condition = sanitizeInput($filters['condition'], 'string', 100);
        $whereConditions[] = "con.condition_sv_name = ?";
        $params[] = $condition;
    }
    
    // Shelf filter
    if (!empty($filters['shelf'])) {
        $shelf = sanitizeInput($filters['shelf'], 'string', 100);
        $whereConditions[] = "sh.shelf_sv_name = ?";
        $params[] = $shelf;
    }
    
    // Price range filters
    if (!empty($filters['price_min'])) {
        $priceMin = sanitizeInput($filters['price_min'], 'float');
        if ($priceMin >= 0 && $priceMin <= 999999.99) {
            $whereConditions[] = "p.price >= ?";
            $params[] = $priceMin;
        }
    }
    
    if (!empty($filters['price_max'])) {
        $priceMax = sanitizeInput($filters['price_max'], 'float');
        if ($priceMax >= 0 && $priceMax <= 999999.99) {
            $whereConditions[] = "p.price <= ?";
            $params[] = $priceMax;
        }
    }
    
    // Date range filters
    if (!empty($filters['date_min'])) {
        $dateMin = sanitizeInput($filters['date_min'], 'string', 20);
        if (validateDate($dateMin, 'Y-m-d')) {
            $whereConditions[] = "DATE(p.date_added) >= ?";
            $params[] = $dateMin;
        }
    }
    
    if (!empty($filters['date_max'])) {
        $dateMax = sanitizeInput($filters['date_max'], 'string', 20);
        if (validateDate($dateMax, 'Y-m-d')) {
            $whereConditions[] = "DATE(p.date_added) <= ?";
            $params[] = $dateMax;
        }
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
            $status = sanitizeInput($filters['status'], 'string', 50);
            $whereConditions[] = "s.status_sv_name = ?";
            $params[] = $status;
        }
    }
    
    // Special marking filters
    if (!empty($filters['special_price'])) {
        $specialPrice = sanitizeInput($filters['special_price'], 'int');
        if ($specialPrice > 0) {
            $whereConditions[] = "p.special_price = 1";
        }
    }
    
    if (!empty($filters['rare'])) {
        $rare = sanitizeInput($filters['rare'], 'int');
        if ($rare > 0) {
            $whereConditions[] = "p.rare = 1";
        }
    }
    
    if (!empty($filters['recommended'])) {
        $recommended = sanitizeInput($filters['recommended'], 'int');
        if ($recommended > 0) {
            $whereConditions[] = "p.recommended = 1";
        }
    }
    
    // Special condition filters
    if (!empty($filters['no_price']) && $filters['no_price']) {
        $whereConditions[] = "(p.price IS NULL OR p.price = 0)";
    }
    
    if (!empty($filters['poor_condition']) && $filters['poor_condition']) {
        $whereConditions[] = "p.condition_id = 4"; // Assuming 4 is the poorest condition
    }
    
    if (!empty($filters['year_threshold'])) {
        $yearThreshold = sanitizeInput($filters['year_threshold'], 'int');
        $currentYear = (int)date('Y');
        if ($yearThreshold >= 1800 && $yearThreshold <= $currentYear + 10) {
            $whereConditions[] = "p.year <= ?";
            $params[] = $yearThreshold;
        }
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add LIMIT to prevent excessive results
    $sql .= " LIMIT 10000";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters with appropriate types
        foreach ($params as $index => $param) {
            if (is_int($param)) {
                $stmt->bindValue($index + 1, $param, PDO::PARAM_INT);
            } elseif (is_float($param)) {
                $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
            } else {
                $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
            }
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return array_map('intval', $results);
    } catch (PDOException $e) {
        throw new PDOException("Fel vid hämtning av produkt-ID:n");
    }
}

/**
 * Performs batch operations on multiple products with enhanced security
 * 
 * Executes the specified batch operation on the provided product IDs
 * with comprehensive validation, transaction support, and logging.
 * 
 * @param array $productIds Product IDs to operate on
 * @param string $operation Operation type
 * @param array $params Additional parameters for the operation
 * @return array Result with success flag and message
 * @throws InvalidArgumentException If invalid parameters provided
 * @throws PDOException If database error occurs
 * @throws Exception If operation fails
 */
function batchOperations($productIds, $operation, $params = []) {
    global $pdo;

    if (empty($productIds)) {
        throw new InvalidArgumentException("Inga produkter valda");
    }
    
    // Ensure UPLOAD_PATH is defined for delete operations
    if ($operation === 'delete' && !defined('UPLOAD_PATH')) {
        throw new Exception("Serverkonfigurationsfel: UPLOAD_PATH är inte definierad");
    }

    // Start database transaction
    try {
        $pdo->beginTransaction();
        
        // Verify all products exist and user has permission to modify them
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE prod_id IN ($placeholders)");
        
        foreach ($productIds as $index => $id) {
            $checkStmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        
        $checkStmt->execute();
        $existingCount = (int)$checkStmt->fetchColumn();
        
        if ($existingCount != count($productIds)) {
            throw new InvalidArgumentException("Vissa produkter kunde inte hittas");
        }
        
        $message = '';
        
        // Execute the appropriate operation
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
        $eventDescSuffix = ($operation === 'delete' && count($productIds) < 10) ? 
            ' IDs: ' . implode(', ', $productIds) : '';
        $eventDescription = 'Batch operation: ' . $message . $eventDescSuffix;
        
        $logStmt->execute([$userId, $eventType, $eventDescription]);
        
        $pdo->commit();
        return ['success' => true, 'message' => $message];
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw new PDOException("Ett databasfel inträffade vid batch-operationen");
        
    } catch (Exception $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Handle status update operation
 * 
 * @param array $productIds Product IDs to update
 * @param array $params Parameters including new_status
 * @return string Success message
 * @throws InvalidArgumentException If invalid status provided
 * @throws PDOException If database error occurs
 */
function handleUpdateStatus($productIds, $params) {
    global $pdo;
    
    $newStatus = sanitizeInput($params['new_status'] ?? 0, 'int');
    if ($newStatus <= 0 || $newStatus > 10) {
        throw new InvalidArgumentException("Ogiltig status");
    }
    
    // Verify status exists in database
    $statusCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM status WHERE status_id = ?");
    $statusCheckStmt->bindValue(1, $newStatus, PDO::PARAM_INT);
    $statusCheckStmt->execute();
    
    if ($statusCheckStmt->fetchColumn() == 0) {
        throw new InvalidArgumentException("Status finns inte");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET status = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $newStatus, PDO::PARAM_INT);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return count($productIds) . ' produkter har fått ny status';
}

/**
 * Handle price update operation
 * 
 * @param array $productIds Product IDs to update
 * @param array $params Parameters including new_price
 * @return string Success message
 * @throws InvalidArgumentException If invalid price provided
 */
function handleUpdatePrice($productIds, $params) {
    global $pdo;
    
    $newPrice = sanitizeInput($params['new_price'] ?? 0, 'float');
    if ($newPrice <= 0 || $newPrice > 999999.99) {
        throw new InvalidArgumentException("Ogiltigt pris (måste vara mellan 0.01 och 999999.99)");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET price = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $newPrice, PDO::PARAM_STR);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return count($productIds) . ' produkter uppdaterade med nytt pris';
}

/**
 * Handle shelf move operation
 * 
 * @param array $productIds Product IDs to move
 * @param array $params Parameters including new_shelf
 * @return string Success message
 * @throws InvalidArgumentException If invalid shelf provided
 * @throws PDOException If database error occurs
 */
function handleMoveShelf($productIds, $params) {
    global $pdo;
    
    $newShelfId = sanitizeInput($params['new_shelf'] ?? 0, 'int');
    if ($newShelfId <= 0) {
        throw new InvalidArgumentException("Ogiltig hylla");
    }
    
    // Verify shelf exists in database
    $shelfCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM shelf WHERE shelf_id = ?");
    $shelfCheckStmt->bindValue(1, $newShelfId, PDO::PARAM_INT);
    $shelfCheckStmt->execute();
    
    if ($shelfCheckStmt->fetchColumn() == 0) {
        throw new InvalidArgumentException("Hyllan finns inte");
    }
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET shelf_id = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $newShelfId, PDO::PARAM_INT);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return count($productIds) . ' produkter flyttade till ny hylla';
}

/**
 * Handle special price setting operation
 * 
 * @param array $productIds Product IDs to update
 * @param array $params Parameters including special_price_value
 * @return string Success message
 */
function handleSetSpecialPrice($productIds, $params) {
    global $pdo;
    
    $specialPriceValue = sanitizeInput($params['special_price_value'] ?? 0, 'int');
    $specialPriceValue = $specialPriceValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET special_price = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $specialPriceValue, PDO::PARAM_INT);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $statusText = $specialPriceValue ? 'markerade som rea' : 'tog bort rea-markeringen från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle rare setting operation
 * 
 * @param array $productIds Product IDs to update
 * @param array $params Parameters including rare_value
 * @return string Success message
 */
function handleSetRare($productIds, $params) {
    global $pdo;
    
    $rareValue = sanitizeInput($params['rare_value'] ?? 0, 'int');
    $rareValue = $rareValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET rare = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $rareValue, PDO::PARAM_INT);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $statusText = $rareValue ? 'markerade som sällsynta' : 'tog bort sällsynt-markeringen från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle recommended setting operation
 * 
 * @param array $productIds Product IDs to update
 * @param array $params Parameters including recommended_value
 * @return string Success message
 */
function handleSetRecommended($productIds, $params) {
    global $pdo;
    
    $recommendedValue = sanitizeInput($params['recommended_value'] ?? 0, 'int');
    $recommendedValue = $recommendedValue ? 1 : 0; // Normalize to 0 or 1
    
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("UPDATE product SET recommended = ? WHERE prod_id IN ($placeholders)");
    
    $stmt->bindValue(1, $recommendedValue, PDO::PARAM_INT);
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $statusText = $recommendedValue ? 'markerade som rekommenderade' : 'tog bort rekommendation från';
    return count($productIds) . ' produkter ' . $statusText;
}

/**
 * Handle delete operation with enhanced file cleanup and security
 * 
 * Deletes products and their associated files with comprehensive
 * security checks and proper cleanup of database relationships.
 * 
 * @param array $productIds Product IDs to delete
 * @return string Success message
 * @throws Exception If UPLOAD_PATH not defined or other errors occur
 */
function handleDelete($productIds) {
    global $pdo;
    
    $deletedImagesCount = 0;
    $imageErrors = [];
    
    foreach ($productIds as $productId) {
        // Fetch image paths for this product
        $imageSql = "SELECT image_id, image_path FROM image WHERE prod_id = ?";
        $imageStmt = $pdo->prepare($imageSql);
        $imageStmt->bindValue(1, $productId, PDO::PARAM_INT);
        $imageStmt->execute();
        $imagesToDelete = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

        // Delete actual image files with security checks
        foreach ($imagesToDelete as $img) {
            if (!empty($img['image_path'])) {
                // Enhanced file path resolution with security checks
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
                        // Critical security check: ensure file is within allowed directory
                        $realPath = realpath($filePath);
                        $uploadPath = realpath(UPLOAD_PATH);
                        
                        if ($realPath && $uploadPath && strpos($realPath, $uploadPath) === 0) {
                            if (@unlink($filePath)) {
                                $deletedImagesCount++;
                                $fileDeleted = true;
                                break;
                            } else {
                                $imageErrors[] = "Failed to delete file: " . basename($filePath);
                            }
                        }
                    }
                }
                
                if (!$fileDeleted) {
                    $imageErrors[] = "Image file not found: " . basename($img['image_path']);
                }
            }
        }

        // Delete database records in correct order to maintain referential integrity
        $deleteQueries = [
            "DELETE FROM product_author WHERE product_id = ?",
            "DELETE FROM product_genre WHERE product_id = ?", 
            "DELETE FROM image WHERE prod_id = ?",
            "UPDATE event_log SET product_id = NULL WHERE product_id = ?"
        ];
        
        foreach ($deleteQueries as $query) {
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(1, $productId, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
    // Delete the products themselves
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("DELETE FROM product WHERE prod_id IN ($placeholders)");
    
    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    $message = count($productIds) . ' produkter har tagits bort';
    if ($deletedImagesCount > 0) {
        $message .= '. ' . $deletedImagesCount . ' bildfiler raderade';
    }
    
    // Note: Image errors are not included in user message for security reasons
    
    return $message;
}
?>