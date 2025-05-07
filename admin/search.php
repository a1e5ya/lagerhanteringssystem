<?php
/**
 * Search Products
 * 
 * Contains:
 * - Product search functionality
 * - AJAX endpoints for public and admin search
 * - Product status management
 * 
 * Functions:
 * - searchProducts() - Searches products based on criteria
 * - renderIndexProducts() - For public view
 * - renderAdminProducts() - For admin view only
 * - changeProductSaleStatus() - Changes product availability
 */

 define('BASE_PATH', dirname(__DIR__));

// Include necessary files if not already included
if (!function_exists('safeEcho')) {
    require_once BASE_PATH . '/includes/functions.php';
}
require_once BASE_PATH . '/config/config.php';

// Process AJAX requests first
if (isset($_GET['ajax']) || (isset($_POST['action']) && $_POST['action'] === 'change_status')) {
    processAjaxRequest();
    exit; // Stop further execution after handling AJAX request
}

/**
 * Handle AJAX requests
 */
function processAjaxRequest() {
    global $pdo;
    
    // For product status change (POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_status') {
        // Set JSON content type
        header('Content-Type: application/json');
        
        // Check if user is authenticated
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        // Get parameters
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $newStatus = isset($_POST['status']) ? intval($_POST['status']) : 0;
        
        // Validate input
        if ($productId <= 0 || ($newStatus != 1 && $newStatus != 2)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        // Change product status
        $result = changeProductSaleStatus($productId, $newStatus);
        
        if ($result) {
            $statusText = ($newStatus == 1) ? 'tillgänglig' : 'såld';
            echo json_encode([
                'success' => true, 
                'message' => 'Produkten har markerats som ' . $statusText
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Ett fel inträffade. Kunde inte ändra produktstatus.'
            ]);
        }
        exit;
    }
    
    // Handle search requests (GET request with ajax parameter)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
        // Get search parameters
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
        
        // Prepare search parameters
        $searchParams = [];
        if (!empty($searchTerm)) {
            $searchParams['search'] = $searchTerm;
        }
        if ($categoryFilter !== 'all') {
            $searchParams['category'] = $categoryFilter;
        }
        
        try {
            // Get products based on search parameters
            $products = searchProducts($searchParams);
            
            // Determine which view to render based on the ajax parameter
            $viewType = $_GET['ajax'];
            
            if ($viewType === 'admin') {
                // Admin view - check authentication
                if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                    echo '<tr><td colspan="8" class="text-center text-danger">Behörighet saknas. Logga in för att fortsätta.</td></tr>';
                    exit;
                }
                
                // Return admin format
                echo renderAdminProducts($products);
            } else if ($viewType === 'lists') {
                // Lists view - check authentication
                if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                    echo '<tr><td colspan="10" class="text-center text-danger">Behörighet saknas. Logga in för att fortsätta.</td></tr>';
                    exit;
                }
                
                // Return lists format
                echo renderListsProducts($products);
            } else {
                // Public view (default)
                echo renderIndexProducts($products);
            }
        } catch (Exception $e) {
            // Log error
            error_log('Search error: ' . $e->getMessage());
            
            // Return error message
            $cols = ($viewType === 'public') ? 7 : 8;
            echo '<tr><td colspan="' . $cols . '" class="text-center text-danger">Ett fel inträffade: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
        }
        
        exit;
    }
}

/**
 * Gets all categories from the database
 * 
 * @param PDO $pdo Database connection
 * @return array List of categories
 */
