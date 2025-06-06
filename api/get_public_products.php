<?php
/**
 * Get Public Products - Secured Version with Smart Search
 *
 * Server-side script to handle product data for public index page with enhanced security.
 * Features smart multi-field search functionality and 1000 product limit when no filters applied.
 * Implements proper input validation, output sanitization, and error handling.
 *
 * @package     KarisAntikvariat
 * @subpackage  API
 * @author      Axxell
 * @version     1.3
 * @since       2024-01-01
 */

require_once dirname(__DIR__) . '/init.php';

/**
 * Smart search function that handles multi-field searches intelligently
 * 
 * Provides intelligent search across multiple database fields with word splitting
 * and combination strategies to find relevant products.
 * 
 * @param string $searchTerm The search term to process
 * @param string $language Current language ('sv' or 'fi')
 * @return array Array containing WHERE clause and parameters for prepared statement
 * @throws InvalidArgumentException If invalid language provided
 */
function buildSmartSearch($searchTerm, $language = 'sv') {
    // Validate language parameter
    $allowedLanguages = ['sv', 'fi'];
    if (!in_array($language, $allowedLanguages)) {
        throw new InvalidArgumentException('Invalid language parameter');
    }
    
    // Sanitize and validate search term
    $searchTerm = sanitizeInput($searchTerm, 'string', 500);
    if (empty($searchTerm)) {
        return ['where' => '', 'params' => []];
    }
    
    // Split search term into words and filter empty elements
    $words = preg_split('/\s+/', $searchTerm);
    $words = array_filter($words, function($word) {
        return strlen(trim($word)) >= 1;
    });
    
    if (empty($words)) {
        return ['where' => '', 'params' => []];
    }
    
    $conditions = [];
    $params = [];
    
    // Determine language field suffix
    $langSuffix = ($language === 'fi') ? 'fi' : 'sv';
    
    // Strategy 1: Exact phrase search across all searchable fields
    $conditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.publisher LIKE ? OR c.category_{$langSuffix}_name LIKE ? OR g.genre_{$langSuffix}_name LIKE ?)";
    $exactPhrase = '%' . $searchTerm . '%';
    for ($i = 0; $i < 6; $i++) {
        $params[] = $exactPhrase;
    }
    
    // Strategy 2: Multi-word intelligent combinations (author + title)
    if (count($words) > 1) {
        // Try different word splits: first N words as author, rest as title
        for ($split = 1; $split < count($words); $split++) {
            $authorPart = implode(' ', array_slice($words, 0, $split));
            $titlePart = implode(' ', array_slice($words, $split));
            
            $conditions[] = "(a.author_name LIKE ? AND p.title LIKE ?)";
            $params[] = '%' . $authorPart . '%';
            $params[] = '%' . $titlePart . '%';
        }
        
        // Try reverse combinations: title first, then author
        for ($split = 1; $split < count($words); $split++) {
            $titlePart = implode(' ', array_slice($words, 0, $split));
            $authorPart = implode(' ', array_slice($words, $split));
            
            $conditions[] = "(p.title LIKE ? AND a.author_name LIKE ?)";
            $params[] = '%' . $titlePart . '%';
            $params[] = '%' . $authorPart . '%';
        }
        
        // Strategy 3: All words must appear somewhere (flexible matching)
        $allWordsConditions = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) >= 2) { // Skip very short words to improve performance
                $allWordsConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.publisher LIKE ? OR c.category_{$langSuffix}_name LIKE ? OR g.genre_{$langSuffix}_name LIKE ?)";
                $wordParam = '%' . $word . '%';
                for ($i = 0; $i < 6; $i++) {
                    $params[] = $wordParam;
                }
            }
        }
        
        if (!empty($allWordsConditions)) {
            $conditions[] = "(" . implode(" AND ", $allWordsConditions) . ")";
        }
    }
    
    $whereClause = "(" . implode(" OR ", $conditions) . ")";
    
    return ['where' => $whereClause, 'params' => $params];
}

