<?php
/**
 * Get Public Products
 *
 * Server-side script to handle product data for public index page
 * Modified to include 1000 product limit when no filters are applied
 *
 * @package     KarisAntikvariat
 * @subpackage  API
 * @author      Axxell
 * @version     1.1
 */

require_once dirname(__DIR__) . '/init.php';

// Set header to JSON
header('Content-Type: application/json');

// Get parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 200) : 25; // Hard cap of 200
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'desc' : 'asc';
$randomSamples = isset($_GET['random_samples']) && $_GET['random_samples'] === 'true';

// Check for special_price parameter from sale.php
$isSalePageRequest = isset($_GET['special_price']) && $_GET['special_price'] === '1';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get language from session or default to Swedish
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Create formatter instance with appropriate locale
$formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');

// MAXIMUM PRODUCTS LIMIT - only applies when no filters are used
const MAX_PRODUCTS_WITHOUT_FILTERS = 1000;

/**
 * Check if any filters are applied
 * 
 * @param string $search Search term
 * @param string $category Category filter
 * @param bool $isSalePageRequest Whether this is from the sale page
 * @return bool True if filters are applied
 */
function hasFiltersApplied($search, $category, $isSalePageRequest) {
    // If there's a search term, filters are applied
    if (!empty($search)) {
        return true;
    }
    
    // If category is not empty and not 'all', filters are applied
    if (!empty($category) && $category !== 'all') {
        return true;
    }
    
    // If this is from the sale page (special_price filter), filters are applied
    if ($isSalePageRequest) {
        return true;
    }
    
    return false;
}

