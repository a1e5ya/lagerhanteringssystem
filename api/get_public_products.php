<?php
/**
 * Get Public Products
 * 
 * Server-side script to handle product data for public index page
 * 
 * @package    KarisAntikvariat
 * @subpackage API
 * @author     Axxell
 * @version    1.0
 */

require_once dirname(__DIR__) . '/init.php';

// Set header to JSON
header('Content-Type: application/json');

// Get parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'desc' : 'asc';
$randomSamples = isset($_GET['random_samples']) && $_GET['random_samples'] === 'true';

// Start session if not already started to get language preference
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get language from session or default to Swedish
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Create formatter instance with appropriate locale
$formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');

try {
    // Check if we should return random samples (initial page load)
    if ($randomSamples && empty($search) && ($category === 'all' || empty($category))) {
        // Get random samples from each category
        $sampleProducts = getRandomSampleProducts($pdo, 2); // 2 samples per category
        
        // Format the products data
        $formattedProducts = [];
        foreach ($sampleProducts as $product) {
            // Format price using Formatter
            $product['formatted_price'] = $formatter->formatPrice($product['price']);
            
            // Format date using Formatter
            $product['formatted_date'] = $formatter->formatDate($product['date_added']);
            
            // Add link to single product page
            $product['href'] = url('singleproduct.php', ['id' => $product['prod_id']]);
            
            // Add to formatted array
            $formattedProducts[] = $product;
        }
        
        // Generate HTML for products
        $html = '';
        if (!empty($formattedProducts)) {
            ob_start();
            foreach ($formattedProducts as $product) {
                // Define the product URL first
                $productUrl = "singleproduct.php?id=" . $product['prod_id'];
                ?>
<tr class="clickable-row" data-href="<?= $productUrl ?>">
                    <td><?= safeEcho($product['title']) ?></td>
                    <td><?= safeEcho($product['author_name']) ?></td>
                    <td><?= safeEcho($product['category_name']) ?></td>
                    <td><?= safeEcho($product['genre_names']) ?></td>
                    <td><?= safeEcho($product['condition_name']) ?></td>
                    <td>
                        <?php if ($product['price'] !== null): ?>
                            <?= safeEcho($product['formatted_price']) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int)$product['special_price'] === 1): ?>
                            <span class="badge bg-danger">Rea</span>
                        <?php endif; ?>
                        <?php if ((int)$product['rare'] === 1): ?>
                            <span class="badge bg-warning text-dark">Sällsynt</span>
                        <?php endif; ?>
                        <?php if ((int)$product['recommended'] === 1): ?>
                            <span class="badge bg-info">Rekommenderas</span>
                        <?php endif; ?>
                        <a class="btn btn-success d-block d-md-none" href="<?= $productUrl ?>">Visa detaljer</a>
                    </td>
                </tr>
                <?php
            }
            $html = ob_get_clean();
        
        } else {
            $html = '<tr><td colspan="7" class="text-center">Inga produkter hittades</td></tr>';
        }
        
        // Set total items in paginator
        $totalItems = count($formattedProducts);
        
        // Calculate pagination info
        $totalPages = ceil($totalItems / $limit);
        $firstRecord = $totalItems > 0 ? (($page - 1) * $limit) + 1 : 0;
        $lastRecord = min($totalItems, $page * $limit);
        
        // Prepare response with formatted sample products
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
                'pageSizeOptions' => [10, 20, 50, 100]
            ]
        ];
        
        // Output JSON response
        echo json_encode($response);
        return;
    }

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
    
    // Add WHERE clause
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
    
    // Add GROUP BY clause
    $sql .= " GROUP BY p.prod_id";
    
    // Add ORDER BY clause with validation
    if (!empty($sort)) {
        // Sanitize sort column to prevent SQL injection
        $allowedSortColumns = ['title', 'author_name', 'category_name', 'genre_names', 'condition_name', 'price', 'date_added'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'title';
        }
        
        // Use appropriate sort direction
        $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql .= " ORDER BY {$sort} {$orderDirection}";
    } else {
        // Default sort
        $sql .= " ORDER BY title ASC";
    }
    
    // Create a copy of the SQL for counting total (without LIMIT)
    $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as counted";
    
    // Add LIMIT clause for pagination
    $sql .= " LIMIT " . (($page - 1) * $limit) . ", " . $limit;
    
    // Execute count query
    $stmt = $pdo->prepare($countSql);
    if (!empty($params)) {
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
    }
    $stmt->execute();
    $totalItems = $stmt->fetchColumn();
    
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
    $formattedProducts = [];
    foreach ($products as $product) {
        // Format price using Formatter
        $product['formatted_price'] = $formatter->formatPrice($product['price']);
        
        // Format date using Formatter
        $product['formatted_date'] = $formatter->formatDate($product['date_added']);
        
        // Add link to single product page
        $product['href'] = url('singleproduct.php', ['id' => $product['prod_id']]);
        
        // Add to formatted array
        $formattedProducts[] = $product;
    }
    
    // Generate HTML for products (for direct insertion into the page)
    $html = '';
    if (!empty($formattedProducts)) {
        ob_start();
        foreach ($formattedProducts as $product) {
            ?>
            <tr class="clickable-row" data-href="<?= safeEcho(url('singleproduct.php', ['id' => $product['prod_id']])) ?>">
                <td><?= safeEcho($product['title']) ?></td>
                <td><?= safeEcho($product['author_name']) ?></td>
                <td><?= safeEcho($product['category_name']) ?></td>
                <td><?= safeEcho($product['genre_names']) ?></td>
                <td><?= safeEcho($product['condition_name']) ?></td>
                <td>
                    <?php if ($product['price'] !== null): ?>
                        <?= safeEcho($product['formatted_price']) ?>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ((int)$product['special_price'] === 1): ?>
                        <span class="badge bg-danger">Rea</span>
                    <?php endif; ?>
                    <?php if ((int)$product['rare'] === 1): ?>
                        <span class="badge bg-warning text-dark">Sällsynt</span>
                    <?php endif; ?>
                    <?php if ((int)$product['recommended'] === 1): ?>
                        <span class="badge bg-info">Rekommenderas</span>
                    <?php endif; ?>
                    <a class="btn btn-success d-block d-md-none" href="<?= safeEcho(url('singleproduct.php', ['id' => $product['prod_id']])) ?>">Visa detaljer</a>
                </td>
            </tr>
            <?php
        }
        $html = ob_get_clean();
    } else {
        $html = '<tr><td colspan="7" class="text-center">Inga produkter hittades</td></tr>';
    }
    
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
            'pageSizeOptions' => [10, 20, 50, 100]
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
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Get random sample products from each category
 * 
 * @param PDO $pdo Database connection
 * @param int $samplesPerCategory Number of samples to get from each category
 * @return array Sample products
 */