/**
 * Check if any search or filter parameters are applied
 * 
 * @param string $search Search term
 * @param string $category Category filter
 * @param bool $isSalePageRequest Whether this is from the sale page
 * @return bool True if filters are applied
 */
function hasFiltersApplied($search, $category, $isSalePageRequest) {
    // Search term counts as filter
    if (!empty(trim($search))) {
        return true;
    }
    
    // Category filter (excluding 'all')
    if (!empty($category) && $category !== 'all') {
        return true;
    }
    
    // Sale page special price filter
    if ($isSalePageRequest) {
        return true;
    }
    
    return false;
}

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

// Sanitize and validate input parameters
$search = sanitizeInput($_GET['search'] ?? '', 'string', 500);
$category = sanitizeInput($_GET['category'] ?? '', 'string', 50);
$page = sanitizeInput($_GET['page'] ?? 1, 'int', null, ['min' => 1, 'max' => 1000]);
$limit = sanitizeInput($_GET['limit'] ?? 25, 'int', null, ['min' => 1, 'max' => 200]);
$sort = sanitizeInput($_GET['sort'] ?? 'title', 'string', 50);
$order = sanitizeInput($_GET['order'] ?? 'asc', 'string', 4);
$randomSamples = isset($_GET['random_samples']) && $_GET['random_samples'] === 'true';

// Validate sort order parameter
if (!in_array(strtolower($order), ['asc', 'desc'])) {
    $order = 'asc';
}

// Check for special_price parameter from sale.php
$isSalePageRequest = isset($_GET['special_price']) && $_GET['special_price'] === '1';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get and validate language from session
$language = isset($_SESSION['language']) && in_array($_SESSION['language'], ['sv', 'fi']) 
    ? $_SESSION['language'] 
    : 'sv';

// Create formatter instance with appropriate locale
try {
    $formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');
} catch (Exception $e) {
    // Fallback to Swedish if formatter fails
    $formatter = new Formatter('sv_SE');
}

// Maximum products limit for unfiltered requests
const MAX_PRODUCTS_WITHOUT_FILTERS = 1000;