function getCategories($pdo) {
    try {
        // Prepare SQL query to get categories
        $stmt = $pdo->prepare("SELECT category_id, category_name FROM category ORDER BY category_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Searches products based on user search parameters
 * 
 * @param array|null $searchParams Search parameters (search term, category)
 * @return array Found products
 */
function searchProducts(?array $searchParams = null): array
{
    global $pdo;
    
    // Check if PDO is properly initialized
    if (!is_object($pdo)) {
        throw new Exception("Database connection not available");
    }

    // Prepare search term
    $trimmedSearch = trim($searchParams['search'] ?? '');
    $searchTerm = '%' . $trimmedSearch . '%';

    // Get category filter
    $categoryFilter = !empty($searchParams['category']) && $searchParams['category'] !== 'all' 
                    ? $searchParams['category'] : null;

    // Build SQL query
    $sql = "SELECT
                p.prod_id,
                p.title,
                p.status,
                s.status_name,
                p.shelf_id,
                sh.shelf_name,
                GROUP_CONCAT(DISTINCT a.first_name SEPARATOR ', ') AS first_names,
                GROUP_CONCAT(DISTINCT a.last_name SEPARATOR ', ') AS last_names,                
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
            JOIN `status` s ON p.status = s.status_id";
    
    // Add WHERE clause only if we have search parameters
    $hasWhere = false;
    $params = [];
    
    if (!empty($trimmedSearch) || $categoryFilter !== null) {
        $sql .= " WHERE";
        $hasWhere = true;
        
        if (!empty($trimmedSearch)) {
            $sql .= " (p.title LIKE :searchTerm1 OR
                      a.first_name LIKE :searchTerm2 OR
                      a.last_name LIKE :searchTerm3 OR
                      cat.category_name LIKE :searchTerm4";
            
            // Allow searching by product ID if numeric
            if (is_numeric($trimmedSearch)) {
                $sql .= " OR p.prod_id = :prodId";
            }
            
            $sql .= ")";
            
            $params[':searchTerm1'] = $searchTerm;
            $params[':searchTerm2'] = $searchTerm;
            $params[':searchTerm3'] = $searchTerm;
            $params[':searchTerm4'] = $searchTerm;
            
            if (is_numeric($trimmedSearch)) {
                $params[':prodId'] = $trimmedSearch;
            }
        }
        
        // Add category filter if provided
        if ($categoryFilter !== null) {
            if (!empty($trimmedSearch)) {
                $sql .= " AND";
            }
            $sql .= " p.category_id = :categoryId";
            $params[':categoryId'] = $categoryFilter;
        }
    }

    // Finish SQL with GROUP BY
    $sql .= " GROUP BY p.prod_id";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Database error in searchProducts: " . $e->getMessage());
        throw new Exception("Database error: " . $e->getMessage());
    }
}

/**
 * Renders products for the public view (index.php)
 * 
 * @param array $products Products to render
 * @return string HTML for the product table
 */
function renderIndexProducts(array $products): string
{
    ob_start();

    if (!empty($products)) {
        foreach ($products as $product) {
            ?>
            <tr class="clickable-row" data-href="singleproduct.php?id=<?= safeEcho($product->prod_id) ?>">
                <td data-label="Titel"><?= safeEcho($product->title) ?></td>
                <td data-label="Författare/Artist">
                    <?php
                    $authorName = '';
                    if (!empty($product->first_names) && !empty($product->last_names)) {
                        $authorName = $product->first_names . ' ' . $product->last_names;
                    } elseif (!empty($product->first_names)) {
                        $authorName = $product->first_names;
                    } elseif (!empty($product->last_names)) {
                        $authorName = $product->last_names;
                    }
                    echo safeEcho($authorName);
                    ?>
                </td>
                <td data-label="Kategori"><?= safeEcho($product->category_name) ?></td>
                <td data-label="Genre"><?= safeEcho($product->genre_names) ?></td>
                <td data-label="Skick"><?= safeEcho($product->condition_name) ?></td>
                <td data-label="Pris"><?= safeEcho(number_format($product->price, 2, ',', ' ')) . ' €' ?></td>
                <td><a class="btn btn-success d-block d-md-none" href="singleproduct.php?id=<?= safeEcho($product->prod_id) ?>">Visa detaljer</a></td>
            </tr>
            <?php
        }
    } else {
        // No results found
        echo '<tr><td colspan="7" class="text-center py-3">Inga produkter hittades som matchar din sökning.</td></tr>';
    }

    return ob_get_clean();
}

/**
 * Renders products in admin panel format
 * 
 * @param array $products Array of product objects to render
 * @return string HTML output for the inventory table
 */
function renderAdminProducts(array $products): string
{
    ob_start();

    if (!empty($products)) {
        foreach ($products as $product) {
            // Determine status class for styling
            $statusClass = '';
            $statusName = $product->status_name ?? 'Tillgänglig';
            
            switch ($product->status ?? 1) {
                case 1: // Available
                    $statusClass = 'text-success';
                    break;
                case 2: // Sold
                    $statusClass = 'text-danger';
                    break;
            }

            // Format price
            $formattedPrice = number_format($product->price, 2, ',', ' ') . ' €';
            
            // Format author name
            $authorName = '';
            if (!empty($product->first_names) && !empty($product->last_names)) {
                $authorName = $product->first_names . ' ' . $product->last_names;
            } elseif (!empty($product->first_names)) {
                $authorName = $product->first_names;
            } elseif (!empty($product->last_names)) {
                $authorName = $product->last_names;
            }
            ?>
            <tr class="product-row" data-id="<?= safeEcho($product->prod_id) ?>">
                <td><?= safeEcho($product->prod_id) ?></td>
                <td><?= safeEcho($product->title) ?></td>
                <td><?= safeEcho($authorName) ?></td>
                <td><?= safeEcho($product->category_name) ?></td>
                <td><?= safeEcho($product->shelf_name) ?></td>
                <td><?= safeEcho($formattedPrice) ?></td>
                <td class="<?= $statusClass ?>"><?= safeEcho($statusName) ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="admin/adminsingleproduct.php?id=<?= safeEcho($product->prod_id) ?>" class="btn btn-outline-primary" title="Redigera">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <?php if (($product->status ?? 1) == 1): // If Available, show Sell button ?>
                            <button class="btn btn-outline-success quick-sell" data-id="<?= safeEcho($product->prod_id) ?>" title="Markera som såld">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        <?php elseif (($product->status ?? 0) == 2): // If Sold, show Return button ?>
                            <button class="btn btn-outline-warning quick-return" data-id="<?= safeEcho($product->prod_id) ?>" title="Återställ till tillgänglig">
                                <i class="fas fa-undo"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php
        }
    } else {
        // No results found
        echo '<tr><td colspan="8" class="text-center text-muted py-3">Inga produkter hittades som matchar din sökning.</td></tr>';
    }

    return ob_get_clean();
}

/**
 * Renders products in lists format for admin
 * 
 * @param array $products Array of product objects to render
 * @return string HTML output for the lists table
 */
function renderListsProducts(array $products): string
{
    ob_start();

    if (!empty($products)) {
        foreach ($products as $product) {
            // Determine status class for styling
            $statusClass = '';
            $statusName = $product->status_name ?? 'Tillgänglig';
            
            switch ($product->status ?? 1) {
                case 1: // Available
                    $statusClass = 'text-success';
                    break;
                case 2: // Sold
                    $statusClass = 'text-danger';
                    break;
            }

            // Format price
            $formattedPrice = number_format($product->price, 2, ',', ' ') . ' €';
            
            // Format date added (assuming it's available in the product object)
            $dateAdded = isset($product->date_added) ? date('Y-m-d', strtotime($product->date_added)) : '';
            
            ?>
            <tr>
                <td><input type="checkbox" class="item-checkbox" value="<?= safeEcho($product->prod_id) ?>"></td>
                <td><?= safeEcho($product->prod_id) ?></td>
                <td><?= safeEcho($product->title) ?></td>
                <td><?= safeEcho($product->first_names . ' ' . $product->last_names) ?></td>
                <td><?= safeEcho($product->category_name) ?></td>
                <td><?= safeEcho($product->shelf_name) ?></td>
                <td><?= safeEcho($product->condition_name) ?></td>
                <td><?= safeEcho($formattedPrice) ?></td>
                <td class="<?= $statusClass ?>"><?= safeEcho($statusName) ?></td>
                <td><?= $dateAdded ?></td>
            </tr>
            <?php
        }
    } else {
        // No results found
        echo '<tr><td colspan="10" class="text-center text-muted py-3">Inga produkter hittades som matchar din sökning.</td></tr>';
    }

    return ob_get_clean();
}

/**
 * Updates product sale status
 * 
 * @param int $productId The ID of the product to update
 * @param int $newStatus The new status for the product (1=Available, 2=Sold)
 * @return bool True if status changed successfully, false otherwise
 */
function changeProductSaleStatus(int $productId, int $newStatus): bool
{
    global $pdo;
    
    // Validate new status (only allow Available=1 or Sold=2)
    if ($newStatus != 1 && $newStatus != 2) {
        return false;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Update product status
        $stmt = $pdo->prepare("UPDATE product SET status = :status WHERE prod_id = :product_id");
        $stmt->execute([
            'status' => $newStatus,
            'product_id' => $productId
        ]);
        
        // Get product title for logging
        $titleStmt = $pdo->prepare("SELECT title FROM product WHERE prod_id = :product_id");
        $titleStmt->execute(['product_id' => $productId]);
        $productTitle = $titleStmt->fetchColumn();
        
        // Get current user ID from session
        $userId = $_SESSION['user_id'] ?? 1; // Default to ID 1 if not set
        
        // Determine description for log based on new status
        $statusDescription = ($newStatus == 1) ? 'Återställd till tillgänglig' : 'Markerad som såld';
        $eventDescription = $statusDescription . ': ' . $productTitle;
        
        // Log the status change
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description, product_id) 
            VALUES (:user_id, :event_type, :event_description, :product_id)
        ");
        $logStmt->execute([
            'user_id' => $userId,
            'event_type' => 'update',
            'event_description' => $eventDescription,
            'product_id' => $productId
        ]);
        
        // Commit transaction
        $pdo->commit();
        
        return true;
    } catch (PDOException $e) {
        // Roll back transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error changing product status: " . $e->getMessage());
        return false;
    }
}
?>

<!-- Only show the admin search form on admin pages -->
<?php if (basename($_SERVER['PHP_SELF']) === 'admin.php' || strpos($_SERVER['REQUEST_URI'], 'admin') !== false): ?>
<div class="tab-pane fade show active" id="search">
    <form method="GET" action="" id="admin-search-form">
        <div class="row mb-3">
            <div class="col-12 mb-3">
                <label for="search-term" class="form-label">Sökterm</label>
                <input type="text" class="form-control" id="search-term" name="search" placeholder="Ange titel, författare eller ID" value="<?= safeEcho($_GET['search'] ?? '') ?>">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="category-filter" class="form-label">Kategorifilter</label>
                <select class="form-select" id="category-filter" name="category">
                    <option value="all">Alla</option>
                    <?php 
                    $categories = getCategories($pdo);
                    foreach ($categories as $category): 
                    ?>
                    <option value="<?= safeEcho($category['category_id']) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                        <?= safeEcho($category['category_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button id="search-btn" class="btn btn-primary w-100" type="submit">Sök</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive mt-4">
        <table class="table table-hover" id="inventory-table">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Författare</th>
                    <th>Kategori</th>
                    <th>Hylla</th>
                    <th>Pris</th>
                    <th>Status</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody id="inventory-body">
                <?php
                // Get search parameters (if any)
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
                
                // Prepare search parameters
                $searchParams = array();
                if (!empty($searchTerm)) {
                    $searchParams['search'] = $searchTerm;
                }
                if ($categoryFilter !== 'all') {
                    $searchParams['category'] = $categoryFilter;
                }
                
                try {
                    // Get products based on search parameters
                    $products = searchProducts($searchParams);
                    
                    // Render products in admin table format
                    echo renderAdminProducts($products);
                } catch (Exception $e) {
                    echo '<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>