try {
    if ($randomSamples && empty($search) && ($category === 'all' || empty($category)) && !$isSalePageRequest) {
        // Fetch random samples
        $sampleProducts = getRandomSampleProducts($pdo, 2, $language);
    
        // Calculate total items and paginate
        $totalItems = count($sampleProducts);
        $totalPages = ceil($totalItems / $limit);
        $offset = ($page - 1) * $limit;
        $paginatedSamples = array_slice($sampleProducts, $offset, $limit);
    
        // Format the product data
        $formattedProducts = formatProductsData($paginatedSamples, $formatter);
    
        // Generate HTML for products
        $html = renderProductsHTML($formattedProducts, false);
    
        // Prepare response
        $response = [
            'success' => true,
            'items' => $formattedProducts,
            'html' => $html,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
                'itemsPerPage' => $limit,
                'firstRecord' => $totalItems > 0 ? $offset + 1 : 0,
                'lastRecord' => min($totalItems, $page * $limit),
                'pageSizeOptions' => [10, 25, 50, 100, 200]
            ]
        ];
    
        // Output JSON response
        echo json_encode($response);
        return;
    }

    // Check if filters are applied
    $filtersApplied = hasFiltersApplied($search, $category, $isSalePageRequest);

    // Build SQL query for public products
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
                c.category_" . ($language === 'fi' ? 'fi' : 'sv') . "_name as category_name,
                p.category_id,
                GROUP_CONCAT(DISTINCT g.genre_" . ($language === 'fi' ? 'fi' : 'sv') . "_name SEPARATOR ', ') AS genre_names,
                con.condition_" . ($language === 'fi' ? 'fi' : 'sv') . "_name as condition_name,
                p.price,
                IFNULL(l.language_" . ($language === 'fi' ? 'fi' : 'sv') . "_name, '') as language,
                p.year,
                p.publisher,
                p.notes,
                p.special_price,
                p.rare,
                p.recommended,
                p.date_added
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            JOIN category c ON p.category_id = c.category_id
            LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
            LEFT JOIN genre g ON pg.genre_id = g.genre_id
            LEFT JOIN `condition` con ON p.condition_id = con.condition_id
            LEFT JOIN `language` l ON p.language_id = l.language_id";
    
    // Add WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Always show only available products (status = 1)
    $whereConditions[] = "p.status = 1";

    // Add special_price condition if the request came from the sale page
    if ($isSalePageRequest) {
        $whereConditions[] = "p.special_price = 1";
    }

    // Search condition
    if (!empty($search)) {
        $whereConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.publisher LIKE ? OR c.category_" . ($language === 'fi' ? 'fi' : 'sv') . "_name LIKE ? OR g.genre_" . ($language === 'fi' ? 'fi' : 'sv') . "_name LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Category condition
    if (!empty($category) && $category !== 'all') {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $category;
    }
    
    // Construct the full WHERE clause
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add GROUP BY clause
    $sql .= " GROUP BY p.prod_id";
    
    // Add ORDER BY clause with validation
    if (!empty($sort)) {
        // Sanitize sort column to prevent SQL injection
        $allowedSortColumns = ['title', 'author_name', 'category_name', 'genre_names', 'condition_name', 'price', 'date_added'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'title';
        }
        
        $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY {$sort} {$orderDirection}";
    } else {
        $sql .= " ORDER BY title ASC";
    }
    
    // Build SQL for counting total items
    $countSql = "SELECT COUNT(DISTINCT p.prod_id) 
                 FROM product p
                 LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                 LEFT JOIN author a ON pa.author_id = a.author_id
                 JOIN category c ON p.category_id = c.category_id
                 LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
                 LEFT JOIN genre g ON pg.genre_id = g.genre_id
                 LEFT JOIN `condition` con ON p.condition_id = con.condition_id
                 LEFT JOIN `language` l ON p.language_id = l.language_id";
    
    // Add WHERE conditions to count query
    if (!empty($whereConditions)) {
        $countSql .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Execute count query
    $stmt = $pdo->prepare($countSql);
    if (!empty($params)) {
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
    }
    $stmt->execute();
    $actualTotalItems = $stmt->fetchColumn();
    
    // Apply the 1000 limit if no filters are applied
    $totalItems = $actualTotalItems;
    $limitApplied = false;
    
    if (!$filtersApplied && $actualTotalItems > MAX_PRODUCTS_WITHOUT_FILTERS) {
        $totalItems = MAX_PRODUCTS_WITHOUT_FILTERS;
        $limitApplied = true;
        
        // Add LIMIT to the main query to respect the 1000 limit
        $maxOffset = ($page - 1) * $limit;
        if ($maxOffset >= MAX_PRODUCTS_WITHOUT_FILTERS) {
            // If trying to access beyond the limit, show empty results
            $sql .= " LIMIT 0";
        } else {
            // Calculate how many items we can actually fetch
            $remainingItems = MAX_PRODUCTS_WITHOUT_FILTERS - $maxOffset;
            $actualLimit = min($limit, $remainingItems);
            $sql .= " LIMIT " . $maxOffset . ", " . $actualLimit;
        }
    } else {
        // Normal pagination without the 1000 limit
        $sql .= " LIMIT " . (($page - 1) * $limit) . ", " . $limit;
    }

    // Execute main query
    $stmt = $pdo->prepare($sql);
    if (!empty($params)) {
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process products to format data
    $formattedProducts = formatProductsData($products, $formatter);
    
    // Generate HTML for products
    $html = renderProductsHTML($formattedProducts, $isSalePageRequest);
    
    // Calculate pagination info
    $totalPages = ceil($totalItems / $limit);
    $firstRecord = $totalItems > 0 ? (($page - 1) * $limit) + 1 : 0;
    $lastRecord = min($totalItems, $page * $limit);
    
    // Prepare response
    $response = [
        'success' => true,
        'items' => $formattedProducts,
        'html' => $html,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'itemsPerPage' => $limit,
            'firstRecord' => $firstRecord,
            'lastRecord' => $lastRecord,
            'pageSizeOptions' => [10, 25, 50, 100, 200],
            'sort' => $sort,
            'order' => $order,
            'limitApplied' => $limitApplied, // to inform frontend
            'actualTotalItems' => $actualTotalItems // to show real total
        ]
    ];
    
    // Output JSON response
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log the error
    error_log('API Error in get_public_products.php: ' . $e->getMessage());
    
    // Send error response
    echo json_encode([
        'success' => false,
        'message' => 'Ett fel inträffade: ' . $e->getMessage()
    ]);
}


/**
 * Get random sample products from each category
 *
 * @param PDO $pdo Database connection
 * @param int $samplesPerCategory Number of samples to get from each category
 * @param string $language Current language ('sv' or 'fi')
 * @return array Sample products
 */
function getRandomSampleProducts(PDO $pdo, int $samplesPerCategory = 2, string $language = 'sv'): array {
    try {
        // Get all categories first
        $stmtCategories = $pdo->query("SELECT category_id FROM category");
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        
        $sampleProducts = [];
        
        // For each category, get sample products
        foreach ($categories as $categoryId) {
            $sql = "SELECT 
                        p.prod_id, 
                        p.title, 
                        p.status,
                        p.category_id,
                        p.price,
                        p.special_price,
                        p.rare,
                        p.recommended,
                        p.date_added,
                        GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
                        c.category_" . ($language === 'fi' ? 'fi' : 'sv') . "_name as category_name,
                        co.condition_" . ($language === 'fi' ? 'fi' : 'sv') . "_name as condition_name,
                        GROUP_CONCAT(DISTINCT g.genre_" . ($language === 'fi' ? 'fi' : 'sv') . "_name SEPARATOR ', ') AS genre_names
                    FROM product p
                    LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                    LEFT JOIN author a ON pa.author_id = a.author_id
                    JOIN category c ON p.category_id = c.category_id
                    LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
                    LEFT JOIN genre g ON pg.genre_id = g.genre_id
                    LEFT JOIN `condition` co ON p.condition_id = co.condition_id
                    WHERE p.status = 1 AND p.category_id = :category_id
                    GROUP BY p.prod_id
                    ORDER BY RAND()
                    LIMIT :limit";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $samplesPerCategory, PDO::PARAM_INT);
            $stmt->execute();
            
            $categoryProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $sampleProducts = array_merge($sampleProducts, $categoryProducts);
        }
        
        return $sampleProducts;
    } catch (PDOException $e) {
        error_log('Error fetching sample products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Format products data for consistent output
 *
 * @param array $products Raw products data from database
 * @param Formatter $formatter Formatter instance
 * @return array Formatted products data
 */
function formatProductsData(array $products, Formatter $formatter): array {
    foreach ($products as &$product) {
        // Format price
        $product['formatted_price'] = $formatter->formatPrice($product['price']);
        
        // Format date if it exists
        if (isset($product['date_added'])) {
            $product['formatted_date'] = $formatter->formatDate($product['date_added']);
        } else {
            $product['formatted_date'] = '';
        }
        
        // Format flags for display
        $product['is_special'] = isset($product['special_price']) && (int)$product['special_price'] === 1;
        $product['is_rare'] = isset($product['rare']) && (int)$product['rare'] === 1;
        $product['is_recommended'] = isset($product['recommended']) && (int)$product['recommended'] === 1;
    }
    
    return $products;
}

/**
 * Render HTML for the products table
 *
 * @param array $products Formatted products data
 * @param bool $isSalePage If true, hide the last column (badges/details button)
 * @return string HTML for the products table
 */
function renderProductsHTML(array $products, bool $isSalePage = false): string {
    ob_start();
    
    if (empty($products)) {
        // Adjust colspan based on whether it's the sale page or not
        echo '<tr><td colspan="' . ($isSalePage ? 6 : 7) . '" class="text-center py-3">Inga produkter hittades.</td></tr>';
    } else {
        foreach ($products as $product) {
            renderPublicProductRow($product, $isSalePage);
        }
    }
    
    return ob_get_clean();
}

/**
 * Render a product row for the public view
 *
 * @param array $product Product data
 * @param bool $isSalePage If true, do not render the badges/details button column
 * @return void
 */
function renderPublicProductRow(array $product, bool $isSalePage = false): void {
    // Format the price using a fallback if price is null
    $displayPrice = isset($product['price']) && $product['price'] !== null 
                    ? number_format((float)$product['price'], 2, ',', ' ') . ' €' 
                    : '<span class="text-muted">Pris på förfrågan</span>';
    
    $productUrl = "singleproduct.php?id=" . $product['prod_id'];
    ?>
    <!-- Desktop Table Row -->
    <tr class="clickable-row d-none d-md-table-row" data-href="<?= safeEcho($productUrl) ?>">
        <td data-label="Titel"><?= safeEcho($product['title']) ?></td>
        <td data-label="Författare/Artist"><?= safeEcho($product['author_name'] ?? '') ?></td>
        <td data-label="Kategori"><?= safeEcho($product['category_name'] ?? '') ?></td>
        <td data-label="Genre"><?= safeEcho($product['genre_names'] ?? '') ?></td>
        <td data-label="Skick"><?= safeEcho($product['condition_name'] ?? '') ?></td>
        <td data-label="Pris"><?= $displayPrice ?></td>
        <?php if (!$isSalePage): ?> 
        <td onclick="event.stopPropagation();">
            <?php if (isset($product['is_special']) && $product['is_special']): ?>
                <span class="badge bg-danger">Rea</span>
            <?php endif; ?>
            <?php if (isset($product['is_rare']) && $product['is_rare']): ?>
                <span class="badge bg-warning text-dark">Sällsynt</span>
            <?php endif; ?>
            <?php if (isset($product['is_recommended']) && $product['is_recommended']): ?>
                <span class="badge bg-info">Rekommenderad</span>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>

    <!-- Mobile Card -->
    <a href="<?= safeEcho($productUrl) ?>" class="text-decoration-none">
        <div class="card d-block d-md-none mb-3">
            <div class="card-body">
                <h5 class="card-title" style="color: black; font-weight: bold;"><?= safeEcho($product['title']) ?></h5>
                <p class="card-text">
                    <span style="color: grey;"><?= safeEcho($product['author_name'] ?? 'Ej angivet') ?></span><br>
                    <span style="color: #2e8b57; font-weight: bold;"><?= $displayPrice ?></span>
                </p>
                <?php if (!$isSalePage): ?>
                    <div class="mt-2">
                        <?php if (isset($product['is_special']) && $product['is_special']): ?>
                            <span class="badge bg-danger me-1">Rea</span>
                        <?php endif; ?>
                        <?php if (isset($product['is_rare']) && $product['is_rare']): ?>
                            <span class="badge bg-warning text-dark me-1">Sällsynt</span>
                        <?php endif; ?>
                        <?php if (isset($product['is_recommended']) && $product['is_recommended']): ?>
                            <span class="badge bg-info">Rekommenderad</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php
}
?>