try {
    // Handle random samples request
    if ($randomSamples && empty($search) && ($category === 'all' || empty($category)) && !$isSalePageRequest) {
        $sampleProducts = getRandomSampleProducts($pdo, 2, $language);
    
        // Calculate pagination for samples
        $totalItems = count($sampleProducts);
        $totalPages = ceil($totalItems / $limit);
        $offset = ($page - 1) * $limit;
        $paginatedSamples = array_slice($sampleProducts, $offset, $limit);
    
        // Format and render products
        $formattedProducts = formatProductsData($paginatedSamples, $formatter);
        $html = renderProductsHTML($formattedProducts, false);
    
        // Prepare and send response
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
    
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        return;
    }

    // Check if filters are applied
    $filtersApplied = hasFiltersApplied($search, $category, $isSalePageRequest);

    // Build main SQL query for public products
    $langField = ($language === 'fi') ? 'fi' : 'sv';
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
                c.category_{$langField}_name as category_name,
                p.category_id,
                GROUP_CONCAT(DISTINCT g.genre_{$langField}_name SEPARATOR ', ') AS genre_names,
                con.condition_{$langField}_name as condition_name,
                p.price,
                IFNULL(l.language_{$langField}_name, '') as language,
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
    
    // Build WHERE conditions with parameters
    $whereConditions = [];
    $params = [];
    
    // Always show only available products
    $whereConditions[] = "p.status = 1";

    // Add special price condition for sale page
    if ($isSalePageRequest) {
        $whereConditions[] = "p.special_price = 1";
    }

    // Apply smart search if search term provided
    if (!empty($search)) {
        $searchResult = buildSmartSearch($search, $language);
        if (!empty($searchResult['where'])) {
            $whereConditions[] = $searchResult['where'];
            $params = array_merge($params, $searchResult['params']);
        }
    }
    
    // Apply category filter
    if (!empty($category) && $category !== 'all') {
        $categoryId = sanitizeInput($category, 'int');
        if ($categoryId > 0) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $categoryId;
        }
    }
    
    // Add WHERE clause to SQL
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add GROUP BY clause
    $sql .= " GROUP BY p.prod_id";
    
    // Add ORDER BY clause with validation
    $allowedSortColumns = ['title', 'author_name', 'category_name', 'genre_names', 'condition_name', 'price', 'date_added'];
    if (!in_array($sort, $allowedSortColumns)) {
        $sort = 'title';
    }
    
    $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    $sql .= " ORDER BY {$sort} {$orderDirection}";
    
    // Build count query for pagination
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
        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    $actualTotalItems = (int)$stmt->fetchColumn();
    
    // Apply 1000 item limit for unfiltered requests
    $totalItems = $actualTotalItems;
    $limitApplied = false;
    
    if (!$filtersApplied && $actualTotalItems > MAX_PRODUCTS_WITHOUT_FILTERS) {
        $totalItems = MAX_PRODUCTS_WITHOUT_FILTERS;
        $limitApplied = true;
        
        // Calculate offset and limit with 1000 cap
        $maxOffset = ($page - 1) * $limit;
        if ($maxOffset >= MAX_PRODUCTS_WITHOUT_FILTERS) {
            $sql .= " LIMIT 0";
        } else {
            $remainingItems = MAX_PRODUCTS_WITHOUT_FILTERS - $maxOffset;
            $actualLimit = min($limit, $remainingItems);
            $sql .= " LIMIT " . $maxOffset . ", " . $actualLimit;
        }
    } else {
        // Normal pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT " . $offset . ", " . $limit;
    }

    // Execute main query
    $stmt = $pdo->prepare($sql);
    if (!empty($params)) {
        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format product data
    $formattedProducts = formatProductsData($products, $formatter);
    
    // Generate HTML output
    $html = renderProductsHTML($formattedProducts, $isSalePageRequest);
    
    // Calculate pagination information
    $totalPages = ceil($totalItems / $limit);
    $firstRecord = $totalItems > 0 ? (($page - 1) * $limit) + 1 : 0;
    $lastRecord = min($totalItems, $page * $limit);
    
    // Prepare successful response
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
            'limitApplied' => $limitApplied,
            'actualTotalItems' => $actualTotalItems
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (InvalidArgumentException $e) {
    // Handle validation errors
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Ogiltiga parametrar angavs'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ett fel inträffade vid databasfrågan'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Handle general errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ett oväntat fel inträffade'
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Get random sample products from each category
 *
 * Retrieves a specified number of random products from each category
 * for display when no specific search criteria are applied.
 *
 * @param PDO $pdo Database connection
 * @param int $samplesPerCategory Number of samples to get from each category
 * @param string $language Current language ('sv' or 'fi')
 * @return array Sample products array
 * @throws PDOException If database query fails
 */
function getRandomSampleProducts(PDO $pdo, int $samplesPerCategory = 2, string $language = 'sv'): array {
    // Validate parameters
    $samplesPerCategory = max(1, min($samplesPerCategory, 10)); // Limit to reasonable range
    $language = in_array($language, ['sv', 'fi']) ? $language : 'sv';
    
    try {
        // Get all available categories
        $stmtCategories = $pdo->query("SELECT category_id FROM category ORDER BY category_id");
        $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);
        
        $sampleProducts = [];
        $langField = ($language === 'fi') ? 'fi' : 'sv';
        
        // Get sample products from each category
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
                        c.category_{$langField}_name as category_name,
                        co.condition_{$langField}_name as condition_name,
                        GROUP_CONCAT(DISTINCT g.genre_{$langField}_name SEPARATOR ', ') AS genre_names
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
            $stmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $samplesPerCategory, PDO::PARAM_INT);
            $stmt->execute();
            
            $categoryProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $sampleProducts = array_merge($sampleProducts, $categoryProducts);
        }
        
        return $sampleProducts;
        
    } catch (PDOException $e) {
        throw new PDOException('Failed to fetch sample products: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

/**
 * Format products data for consistent output
 *
 * Processes raw product data from database and formats it for display,
 * including price formatting and boolean flag processing.
 *
 * @param array $products Raw products data from database
 * @param Formatter $formatter Formatter instance for price/date formatting
 * @return array Formatted products data
 */
function formatProductsData(array $products, Formatter $formatter): array {
    foreach ($products as &$product) {
        // Format price using formatter
        $product['formatted_price'] = $formatter->formatPrice($product['price'] ?? 0);
        
        // Format date if available
        if (isset($product['date_added']) && !empty($product['date_added'])) {
            $product['formatted_date'] = $formatter->formatDate($product['date_added']);
        } else {
            $product['formatted_date'] = '';
        }
        
        // Process boolean flags for display
        $product['is_special'] = isset($product['special_price']) && (int)$product['special_price'] === 1;
        $product['is_rare'] = isset($product['rare']) && (int)$product['rare'] === 1;
        $product['is_recommended'] = isset($product['recommended']) && (int)$product['recommended'] === 1;
        
        // Ensure all string fields are properly handled for null values
        $product['title'] = $product['title'] ?? '';
        $product['author_name'] = $product['author_name'] ?? '';
        $product['category_name'] = $product['category_name'] ?? '';
        $product['genre_names'] = $product['genre_names'] ?? '';
        $product['condition_name'] = $product['condition_name'] ?? '';
        $product['notes'] = $product['notes'] ?? '';
        $product['publisher'] = $product['publisher'] ?? '';
    }
    
    return $products;
}

/**
 * Render HTML for the products table
 *
 * Generates HTML table rows for product display in both desktop and mobile views.
 *
 * @param array $products Formatted products data
 * @param bool $isSalePage If true, hide the badges column for sale page layout
 * @return string HTML string for table rows
 */
function renderProductsHTML(array $products, bool $isSalePage = false): string {
    ob_start();
    
    if (empty($products)) {
        $colspan = $isSalePage ? 6 : 7;
        echo '<tr><td colspan="' . $colspan . '" class="text-center py-3">Inga produkter hittades.</td></tr>';
    } else {
        foreach ($products as $product) {
            renderPublicProductRow($product, $isSalePage);
        }
    }
    
    return ob_get_clean();
}

/**
 * Render a single product row for the public view
 *
 * Outputs HTML for both desktop table row and mobile card view of a product.
 * Includes proper output sanitization for all dynamic content.
 *
 * @param array $product Product data array
 * @param bool $isSalePage If true, do not render the badges column
 * @return void
 */
function renderPublicProductRow(array $product, bool $isSalePage = false): void {
    // Validate product ID
    $productId = sanitizeInput($product['prod_id'] ?? 0, 'int');
    if ($productId <= 0) {
        return; // Skip invalid products
    }
    
    // Format price with fallback for null values
    if (isset($product['price']) && $product['price'] !== null && $product['price'] > 0) {
        $displayPrice = number_format((float)$product['price'], 2, ',', ' ') . ' €';
    } else {
        $displayPrice = '<span class="text-muted">Pris på förfrågan</span>';
    }
    
    $productUrl = "singleproduct.php?id=" . $productId;
    ?>
    <!-- Desktop Table Row -->
    <tr class="clickable-row d-none d-md-table-row" data-href="<?= safeEcho($productUrl) ?>">
        <td data-label="Titel"><?= safeEcho($product['title']) ?></td>
        <td data-label="Författare/Artist"><?= safeEcho($product['author_name']) ?></td>
        <td data-label="Kategori"><?= safeEcho($product['category_name']) ?></td>
        <td data-label="Genre"><?= safeEcho($product['genre_names']) ?></td>
        <td data-label="Skick"><?= safeEcho($product['condition_name']) ?></td>
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
                    <span style="color: grey;"><?= safeEcho($product['author_name'] ?: 'Ej angivet') ?></span><br>
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