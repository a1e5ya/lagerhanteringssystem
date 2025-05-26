<?php
/**
 * AJAX Handler for Lists - COMPLETE AND FIXED
 * 
 * Processes AJAX requests from lists.php
 * Handles batch operations with support for "select all pages" functionality
 */

require_once '../init.php';

// Check authentication - requires admin or editor role
checkAuth(2); // Role 2 (Editor) or above required

// Set response header
header('Content-Type: application/json');

// Check if this is an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get action parameter
$action = $_POST['action'] ?? '';

// Process action
if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

try {
    switch ($action) {
        case 'batch_action':
            // Validate required parameters
            $batchAction = $_POST['batch_action'] ?? '';
            
            // Check if this is a "select all with filters" operation
            $selectAllWithFilters = isset($_POST['select_all_with_filters']) && $_POST['select_all_with_filters'] === 'true';
            
            $productIds = [];
            
            if ($selectAllWithFilters) {
                // Get all product IDs that match the current filters
                $filtersJson = $_POST['filters'] ?? '{}';
                $filters = json_decode($filtersJson, true);
                $productIds = getProductIdsWithFilters($filters);
            } else {
                // Get product IDs from JSON string (normal selection)
                $productIdsJson = $_POST['product_ids'] ?? '[]';
                $productIds = json_decode($productIdsJson, true);
            }
            
            if (!is_array($productIds) || empty($productIds)) {
                echo json_encode(['success' => false, 'message' => 'Inga produkter valda.']);
                exit;
            }
            
            // Perform batch operation and get result
            $result = batchOperations($productIds, $batchAction, $_POST);
            
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

/**
 * Get product IDs that match the current filters (for "select all pages" functionality)
 * 
 * @param array $filters Current filter parameters
 * @return array Product IDs
 */
function getProductIdsWithFilters($filters) {
    global $pdo;
    
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
    
    // Apply the same filters as in get_products.php
    
    // Search condition
    if (!empty($filters['search'])) {
        $whereConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
        $searchParam = '%' . $filters['search'] . '%';
        for ($i = 0; $i < 7; $i++) {
            $params[] = $searchParam;
        }
    }
    
    // Category condition
    if (!empty($filters['category'])) {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $filters['category'];
    }
    
    // Genre condition
    if (!empty($filters['genre'])) {
        $whereConditions[] = "g.genre_sv_name = ?";
        $params[] = $filters['genre'];
    }
    
    // Condition
    if (!empty($filters['condition'])) {
        $whereConditions[] = "con.condition_sv_name = ?";
        $params[] = $filters['condition'];
    }
    
    // Shelf
    if (!empty($filters['shelf'])) {
        $whereConditions[] = "sh.shelf_sv_name = ?";
        $params[] = $filters['shelf'];
    }
    
    // Price range
    if (!empty($filters['price_min']) && floatval($filters['price_min']) > 0) {
        $whereConditions[] = "p.price >= ?";
        $params[] = floatval($filters['price_min']);
    }
    
    if (!empty($filters['price_max']) && floatval($filters['price_max']) > 0) {
        $whereConditions[] = "p.price <= ?";
        $params[] = floatval($filters['price_max']);
    }
    
    // Date range
    if (!empty($filters['date_min'])) {
        $whereConditions[] = "DATE(p.date_added) >= ?";
        $params[] = $filters['date_min'];
    }
    
    if (!empty($filters['date_max'])) {
        $whereConditions[] = "DATE(p.date_added) <= ?";
        $params[] = $filters['date_max'];
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
            $whereConditions[] = "s.status_sv_name = ?";
            $params[] = $filters['status'];
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
    
    if (!empty($filters['year_threshold']) && intval($filters['year_threshold']) > 0) {
        $whereConditions[] = "p.year <= ?";
        $params[] = intval($filters['year_threshold']);
    }
    
    // Add WHERE clause if we have conditions
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $results);
    } catch (PDOException $e) {
        error_log("Error getting product IDs with filters: " . $e->getMessage());
        return [];
    }
}

/**
 * Performs batch operations on multiple products
 * 
 * @param array $productIds Product IDs to operate on
 * @param string $operation Operation type
 * @param array $params Additional parameters
 * @return array Result with success flag and message
 */
function batchOperations($productIds, $operation, $params = []) {
    global $pdo;

    if (empty($productIds)) {
        return ['success' => false, 'message' => 'Inga produkter valda.'];
    }
    
    try {
        $pdo->beginTransaction();
        
        switch ($operation) {
            case 'update_status':
                // Update status to the specified value
                $newStatus = isset($params['new_status']) ? intval($params['new_status']) : 0;
                if ($newStatus <= 0) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Ogiltig status.'];
                }
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET status = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$newStatus];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $message = count($productIds) . ' produkter har fått ny status.';
                break;
                
            case 'update_price':
                // Update price
                $newPrice = isset($params['new_price']) ? floatval($params['new_price']) : 0;
                if ($newPrice <= 0) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Ogiltigt pris.'];
                }
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET price = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$newPrice];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $message = count($productIds) . ' produkter uppdaterade med nytt pris.';
                break;
                
            case 'move_shelf':
                // Move to different shelf
                $newShelfId = isset($params['new_shelf']) ? intval($params['new_shelf']) : 0;
                if ($newShelfId <= 0) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Ogiltig hylla.'];
                }
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET shelf_id = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$newShelfId];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $message = count($productIds) . ' produkter flyttade till ny hylla.';
                break;
                
            case 'set_special_price':
                // Set special price to specific value
                $specialPriceValue = isset($params['special_price_value']) ? intval($params['special_price_value']) : 0;
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET special_price = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$specialPriceValue];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $statusText = $specialPriceValue ? 'markerade som rea' : 'tog bort rea-markeringen från';
                $message = count($productIds) . ' produkter ' . $statusText . '.';
                break;
                
            case 'set_rare':
                // Set rare to specific value
                $rareValue = isset($params['rare_value']) ? intval($params['rare_value']) : 0;
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET rare = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$rareValue];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $statusText = $rareValue ? 'markerade som sällsynta' : 'tog bort sällsynt-markeringen från';
                $message = count($productIds) . ' produkter ' . $statusText . '.';
                break;
                
            case 'set_recommended':
                // Set recommended to specific value
                $recommendedValue = isset($params['recommended_value']) ? intval($params['recommended_value']) : 0;
                
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET recommended = ? WHERE prod_id IN ($placeholders)");
                
                $bindParams = [$recommendedValue];
                foreach ($productIds as $id) {
                    $bindParams[] = $id;
                }
                
                $stmt->execute($bindParams);
                $statusText = $recommendedValue ? 'markerade som rekommenderade' : 'tog bort rekommendation från';
                $message = count($productIds) . ' produkter ' . $statusText . '.';
                break;
                
            case 'delete':
                // Delete products from the database
                // First, make sure to delete related records to avoid foreign key constraints
                foreach ($productIds as $productId) {
                    // Delete product_author relationships
                    $stmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    
                    // Delete product_genre relationships
                    $stmt = $pdo->prepare("DELETE FROM product_genre WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    
                    // Delete images
                    $stmt = $pdo->prepare("DELETE FROM image WHERE prod_id = ?");
                    $stmt->execute([$productId]);
                    
                    // Update event_log to remove references to this product
                    $stmt = $pdo->prepare("UPDATE event_log SET product_id = NULL WHERE product_id = ?");
                    $stmt->execute([$productId]);
                }
                
                // Now delete the products
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("DELETE FROM product WHERE prod_id IN ($placeholders)");
                $stmt->execute($productIds);
                
                $message = count($productIds) . ' produkter har tagits bort.';
                break;
                
            default:
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Okänd åtgärd.'];
        }
        
        // Log the batch action
        $userId = $_SESSION['user_id'] ?? 1;
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description) 
            VALUES (?, ?, ?)
        ");
        $eventType = 'batch_' . $operation;
        $eventDescription = 'Batch operation: ' . $message;
        $logStmt->execute([$userId, $eventType, $eventDescription]);
        
        $pdo->commit();
        return ['success' => true, 'message' => $message];
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Batch operation error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Ett fel inträffade: ' . $e->getMessage()];
    }
}