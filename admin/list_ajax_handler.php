<?php
/**
 * AJAX Handler for Lists 
 * 
 * Processes AJAX requests from lists.php
 */

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/db_functions.php';
require_once '../includes/auth.php';

// DEBUG CODE HERE - START
// Enable error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create a log function
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= " - Data: " . json_encode($data);
    }
    error_log($log_message);
}

// Log the request
debug_log("AJAX request received", $_POST);
// DEBUG CODE HERE - END

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
        case 'get_filtered_products':
            // Extract filter parameters
            $filters = [];
            
            // Pagination parameters
            $filters['page'] = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
            $filters['limit'] = isset($_POST['limit']) ? max(1, intval($_POST['limit'])) : 15;
            
            // Other filter parameters
            $filterKeys = [
                'category', 'genre', 'condition', 'status', 
                'min_price', 'max_price', 'min_date', 'max_date', 
                'search', 'no_price', 'poor_condition', 'year_threshold', 'shelf'
            ];
            
            foreach ($filterKeys as $key) {
                if (isset($_POST[$key]) && $_POST[$key] !== '') {
                    $filters[$key] = $_POST[$key];
                }
            }
            
            // Get products based on filters
            $products = selectListItems($filters);
            
            // Extract pagination data
            $pagination = [
                'currentPage' => $filters['page'],
                'totalPages' => 1,
                'totalResults' => 0
            ];
            
            if (!empty($products) && isset($products[0]->pagination)) {
                $pagination = $products[0]->pagination;
            }
            
            // Render product rows as HTML
            $html = renderProductRows($products);
            
            echo json_encode([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
            break;
            
            case 'batch_action':
                // Log that we entered this case
                error_log("Entered batch_action case");
                
                // Validate required parameters
                $batchAction = $_POST['batch_action'] ?? '';
                $productIdsJson = $_POST['product_ids'] ?? '[]';
                
                error_log("batch_action: " . $batchAction);
                error_log("product_ids (raw): " . $productIdsJson);
                
                // Decode product IDs from JSON string
                $productIds = json_decode($productIdsJson, true);
                
                error_log("product_ids (decoded): " . print_r($productIds, true));
                
                if (!is_array($productIds) || empty($productIds)) {
                    error_log("Error: No products selected or invalid product_ids");
                    echo json_encode(['success' => false, 'message' => 'Inga produkter valda.']);
                    exit;
                }
                
                // Log additional parameters
                foreach ($_POST as $key => $value) {
                    if ($key !== 'action' && $key !== 'batch_action' && $key !== 'product_ids') {
                        error_log("Additional parameter: {$key}: " . $value);
                    }
                }
                
                // Perform batch operation and get result
                $result = batchOperations($productIds, $batchAction, $_POST);
                
                error_log("Batch operation result: " . print_r($result, true));
                
                echo json_encode($result);
                break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}

/**
 * Render product rows as HTML
 * 
 * @param array $products Products to render
 * @return string HTML for table rows
 */
function renderProductRows($products) {
    ob_start();
    
    if (empty($products) || (count($products) === 1 && isset($products[0]->pagination) && $products[0]->pagination['totalResults'] === 0)) {
        echo '<tr><td colspan="10" class="text-center py-3">Inga produkter hittades.</td></tr>';
    } else {
        foreach ($products as $product) {
            // Skip dummy products used only for pagination
            if (isset($product->prod_id)) {
                $statusClass = $product->status == 1 ? 'bg-success' : 'bg-secondary';
                $formattedDate = date('Y-m-d', strtotime($product->date_added));
                $formattedPrice = number_format($product->price, 2, ',', ' ') . ' €';
                
                echo '<tr>';
                echo '<td><input type="checkbox" name="list-item" value="' . $product->prod_id . '"></td>';
                echo '<td>' . $product->prod_id . '</td>';
                echo '<td>' . htmlspecialchars($product->title) . '</td>';
                echo '<td>' . htmlspecialchars($product->author_name ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($product->category_name) . '</td>';
                echo '<td>' . htmlspecialchars($product->shelf_name) . '</td>';
                echo '<td>' . htmlspecialchars($product->condition_name) . '</td>';
                echo '<td>' . $formattedPrice . '</td>';
                echo '<td><span class="badge ' . $statusClass . '">' . htmlspecialchars($product->status_name) . '</span></td>';
                echo '<td>' . $formattedDate . '</td>';
                echo '</tr>';
            }
        }
    }
    
    return ob_get_clean();
}

/**
 * Get product lists based on criteria
 * 
 * @param array $criteria Search criteria
 * @return array Found products
 */
function selectListItems($criteria = []) {
    global $pdo;
    
    // Build SQL with appropriate filters
    $sql = "SELECT
                p.prod_id,
                p.title,
                p.status,
                s.status_name,
                p.shelf_id,
                sh.shelf_name,
                GROUP_CONCAT(DISTINCT CONCAT(a.first_name, ' ', a.last_name) SEPARATOR ', ') AS author_name,
                cat.category_name,
                p.category_id,
                GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ', ') AS genre_names,
                con.condition_name,
                p.price,
                p.date_added
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            JOIN category cat ON p.category_id = cat.category_id
            LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
            LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
            LEFT JOIN genre g ON pg.genre_id = g.genre_id
            JOIN `condition` con ON p.condition_id = con.condition_id
            JOIN `status` s ON p.status = s.status_id
            WHERE 1=1";
    
    $params = [];
    
    // Add criteria filters
    if (!empty($criteria['category'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = $criteria['category'];
    }
    
    if (!empty($criteria['genre'])) {
        $sql .= " AND g.genre_name = ?";
        $params[] = $criteria['genre'];
    }
    
    if (!empty($criteria['shelf'])) {
        $sql .= " AND sh.shelf_name = ?";
        $params[] = $criteria['shelf'];
    }
    
    if (!empty($criteria['condition'])) {
        $sql .= " AND con.condition_name = ?";
        $params[] = $criteria['condition'];
    }
    
    if (!empty($criteria['status']) && $criteria['status'] !== 'all') {
        $sql .= " AND s.status_name = ?";
        $params[] = $criteria['status'];
    }
    
    if (!empty($criteria['min_price'])) {
        $sql .= " AND p.price >= ?";
        $params[] = $criteria['min_price'];
    }
    
    if (!empty($criteria['max_price'])) {
        $sql .= " AND p.price <= ?";
        $params[] = $criteria['max_price'];
    }
    
    if (!empty($criteria['min_date'])) {
        $sql .= " AND p.date_added >= ?";
        $params[] = $criteria['min_date'];
    }
    
    if (!empty($criteria['max_date'])) {
        $sql .= " AND p.date_added <= ?";
        $params[] = $criteria['max_date'];
    }
    
    if (!empty($criteria['year_threshold'])) {
        $sql .= " AND p.year <= ?";
        $params[] = $criteria['year_threshold'];
    }
    
    if (!empty($criteria['search'])) {
        $sql .= " AND (p.title LIKE ? OR a.first_name LIKE ? OR a.last_name LIKE ? OR p.notes LIKE ?)";
        $searchTerm = "%{$criteria['search']}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Special filters
    if (isset($criteria['no_price']) && $criteria['no_price']) {
        $sql .= " AND (p.price IS NULL OR p.price = 0 OR p.price = '')";
    }
    
    if (isset($criteria['poor_condition']) && $criteria['poor_condition']) {
        $sql .= " AND p.condition_id = 4"; // Assuming 4 is 'Acceptabelt' (lowest condition)
    }
    
    // Group by to avoid duplicates
    $sql .= " GROUP BY p.prod_id";
    
    // Order by product ID by default
    $sql .= " ORDER BY p.prod_id ASC";
    
    // Add pagination
    if (isset($criteria['page']) && isset($criteria['limit'])) {
        $page = max(1, intval($criteria['page']));
        $limit = max(1, intval($criteria['limit']));
        $offset = ($page - 1) * $limit;
        
        // Create a copy of the query for counting total results
        $countSql = $sql;
        
        // Add limit clause to the main query
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    try {
        // Execute query to get products
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // If pagination is requested, count total results
        if (isset($criteria['page']) && isset($criteria['limit'])) {
            // Prepare count statement (uses same WHERE conditions)
            $countSql = "SELECT COUNT(*) FROM (" . $countSql . ") as counted";
            $countStmt = $pdo->prepare($countSql);
            
            // Remove pagination parameters as they're not needed for count
            array_pop($params); // Remove offset
            array_pop($params); // Remove limit
            
            $countStmt->execute($params);
            $totalResults = $countStmt->fetchColumn();
            
            // Add pagination info to first product
            if (!empty($products)) {
                $products[0]->pagination = [
                    'totalResults' => $totalResults,
                    'currentPage' => $page,
                    'totalPages' => ceil($totalResults / $limit),
                    'limit' => $limit
                ];
            } else {
                // Create a dummy product with pagination info if no products found
                $dummyProduct = new stdClass();
                $dummyProduct->pagination = [
                    'totalResults' => 0,
                    'currentPage' => $page,
                    'totalPages' => 0,
                    'limit' => $limit
                ];
                $products[] = $dummyProduct;
            }
        }
        
        return $products;
    } catch (PDOException $e) {
        error_log("Database error in selectListItems: " . $e->getMessage());
        throw new Exception("Database error: " . $e->getMessage());
    }
}

/**
 * Performs batch operations on multiple products
 * 
 * @param array $productIds Product IDs to operate on
 * @param string $operation Operation type (sell, return, delete, etc.)
 * @param array $params Additional parameters
 * @return array Result with success flag and message
 */
function batchOperations($productIds, $operation, $params = []) {
    global $pdo;
    
    debug_log("batchOperations called", [
        'operation' => $operation,
        'productIds' => $productIds,
        'params' => $params
    ]);

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