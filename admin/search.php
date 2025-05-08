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
    echo json_encode([
        'success' => true
        // No message field
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Ett fel inträffade. Kunde inte ändra produktstatus.' // Keep error message for debugging
    ]);
}
exit; }
    
    // Public pagination specific request
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'public_pagination') {
        // Get search parameters
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10; // Default to 10 for public view
        
        // Prepare search parameters
        $searchParams = [
            'page' => $page,
            'limit' => $limit
        ];
        
        if (!empty($searchTerm)) {
            $searchParams['search'] = $searchTerm;
        }
        if ($categoryFilter !== 'all') {
            $searchParams['category'] = $categoryFilter;
        }
        
        try {
            // Get products based on search parameters
            $products = searchProducts($searchParams);
            
            // Extract pagination metadata
            $pagination = null;
            if (!empty($products)) {
                $pagination = isset($products[0]->pagination) ? $products[0]->pagination : null;
            }
            
            // Render pagination controls
            if ($pagination && $pagination['totalPages'] > 1) {
                echo '<nav aria-label="Search results pagination">';
                echo '<ul class="pagination justify-content-center">';
                
                // Previous page button
                echo '<li class="page-item ' . (($pagination['currentPage'] <= 1) ? 'disabled' : '') . '">';
                echo '<a class="page-link public-pagination-link" href="javascript:void(0);" data-page="' . ($pagination['currentPage'] - 1) . '" aria-label="Previous">';
                echo '<span aria-hidden="true">&laquo;</span></a></li>';
                
                // Calculate range of page numbers to display
                $startPage = max(1, $pagination['currentPage'] - 2);
                $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
                
                // Ensure we always show at least 5 pages if available
                if ($endPage - $startPage + 1 < 5) {
                    if ($startPage == 1) {
                        $endPage = min($pagination['totalPages'], $startPage + 4);
                    } elseif ($endPage == $pagination['totalPages']) {
                        $startPage = max(1, $endPage - 4);
                    }
                }
                
                // Display page numbers
                for ($i = $startPage; $i <= $endPage; $i++) {
                    echo '<li class="page-item ' . (($i == $pagination['currentPage']) ? 'active' : '') . '">';
                    echo '<a class="page-link public-pagination-link" href="javascript:void(0);" data-page="' . $i . '">' . $i . '</a></li>';
                }
                
                // Next page button
                echo '<li class="page-item ' . (($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '') . '">';
                echo '<a class="page-link public-pagination-link" href="javascript:void(0);" data-page="' . ($pagination['currentPage'] + 1) . '" aria-label="Next">';
                echo '<span aria-hidden="true">&raquo;</span></a></li>';
                
                echo '</ul></nav>';
            } else {
                echo ''; // No pagination needed
            }
        } catch (Exception $e) {
            // Error handling
            echo '<div class="alert alert-danger">Ett fel inträffade: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        exit;
    }

    // Handle search requests (GET request with ajax parameter)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
        // Get search parameters
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        
        // Determine appropriate limit based on view type
        $viewType = $_GET['ajax'];
        $limit = 10; // Default for public view
        
        if ($viewType === 'admin' || $viewType === 'lists') {
            $limit = 20; // 20 per page for admin and lists views
        }
        
        // Prepare search parameters
        $searchParams = [
            'page' => $page,
            'limit' => $limit
        ];
        
        if (!empty($searchTerm)) {
            $searchParams['search'] = $searchTerm;
        }
        if ($categoryFilter !== 'all') {
            $searchParams['category'] = $categoryFilter;
        }
        
        try {
            // Get products based on search parameters
            $products = searchProducts($searchParams);
            
            // Extract pagination metadata
            $pagination = null;
            if (!empty($products)) {
                $pagination = isset($products[0]->pagination) ? $products[0]->pagination : null;
            }
            
            // Determine which view to render based on the ajax parameter
            if ($viewType === 'admin') {
                // Admin view - check authentication
                if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                    echo '<tr><td colspan="8" class="text-center text-danger">Behörighet saknas. Logga in för att fortsätta.</td></tr>';
                    exit;
                }
                
                // Return admin format
                echo renderAdminProducts($products);
                
                // Add pagination if needed
                if ($pagination && $pagination['totalPages'] > 1) {
                    echo renderPagination($pagination, 'admin');
                }
            } else if ($viewType === 'lists') {
                // Lists view - check authentication
                if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                    echo '<tr><td colspan="10" class="text-center text-danger">Behörighet saknas. Logga in för att fortsätta.</td></tr>';
                    exit;
                }
                
                // Return lists format
                echo renderListsProducts($products);
                
                // Add pagination if needed
                if ($pagination && $pagination['totalPages'] > 1) {
                    echo renderPagination($pagination, 'lists');
                }
            } else {
                // Public view (default)
                echo renderIndexProducts($products);
                
                // Pagination is handled separately via public_pagination AJAX call
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
 * Renders pagination controls
 * 
 * @param array $pagination Pagination metadata
 * @param string $view The view type ('public', 'admin', 'lists')
 * @return string HTML for pagination controls
 */
function renderPagination($pagination, $view = 'public') {
    $output = '';
    
    if ($pagination && $pagination['totalPages'] > 1) {
        $output .= '<tr><td colspan="' . ($view === 'lists' ? '10' : '8') . '"><nav aria-label="Search results pagination">';
        $output .= '<ul class="pagination justify-content-center mt-4">';
        
        // Previous page button
        $output .= '<li class="page-item ' . (($pagination['currentPage'] <= 1) ? 'disabled' : '') . '">';
        $output .= '<a class="page-link pagination-link" href="javascript:void(0);" data-page="' . ($pagination['currentPage'] - 1) . '" aria-label="Previous">';
        $output .= '<span aria-hidden="true">&laquo;</span></a></li>';
        
        // Calculate range of page numbers to display
        $startPage = max(1, $pagination['currentPage'] - 2);
        $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
        
        // Ensure we always show at least 5 pages if available
        if ($endPage - $startPage + 1 < 5) {
            if ($startPage == 1) {
                $endPage = min($pagination['totalPages'], $startPage + 4);
            } elseif ($endPage == $pagination['totalPages']) {
                $startPage = max(1, $endPage - 4);
            }
        }
        
        // Display page numbers
        for ($i = $startPage; $i <= $endPage; $i++) {
            $output .= '<li class="page-item ' . (($i == $pagination['currentPage']) ? 'active' : '') . '">';
            $output .= '<a class="page-link pagination-link" href="javascript:void(0);" data-page="' . $i . '">' . $i . '</a></li>';
        }
        
        // Next page button
        $output .= '<li class="page-item ' . (($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '') . '">';
        $output .= '<a class="page-link pagination-link" href="javascript:void(0);" data-page="' . ($pagination['currentPage'] + 1) . '" aria-label="Next">';
        $output .= '<span aria-hidden="true">&raquo;</span></a></li>';
        
        $output .= '</ul></nav></td></tr>';
    }
    
    return $output;
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
 * Count total products matching search criteria
 * 
 * @param array|null $searchParams Search parameters
 * @return int Number of matching products
 */
function countProducts(?array $searchParams = null): int
{
    global $pdo;
    
    // Prepare search term
    $trimmedSearch = trim($searchParams['search'] ?? '');
    $searchTerm = '%' . $trimmedSearch . '%';

    // Get category filter
    $categoryFilter = !empty($searchParams['category']) && $searchParams['category'] !== 'all' 
                    ? $searchParams['category'] : null;

    // Build SQL query
    $sql = "SELECT COUNT(DISTINCT p.prod_id) as total
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            JOIN category cat ON p.category_id = cat.category_id";
    
    // Add WHERE clause only if we have search parameters
    $params = [];
    
    if (!empty($trimmedSearch) || $categoryFilter !== null) {
        $sql .= " WHERE";
        
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
    
    try {
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return (int)$result->total;
    } catch (PDOException $e) {
        error_log("Database error in countProducts: " . $e->getMessage());
        return 0;
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
                    
    // Pagination parameters
    $page = isset($searchParams['page']) ? max(1, intval($searchParams['page'])) : 1;
    $limit = isset($searchParams['limit']) ? max(1, intval($searchParams['limit'])) : 10; // Default to 10 for index, 20 for admin
    $offset = ($page - 1) * $limit;

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
    
    // Add limit and offset for pagination
    $countSql = $sql; // Save the query for counting total results
    $sql .= " LIMIT :limit OFFSET :offset";
    
    try {
        // First, count total results for pagination
        $countStmt = $pdo->prepare($countSql);
        
        // Bind parameters for count query
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $countStmt->execute();
        $totalResults = $countStmt->rowCount();
        
        // Now get the paginated results
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        // Bind pagination parameters
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Add pagination metadata to the first result if there are any results
        if (!empty($results)) {
            $results[0]->pagination = [
                'totalResults' => $totalResults,
                'currentPage' => $page,
                'totalPages' => ceil($totalResults / $limit),
                'limit' => $limit
            ];
        }
        
        return $results;
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
            <tr class="clickable-row" data-href="admin/adminsingleproduct.php?id=<?= safeEcho($product->prod_id) ?>">
                <td><?= safeEcho($product->prod_id) ?></td>
                <td><?= safeEcho($product->title) ?></td>
                <td><?= safeEcho($authorName) ?></td>
                <td><?= safeEcho($product->category_name) ?></td>
                <td><?= safeEcho($product->shelf_name) ?></td>
                <td><?= safeEcho($formattedPrice) ?></td>
                <td class="<?= $statusClass ?>"><?= safeEcho($statusName) ?></td>
                <td>
                    <div class="btn-group btn-group-sm">

                        
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

// Render the search form for the admin page
if (basename($_SERVER['PHP_SELF']) === 'admin.php' || strpos($_SERVER['REQUEST_URI'], 'admin') !== false) {
    // Get categories for dropdown
    $categories = getCategories($pdo);
    
    // Get current page from GET parameters
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    ?>
    
    <div class="tab-pane fade show active" id="search">
        <form method="GET" action="javascript:void(0);" id="admin-search-form">
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
                        <?php foreach ($categories as $category): ?>
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
            <input type="hidden" name="tab" value="search">
            <input type="hidden" name="page" value="1" id="admin-current-page">
            <input type="hidden" name="limit" value="20">
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
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    
                    // Prepare search parameters
                    $searchParams = [
                        'page' => $page,
                        'limit' => 20
                    ];
                    
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
                        
                        // Add pagination if needed
                        if (!empty($products) && isset($products[0]->pagination) && $products[0]->pagination['totalPages'] > 1) {
                            echo renderPagination($products[0]->pagination, 'admin');
                        }
                    } catch (Exception $e) {
                        echo '<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add JavaScript for AJAX admin search -->
    <script>
    $(document).ready(function() {
        // Handle search form submission with AJAX
        $('#admin-search-form').on('submit', function(e) {
            e.preventDefault();
            performAdminSearch(1); // Page 1 for new searches
        });

        // Handle pagination clicks
        $(document).on('click', '.pagination-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            $('#admin-current-page').val(page);
            performAdminSearch(page);
        });

        // Perform admin search
        function performAdminSearch(page) {
            const searchTerm = $('#search-term').val();
            const category = $('#category-filter').val();
            const limit = 20; // Fixed limit for admin view
            
            // Update the hidden page input
            $('#admin-current-page').val(page);
            
            // Show loading indicator
            $('#inventory-body').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
            
            // Perform AJAX request
            $.ajax({
                url: 'admin/search.php',
                data: {
                    ajax: 'admin',
                    search: searchTerm,
                    category: category,
                    page: page,
                    limit: limit
                },
                type: 'GET',
                success: function(data) {
                    // Update results
                    $('#inventory-body').html(data);
                    
                    // Update URL for bookmarking without page reload
                    const url = new URL(window.location);
                    url.searchParams.set('search', searchTerm);
                    url.searchParams.set('category', category);
                    url.searchParams.set('page', page);
                    url.searchParams.set('limit', limit);
                    url.searchParams.set('tab', 'search');
                    window.history.pushState({}, '', url);
                    
                    // Attach event handlers to action buttons
                    attachActionListeners();
                },
                error: function() {
                    $('#inventory-body').html('<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>');
                }
            });
        }

        // Auto-execute search if there are search parameters in URL
        const urlParams = new URLSearchParams(window.location.search);
        if ((urlParams.has('search') || (urlParams.has('category') && urlParams.get('category') !== 'all')) && urlParams.get('tab') === 'search') {
            // Set form values from URL parameters
            $('#search-term').val(urlParams.get('search') || '');
            $('#category-filter').val(urlParams.get('category') || 'all');
            $('#admin-current-page').val(urlParams.get('page') || '1');
            
            // Attach event handlers to action buttons
            attachActionListeners();
        }
    });
    
    // Handle action buttons
function attachActionListeners() {
    // Quick sell button click - no confirmation
    document.querySelectorAll('.quick-sell').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            changeProductStatus(productId, 2); // 2 = Sold, no confirmation
        });
    });
    
    // Quick return button click - no confirmation
    document.querySelectorAll('.quick-return').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            changeProductStatus(productId, 1); // 1 = Available, no confirmation
        });
    });
}
    
    // Function to perform admin search
    function performAdminSearch(page) {
        const searchTerm = $('#search-term').val();
        const category = $('#category-filter').val();
        const limit = 20; // Fixed limit for admin view
        
        // Update the hidden page input
        $('#admin-current-page').val(page);
        
        // Show loading indicator
        $('#inventory-body').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        
        // Perform AJAX request
        $.ajax({
            url: 'admin/search.php',
            data: {
                ajax: 'admin',
                search: searchTerm,
                category: category,
                page: page,
                limit: limit
            },
            type: 'GET',
            success: function(data) {
                // Update results
                $('#inventory-body').html(data);
                
                // Update URL for bookmarking without page reload
                const url = new URL(window.location);
                url.searchParams.set('search', searchTerm);
                url.searchParams.set('category', category);
                url.searchParams.set('page', page);
                url.searchParams.set('limit', limit);
                url.searchParams.set('tab', 'search');
                window.history.pushState({}, '', url);
                
                // Attach event handlers to action buttons
                attachActionListeners();
            },
            error: function() {
                $('#inventory-body').html('<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>');
            }
        });
    }
    </script>
<?php } ?>

<?php
// Only show the admin search form on AJAX requests or when explicitly requested
if (isset($_GET['ajax']) || (basename($_SERVER['PHP_SELF']) === 'admin.php' && !isset($_GET['include_only']))):
?>
<!-- Search form HTML here -->
<?php endif; ?>