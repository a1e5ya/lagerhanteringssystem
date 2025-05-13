<?php
/**
 * Lists Management
 * 
 * Contains:
 * - Product lists with filtering
 * - Batch operations
 * - Export functionality
 * 
 * Functions:
 * - selectListItems()
 * - renderList()
 * - printList()
 * - batchOperations()
 * - exportToCSV()
 */

// Include necessary files
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/db_functions.php';
require_once '../includes/auth.php';

// Check if user is authenticated and has admin or editor permissions
// Only Admin (1) or Editor (2) roles can access this page
if (!function_exists('checkAuth')) {
    function checkAuth($requiredRole) {
        // Simple authentication check if not already included
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: ../index.php?auth_error=1");
            exit;
        }

        if ($_SESSION['user_role'] > $requiredRole) {
            header("Location: ../index.php?auth_error=1");
            exit;
        }
    }
}

// Check authentication - requires admin or editor role
checkAuth(2); // Role 2 (Editor) or above required

// Process any AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    switch ($action) {
        case 'batch_action':
            $batchAction = $_POST['batch_action'] ?? '';
            $productIds = json_decode($_POST['product_ids'] ?? '[]', true);
            
            if (empty($productIds)) {
                echo json_encode(['success' => false, 'message' => 'Inga produkter valda.']);
                exit;
            }
            
            $result = batchOperations($productIds, $batchAction, $_POST);
            echo json_encode($result);
            break;
            
        case 'export_csv':
            $products = [];
            // Call function to generate CSV
            $csvData = exportToCSV($products);
            echo json_encode(['success' => true, 'data' => $csvData]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Okänd åtgärd.']);
    }
    
    exit;
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
    
    if (empty($productIds)) {
        return ['success' => false, 'message' => 'Inga produkter valda.'];
    }
    
    try {
        $pdo->beginTransaction();
        
        switch ($operation) {
            case 'sell':
                // Update status to sold (2)
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET status = 2 WHERE prod_id IN ($placeholders)");
                $stmt->execute($productIds);
                $message = count($productIds) . ' produkter markerade som sålda.';
                break;
                
            case 'return':
                // Update status to available (1)
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $stmt = $pdo->prepare("UPDATE product SET status = 1 WHERE prod_id IN ($placeholders)");
                $stmt->execute($productIds);
                $message = count($productIds) . ' produkter återställda till tillgängliga.';
                break;
                
            case 'delete':
                // First, delete related records to avoid foreign key constraints
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

/**
 * Exports products to CSV format
 * 
 * @param array $products Products to export
 * @return string CSV data
 */
function exportToCSV($products) {
    // Define CSV headers
    $headers = [
        'ID', 'Titel', 'Författare', 'Kategori', 'Hylla', 
        'Skick', 'Pris', 'Status', 'Tillagd datum'
    ];
    
    // Start output buffer to capture CSV data
    ob_start();
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Output the column headings
    fputcsv($output, $headers);
    
    // Output each row of the data
    foreach ($products as $product) {
        $row = [
            $product->prod_id,
            $product->title,
            $product->author_name ?? '',
            $product->category_name,
            $product->shelf_name,
            $product->condition_name,
            $product->price,
            $product->status_name,
            date('Y-m-d', strtotime($product->date_added))
        ];
        
        fputcsv($output, $row);
    }
    
    // Get the contents
    $csvData = ob_get_clean();
    
    return $csvData;
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
                GROUP_CONCAT(DISTINCT a.first_name,' ',a.last_name SEPARATOR ', ') AS author_name,
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
    
    if (!empty($criteria['status'])) {
        if ($criteria['status'] !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $criteria['status'];
        }
    }
    
    if (isset($criteria['min_price'])) {
        $sql .= " AND p.price >= ?";
        $params[] = $criteria['min_price'];
    }
    
    if (isset($criteria['max_price'])) {
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
        $sql .= " AND (p.price IS NULL OR p.price = 0)";
    }
    
    if (isset($criteria['poor_condition']) && $criteria['poor_condition']) {
        $sql .= " AND p.condition_id = 4"; // Assuming 4 is 'Acceptabelt' (lowest condition)
    }
    
    // Group by to avoid duplicates
    $sql .= " GROUP BY p.prod_id";
    
    // Order by
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
        return [];
    }
}

// Get categories, shelves, conditions, genres, and statuses for dropdowns
function getDropdownOptions() {
    global $pdo;
    $options = [];
    
    try {
        // Get categories
        $stmt = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name");
        $options['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get shelves
        $stmt = $pdo->query("SELECT shelf_id, shelf_name FROM shelf ORDER BY shelf_name");
        $options['shelves'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get conditions
        $stmt = $pdo->query("SELECT condition_id, condition_name FROM `condition` ORDER BY condition_id");
        $options['conditions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get genres
        $stmt = $pdo->query("SELECT genre_id, genre_name FROM genre ORDER BY genre_name");
        $options['genres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statuses
        $stmt = $pdo->query("SELECT status_id, status_name FROM status ORDER BY status_id");
        $options['statuses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $options;
    } catch (PDOException $e) {
        error_log("Error fetching dropdown options: " . $e->getMessage());
        return [];
    }
}

// Get dropdown options
$dropdownOptions = getDropdownOptions();

// Get initial products for the list
$initialProducts = selectListItems(['page' => 1, 'limit' => 15]);
?>

<div class="tab-pane fade show active" id="lists">
    <div class="mb-4">
        <!-- Quick filter buttons -->
        <div class="row">
            <div class="col-md-3 mb-2">
                <button class="btn btn-outline-primary w-100" id="list-no-price">Utan pris</button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-outline-primary w-100" id="list-poor-condition">Dåligt skick</button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-outline-primary w-100" id="list-shelf-check">
                    <span>Inventering av hylla</span>
                    <select class="form-select form-select-sm mt-1" id="shelf-selector">
                        <option value="">Välj hylla</option>
                        <?php foreach ($dropdownOptions['shelves'] as $shelf): ?>
                            <option value="<?php echo htmlspecialchars($shelf['shelf_name']); ?>"><?php echo htmlspecialchars($shelf['shelf_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button class="btn btn-outline-primary w-100" id="list-older-than">
                    <span>Objekt äldre än</span>
                    <div class="input-group input-group-sm mt-1">
                        <input type="number" class="form-control" id="year-threshold" placeholder="År">
                    </div>
                </button>
            </div>
          <!--  <div class="col-md-2 mb-2">
    <button class="btn btn-outline-danger w-100" id="clear-all-filters">
        <i class="fas fa-times me-1"></i> Rensa alla filter
    </button> -->
</div>
        </div>
        <br>

        <!-- Advanced Filtering Section -->
        <div class="card mb-4">
            <div class="card-header bg-light" id="filter-header" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                <h5 class="mb-0" style="margin-bottom: 0px;">Avancerad filtrering</h5>
                <i class="fas fa-chevron-down toggle-icon" style="cursor: pointer; transition: transform 0.3s; font-size: 1.2rem; margin-left: auto; transform: rotate(180deg);"></i>
            </div>
            <div class="card-body" id="filter-body">
                <div class="row mb-3">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="list-categories" class="form-label">Kategori</label>
                        <select class="form-select" id="list-categories">
                            <option value="">Alla kategorier</option>
                            <?php foreach ($dropdownOptions['categories'] as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['category_id']); ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="list-genre" class="form-label">Genre</label>
                        <select class="form-select" id="list-genre">
                            <option value="">Alla genrer</option>
                            <?php foreach ($dropdownOptions['genres'] as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre['genre_name']); ?>"><?php echo htmlspecialchars($genre['genre_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="list-condition" class="form-label">Skick</label>
                        <select class="form-select" id="list-condition">
                            <option value="">Alla skick</option>
                            <?php foreach ($dropdownOptions['conditions'] as $condition): ?>
                                <option value="<?php echo htmlspecialchars($condition['condition_name']); ?>"><?php echo htmlspecialchars($condition['condition_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="list-status" class="form-label">Status</label>
                        <select class="form-select" id="list-status">
                            <option value="all">Alla statusar</option>
                            <?php foreach ($dropdownOptions['statuses'] as $status): ?>
                                <option value="<?php echo htmlspecialchars($status['status_name']); ?>"><?php echo htmlspecialchars($status['status_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Prisintervall</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price-min" placeholder="Min €">
                            <span class="input-group-text">till</span>
                            <input type="number" class="form-control" id="price-max" placeholder="Max €">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Datum tillagt</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="date-min">
                            <span class="input-group-text">till</span>
                            <input type="date" class="form-control" id="date-max">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label for="list-search" class="form-label">Fritextsökning</label>
                        <input type="text" class="form-control" id="list-search" placeholder="Sök i titel, författare eller anteckningar">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="apply-filter-btn">
                            <i class="fas fa-filter me-1"></i> Tillämpa filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="lists-table">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Författare</th>
                    <th>Kategori</th>
                    <th>Hylla</th>
                    <th>Skick</th>
                    <th>Pris</th>
                    <th>Status</th>
                    <th>Tillagd datum</th>
                </tr>
            </thead>
            <tbody id="lists-body">
                <?php if (empty($initialProducts) || (count($initialProducts) === 1 && isset($initialProducts[0]->pagination) && $initialProducts[0]->pagination['totalResults'] === 0)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-3">Inga produkter hittades.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    foreach ($initialProducts as $product):
                        // Skip dummy products used only for pagination
                        if (isset($product->prod_id)):
                            $statusClass = $product->status == 1 ? 'bg-success' : 'bg-secondary';
                            $formattedDate = date('Y-m-d', strtotime($product->date_added));
                            $formattedPrice = number_format($product->price, 2, ',', ' ') . ' €';
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="list-item" value="<?php echo $product->prod_id; ?>">
                        </td>
                        <td><?php echo $product->prod_id; ?></td>
                        <td><?php echo htmlspecialchars($product->title); ?></td>
                        <td><?php echo htmlspecialchars($product->author_name ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($product->category_name); ?></td>
                        <td><?php echo htmlspecialchars($product->shelf_name); ?></td>
                        <td><?php echo htmlspecialchars($product->condition_name); ?></td>
                        <td><?php echo $formattedPrice; ?></td>
                        <td>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($product->status_name); ?>
                            </span>
                        </td>
                        <td><?php echo $formattedDate; ?></td>
                    </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="items-count">
            <span id="selected-count">0</span> av <span id="total-count">
                <?php echo isset($initialProducts[0]->pagination) ? $initialProducts[0]->pagination['totalResults'] : 0; ?>
            </span> objekt valda
        </div>
        <div class="pagination-controls d-flex align-items-center">
            <span class="me-2">Sida 
                <span id="current-page">
                    <?php echo isset($initialProducts[0]->pagination) ? $initialProducts[0]->pagination['currentPage'] : 1; ?>
                </span> av 
                <span id="total-pages">
                    <?php echo isset($initialProducts[0]->pagination) ? $initialProducts[0]->pagination['totalPages'] : 1; ?>
                </span>
            </span>
            <button class="btn btn-sm btn-outline-secondary me-1" id="prev-page-btn" 
                <?php echo (!isset($initialProducts[0]->pagination) || $initialProducts[0]->pagination['currentPage'] <= 1) ? 'disabled' : ''; ?>>
                &laquo;
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="next-page-btn"
                <?php echo (!isset($initialProducts[0]->pagination) || $initialProducts[0]->pagination['currentPage'] >= $initialProducts[0]->pagination['totalPages']) ? 'disabled' : ''; ?>>
                &raquo;
            </button>
        </div>
    </div>
    <br>

    <!-- Batch Operations Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Batchåtgärder</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-update-price" disabled>
                        <i class="fas fa-tag me-1"></i> Uppdatera pris
                    </button>
                </div>

                <div class="col-md-3 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-update-status" disabled>
                        <i class="fas fa-exchange-alt me-1"></i> Ändra status
                    </button>
                </div>

                <div class="col-md-3 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-move-shelf" disabled>
                        <i class="fas fa-arrows-alt me-1"></i> Flytta till hylla
                    </button>
                </div>

                <div class="col-md-3 mb-2">
                    <button class="btn btn-outline-danger w-100" id="batch-delete" disabled>
                        <i class="fas fa-trash-alt me-1"></i> Ta bort markerade
                    </button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 mb-2">
                    <button class="btn btn-outline-primary w-100" id="print-list-btn">
                        <i class="fas fa-print me-1"></i> Skriv ut lista
                    </button>
                </div>

                <div class="col-md-6 mb-2">
                    <button class="btn btn-outline-primary w-100" id="export-csv-btn">
                        <i class="fas fa-file-csv me-1"></i> Exportera till CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Price Modal -->
<div class="modal fade" id="updatePriceModal" tabindex="-1" aria-labelledby="updatePriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePriceModalLabel">Uppdatera pris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update-price-form">
                    <div class="mb-3">
                        <label for="new-price" class="form-label">Nytt pris (€)</label>
                        <input type="number" step="0.01" class="form-control" id="new-price" required min="0.01">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-update-price">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Ändra status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update-status-form">
                    <div class="mb-3">
                        <label for="new-status" class="form-label">Ny status</label>
                        <select class="form-select" id="new-status" required>
                            <?php foreach ($dropdownOptions['statuses'] as $status): ?>
                                <option value="<?php echo $status['status_id']; ?>"><?php echo htmlspecialchars($status['status_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-update-status">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Move Shelf Modal -->
<div class="modal fade" id="moveShelfModal" tabindex="-1" aria-labelledby="moveShelfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveShelfModalLabel">Flytta till hylla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="move-shelf-form">
                    <div class="mb-3">
                        <label for="new-shelf" class="form-label">Ny hylla</label>
                        <select class="form-select" id="new-shelf" required>
                            <?php foreach ($dropdownOptions['shelves'] as $shelf): ?>
                                <option value="<?php echo $shelf['shelf_id']; ?>"><?php echo htmlspecialchars($shelf['shelf_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-move-shelf">Flytta</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Bekräfta borttagning</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Är du säker på att du vill ta bort <span id="delete-count">0</span> valda objekt?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Denna åtgärd kan inte ångras!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Ta bort</button>
            </div>
        </div>
    </div>
</div>

<script>
// Execute when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables for current state
    let currentFilters = {
        page: 1,
        limit: 15
    };
    let selectedItems = [];

    // Function to load products with filters
function loadProducts(filters = {}) {
    console.log("loadProducts called with filters:", filters);
    
    // Merge with current filters
    currentFilters = {...currentFilters, ...filters};
    
    // Show loading indicator
    const listsBody = document.querySelector('#lists-body');
    if (!listsBody) {
        console.error('#lists-body element not found');
        return;
    }
    
    listsBody.innerHTML = 
        '<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status">' +
        '<span class="visually-hidden">Loading...</span></div></td></tr>';
    
    // Perform AJAX request
    $.ajax({
        url: 'admin/list_ajax_handler.php',
        type: 'POST',
        data: {
            action: 'get_filtered_products',
            ...currentFilters
        },
        dataType: 'json',
        success: function(data) {
            console.log("Response received:", data);
            if (data.success) {
                // Update table body
                $('#lists-body').html(data.html);
                
                // Update pagination info
                $('#current-page').text(data.pagination.currentPage);
                $('#total-pages').text(data.pagination.totalPages);
                $('#total-count').text(data.pagination.totalResults);
                
                // Enable/disable pagination buttons
                $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
                
                // Reset selected items
                selectedItems = [];
                updateSelectedCount();
                updateBatchButtons();
                
                // Make rows clickable
                makeRowsClickable();
            } else {
                // Show error
                $('#lists-body').html('<tr><td colspan="10" class="text-center text-danger">' + (data.message || 'Ett fel inträffade') + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            $('#lists-body').html('<tr><td colspan="10" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>');
        }
    });
}
    // Toggle advanced filter visibility
    const filterHeader = document.querySelector('#filter-header');
    const filterBody = document.querySelector('#filter-body');
    const toggleIcon = document.querySelector('.toggle-icon');
    
    filterHeader.addEventListener('click', function() {
        // Toggle body display
        const isVisible = filterBody.style.display !== 'none';
        filterBody.style.display = isVisible ? 'none' : 'block';
        
        // Rotate icon
        toggleIcon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
    });
    
    // Apply filter button click
    document.querySelector('#apply-filter-btn').addEventListener('click', function() {
        // Get all filter values
        const filters = {
            category: document.querySelector('#list-categories').value,
            genre: document.querySelector('#list-genre').value,
            condition: document.querySelector('#list-condition').value,
            status: document.querySelector('#list-status').value,
            min_price: document.querySelector('#price-min').value,
            max_price: document.querySelector('#price-max').value,
            min_date: document.querySelector('#date-min').value,
            max_date: document.querySelector('#date-max').value,
            search: document.querySelector('#list-search').value,
            page: 1 // Reset to page 1 for new filter
        };
        
        // Load products with filters
        loadProducts(filters);
    });
    
    // Quick filter buttons
    document.querySelector('#list-no-price').addEventListener('click', function() {
        loadProducts({
            no_price: true,
            page: 1,
            // Clear other filters
            category: '',
            genre: '',
            condition: '',
            status: 'all',
            min_price: '',
            max_price: '',
            min_date: '',
            max_date: '',
            search: ''
        });
        
        // Reset form fields
        document.querySelector('#list-categories').value = '';
        document.querySelector('#list-genre').value = '';
        document.querySelector('#list-condition').value = '';
        document.querySelector('#list-status').value = 'all';
        document.querySelector('#price-min').value = '';
        document.querySelector('#price-max').value = '';
        document.querySelector('#date-min').value = '';
        document.querySelector('#date-max').value = '';
        document.querySelector('#list-search').value = '';
    });
    
    document.querySelector('#list-poor-condition').addEventListener('click', function() {
        loadProducts({
            poor_condition: true,
            page: 1,
            // Clear other filters
            category: '',
            genre: '',
            condition: '',
            status: 'all',
            min_price: '',
            max_price: '',
            min_date: '',
            max_date: '',
            search: ''
        });
        
        // Reset form fields
        document.querySelector('#list-categories').value = '';
        document.querySelector('#list-genre').value = '';
        document.querySelector('#list-condition').value = '';
        document.querySelector('#list-status').value = 'all';
        document.querySelector('#price-min').value = '';
        document.querySelector('#price-max').value = '';
        document.querySelector('#date-min').value = '';
        document.querySelector('#date-max').value = '';
        document.querySelector('#list-search').value = '';
    });
    
    // Shelf inventory button
    document.querySelector('#shelf-selector').addEventListener('change', function() {
        const shelfName = this.value;
        if (shelfName) {
            loadProducts({
                shelf: shelfName,
                page: 1,
                // Clear other filters
                category: '',
                genre: '',
                condition: '',
                status: 'all',
                min_price: '',
                max_price: '',
                min_date: '',
                max_date: '',
                search: ''
            });
            
            // Reset form fields
            document.querySelector('#list-categories').value = '';
            document.querySelector('#list-genre').value = '';
            document.querySelector('#list-condition').value = '';
            document.querySelector('#list-status').value = 'all';
            document.querySelector('#price-min').value = '';
            document.querySelector('#price-max').value = '';
            document.querySelector('#date-min').value = '';
            document.querySelector('#date-max').value = '';
            document.querySelector('#list-search').value = '';
        }
    });
    
    // Older than filter
    document.querySelector('#list-older-than').addEventListener('click', function() {
        const yearThreshold = document.querySelector('#year-threshold').value;
        if (yearThreshold) {
            loadProducts({
                year_threshold: yearThreshold,
                page: 1,
                // Clear other filters
                category: '',
                genre: '',
                condition: '',
                status: 'all',
                min_price: '',
                max_price: '',
                min_date: '',
                max_date: '',
                search: ''
            });
            
            // Reset form fields
            document.querySelector('#list-categories').value = '';
            document.querySelector('#list-genre').value = '';
            document.querySelector('#list-condition').value = '';
            document.querySelector('#list-status').value = 'all';
            document.querySelector('#price-min').value = '';
            document.querySelector('#price-max').value = '';
            document.querySelector('#date-min').value = '';
            document.querySelector('#date-max').value = '';
            document.querySelector('#list-search').value = '';
        }
    });
    
    // Pagination buttons
    document.querySelector('#prev-page-btn').addEventListener('click', function() {
        if (!this.disabled) {
            loadProducts({ page: currentFilters.page - 1 });
        }
    });
    
    document.querySelector('#next-page-btn').addEventListener('click', function() {
        if (!this.disabled) {
            loadProducts({ page: currentFilters.page + 1 });
        }
    });
    
    // Select all checkbox
    document.querySelector('#select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="list-item"]');
        const isChecked = this.checked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            
            // Update selectedItems array
            const productId = parseInt(checkbox.value);
            const index = selectedItems.indexOf(productId);
            
            if (isChecked && index === -1) {
                selectedItems.push(productId);
            } else if (!isChecked && index !== -1) {
                selectedItems.splice(index, 1);
            }
        });
        
        updateSelectedCount();
        updateBatchButtons();
    });
    
    // Item checkboxes
    document.querySelector('#lists-body').addEventListener('change', function(e) {
        if (e.target.name === 'list-item') {
            const productId = parseInt(e.target.value);
            const index = selectedItems.indexOf(productId);
            
            if (e.target.checked && index === -1) {
                selectedItems.push(productId);
            } else if (!e.target.checked && index !== -1) {
                selectedItems.splice(index, 1);
            }
            
            updateSelectedCount();
            updateBatchButtons();
            
            // Update "select all" checkbox
            const allCheckboxes = document.querySelectorAll('input[name="list-item"]');
            const allChecked = selectedItems.length === allCheckboxes.length;
            document.querySelector('#select-all').checked = allChecked;
        }
    });
    
    // Update selected count
    function updateSelectedCount() {
        document.querySelector('#selected-count').textContent = selectedItems.length;
    }
    
    // Update batch buttons state
    function updateBatchButtons() {
        const hasSelection = selectedItems.length > 0;
        document.querySelector('#batch-update-price').disabled = !hasSelection;
        document.querySelector('#batch-update-status').disabled = !hasSelection;
        document.querySelector('#batch-move-shelf').disabled = !hasSelection;
        document.querySelector('#batch-delete').disabled = !hasSelection;
    }
    
    // Batch buttons click handlers
    document.querySelector('#batch-update-price').addEventListener('click', function() {
        if (selectedItems.length > 0) {
            $('#updatePriceModal').modal('show');
        }
    });
    
    document.querySelector('#batch-update-status').addEventListener('click', function() {
        if (selectedItems.length > 0) {
            $('#updateStatusModal').modal('show');
        }
    });
    
    document.querySelector('#batch-move-shelf').addEventListener('click', function() {
        if (selectedItems.length > 0) {
            $('#moveShelfModal').modal('show');
        }
    });
    
    document.querySelector('#batch-delete').addEventListener('click', function() {
        if (selectedItems.length > 0) {
            // Update count in modal
            document.querySelector('#delete-count').textContent = selectedItems.length;
            $('#deleteConfirmModal').modal('show');
        }
    });
    
    // Modal confirm buttons
    document.querySelector('#confirm-update-price').addEventListener('click', function() {
        const newPrice = document.querySelector('#new-price').value;
        if (newPrice && selectedItems.length > 0) {
            performBatchAction('update_price', { new_price: newPrice });
        }
    });
    
    document.querySelector('#confirm-update-status').addEventListener('click', function() {
        const newStatus = document.querySelector('#new-status').value;
        if (newStatus && selectedItems.length > 0) {
            performBatchAction('update_status', { new_status: newStatus });
        }
    });
    
    document.querySelector('#confirm-move-shelf').addEventListener('click', function() {
        const newShelf = document.querySelector('#new-shelf').value;
        if (newShelf && selectedItems.length > 0) {
            performBatchAction('move_shelf', { new_shelf: newShelf });
        }
    });
    
    document.querySelector('#confirm-delete').addEventListener('click', function() {
        if (selectedItems.length > 0) {
            performBatchAction('delete');
        }
    });
    
    // Export and print buttons
    document.querySelector('#export-csv-btn').addEventListener('click', function() {
        exportData('csv');
    });
    
    document.querySelector('#print-list-btn').addEventListener('click', function() {
        printList();
    });
    
    // Function to perform batch actions
    function performBatchAction(action, params = {}) {
        if (selectedItems.length === 0) return;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'batch_action');
        formData.append('batch_action', action);
        formData.append('product_ids', JSON.stringify(selectedItems));
        
        // Add additional parameters
        for (const [key, value] of Object.entries(params)) {
            formData.append(key, value);
        }
        
        // Send AJAX request
        fetch('lists.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Hide modals
            $('.modal').modal('hide');
            
            if (data.success) {
                // Show success message
                showMessage(data.message, 'success');
                
                // Reload products
                loadProducts();
            } else {
                // Show error message
                showMessage(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
            
            // Hide modals
            $('.modal').modal('hide');
        });
    }
    
    // Function to export data
    function exportData(format) {
        // Prepare export URL with current filters
        let exportUrl = `export.php?format=${format}`;
        
        // Add all current filters to URL
        for (const [key, value] of Object.entries(currentFilters)) {
            if (value) {
                exportUrl += `&${key}=${encodeURIComponent(value)}`;
            }
        }
        
        // Add selected items if any
        if (selectedItems.length > 0) {
            exportUrl += `&selected_items=${encodeURIComponent(JSON.stringify(selectedItems))}`;
        }
        
        // Open in new tab/window
        window.open(exportUrl, '_blank');
    }
    
    // Function to print list
    function printList() {
        // Open print dialog
        window.print();
    }
    
    // Function to show messages
    function showMessage(message, type) {
        // Create alert element
        const alertEl = document.createElement('div');
        alertEl.className = `alert alert-${type} alert-dismissible fade show`;
        alertEl.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Find message container or create one
        let messageContainer = document.querySelector('#message-container');
        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.id = 'message-container';
            document.querySelector('#lists').prepend(messageContainer);
        }
        
        // Add the alert
        messageContainer.appendChild(alertEl);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertEl.classList.remove('show');
            setTimeout(() => {
                alertEl.remove();
            }, 150);
        }, 5000);
    }
    

});
</script>