function getRandomSampleProducts(PDO $pdo, int $samplesPerCategory = 2): array {
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
                    c.category_sv_name as category_name,
                    co.condition_sv_name as condition_name,
                    GROUP_CONCAT(DISTINCT g.genre_sv_name SEPARATOR ', ') AS genre_names
                FROM product p
                LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN author a ON pa.author_id = a.author_id
                JOIN category c ON p.category_id = c.category_id
                LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
                LEFT JOIN genre g ON pg.genre_id = g.genre_id
                JOIN `condition` co ON p.condition_id = co.condition_id
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
    // Format all products
    foreach ($products as &$product) {
        // Format price
        $product['formatted_price'] = $formatter->formatPrice($product['price']);
        
        // Format date if it exists
        if (isset($product['date_added'])) {
            $product['formatted_date'] = $formatter->formatDate($product['date_added']);
        }
        
        // Format flags for display
        $product['is_special'] = isset($product['special_price']) && $product['special_price'] ? true : false;
        $product['is_rare'] = isset($product['rare']) && $product['rare'] ? true : false;
        $product['is_recommended'] = isset($product['recommended']) && $product['recommended'] ? true : false;
        
        // Format image path
        if (!empty($product['image'])) {
            // Make sure the path is relative
            $product['image'] = str_replace('../', '', $product['image']);
            $product['image_url'] = getBasePath() . '/' . $product['image'];
        } else {
            // Default image based on category
            $defaultImage = 'assets/images/src-book.webp';
            if (isset($product['category_id'])) {
                if ($product['category_id'] == 5) { // CD
                    $defaultImage = 'assets/images/src-cd.webp';
                } elseif ($product['category_id'] == 6) { // Vinyl
                    $defaultImage = 'assets/images/src-vinyl.webp';
                } elseif ($product['category_id'] == 7) { // DVD
                    $defaultImage = 'assets/images/src-dvd.webp';
                } elseif ($product['category_id'] == 8) { // Comics/Magazines
                    $defaultImage = 'assets/images/src-magazine.webp';
                }
            }
            
            $product['image'] = $defaultImage;
            $product['image_url'] = asset('images', basename($defaultImage));
        }
    }
    
    return $products;
}

/**
 * Render HTML for the products table
 * 
 * @param array $products Formatted products data
 * @return string HTML for the products table
 */
function renderProductsHTML(array $products): string {
    ob_start();
    
    if (empty($products)) {
        echo '<tr><td colspan="7" class="text-center py-3">Inga produkter hittades.</td></tr>';
    } else {
        foreach ($products as $product) {
            renderPublicProductRow($product);
        }
    }
    
    return ob_get_clean();
}

/**
 * Render a product row for the public view
 * 
 * @param array $product Product data
 * @return void
 */
function renderPublicProductRow(array $product): void {
    // Format the price using a fallback if formatted_price doesn't exist
    $formattedPrice = $product['formatted_price'] ?? (isset($product['price']) ? number_format($product['price'], 2, ',', ' ') . ' €' : '');
    $productUrl = "singleproduct.php?id=" . $product['prod_id'];
    ?>
<tr class="clickable-row" data-href="<?= $productUrl ?>">
        <td data-label="Titel"><?= safeEcho($product['title']) ?></td>
        <td data-label="Författare/Artist"><?= safeEcho($product['author_name']) ?></td>
        <td data-label="Kategori"><?= safeEcho($product['category_name']) ?></td>
        <td data-label="Genre"><?= safeEcho($product['genre_names']) ?></td>
        <td data-label="Skick"><?= safeEcho($product['condition_name']) ?></td>
        <td data-label="Pris"><?= safeEcho($formattedPrice) ?></td>
        <td onclick="event.stopPropagation();">
            <?php if (isset($product['is_special']) && $product['is_special']): ?>
                <span class="badge bg-danger">Rea</span>
            <?php endif; ?>
            <?php if (isset($product['is_rare']) && $product['is_rare']): ?>
                <span class="badge bg-warning text-dark">Sällsynt</span>
            <?php endif; ?>
            <?php if (isset($product['is_recommended']) && $product['is_recommended']): ?>
                <span class="badge bg-info">Rekommenderas</span>
            <?php endif; ?>
            <a class="btn btn-success d-block d-md-none" href="<?= safeEcho($productUrl) ?>">Visa detaljer</a>
        </td>
    </tr>
    <?php
}

