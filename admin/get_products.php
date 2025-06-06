<?php
/**
 * Get Products for Admin Search and Lists - FIXED VERSION WITH SMART SEARCH
 * 
 * Server-side proxy script to handle API requests for both admin search and lists views
 * Now supports special filters like special_price, rare, recommended
 * Enhanced with smart multi-field search functionality
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.1
 * @since   2024-01-01
 */

require_once dirname(__DIR__) . '/init.php';

/**
 * Smart search function that handles multi-field searches intelligently
 * Example: "Jim Butcher Storm Front" will find books where author contains "Jim Butcher" and title contains "Storm Front"
 */
function buildSmartSearch($searchTerm) {
    if (empty(trim($searchTerm))) {
        return ['where' => '', 'params' => []];
    }
    
    $searchTerm = trim($searchTerm);
    $words = preg_split('/\s+/', $searchTerm);
    $words = array_filter($words); // Remove empty elements
    
    if (empty($words)) {
        return ['where' => '', 'params' => []];
    }
    
    $conditions = [];
    $params = [];
    
    // Strategy 1: Exact phrase search across all fields
    $conditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
    $exactPhrase = '%' . $searchTerm . '%';
    for ($i = 0; $i < 7; $i++) {
        $params[] = $exactPhrase;
    }
    
    // Strategy 2: If multiple words, try smart author + title combinations
    if (count($words) > 1) {
        // Try different splits: first N words as author, rest as title
        for ($split = 1; $split < count($words); $split++) {
            $authorPart = implode(' ', array_slice($words, 0, $split));
            $titlePart = implode(' ', array_slice($words, $split));
            
            $conditions[] = "(a.author_name LIKE ? AND p.title LIKE ?)";
            $params[] = '%' . $authorPart . '%';
            $params[] = '%' . $titlePart . '%';
        }
        
        // Also try reverse: title first, then author
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
            if (strlen($word) >= 2) { // Skip very short words
                $allWordsConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
                $wordParam = '%' . $word . '%';
                for ($i = 0; $i < 7; $i++) {
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

// Get and sanitize parameters
$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
if (strlen($search) > 255) $search = substr($search, 0, 255);

$category = isset($_GET['category']) ? trim((string)$_GET['category']) : '';
if ($category === 'all') $category = '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
if ($limit < 1) $limit = 1;
if ($limit > 100) $limit = 100;

$showAllStatuses = isset($_GET['show_all_statuses']) && $_GET['show_all_statuses'] === 'true';
$status = isset($_GET['status']) ? trim((string)$_GET['status']) : '';

// Handle lists view type and additional parameters
$viewType = isset($_GET['view_type']) ? trim((string)$_GET['view_type']) : 'admin';
$isListsView = ($viewType === 'lists');

// Handle additional filter parameters for lists view
$genre = isset($_GET['genre']) ? trim((string)$_GET['genre']) : '';
$condition = isset($_GET['condition']) ? trim((string)$_GET['condition']) : '';
$shelf = isset($_GET['shelf']) ? trim((string)$_GET['shelf']) : '';
$priceMin = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$priceMax = isset($_GET['price_max']) ? (float)$_GET['price_max'] : 0;
$dateMin = isset($_GET['date_min']) ? trim((string)$_GET['date_min']) : '';
$dateMax = isset($_GET['date_max']) ? trim((string)$_GET['date_max']) : '';

// Handle special filters for lists view
$noPrice = isset($_GET['no_price']) && $_GET['no_price'];
$poorCondition = isset($_GET['poor_condition']) && $_GET['poor_condition'];
$yearThreshold = isset($_GET['year_threshold']) ? (int)$_GET['year_threshold'] : 0;

// Handle special marking filters
$specialPrice = isset($_GET['special_price']) ? (int)$_GET['special_price'] : 0;
$rare = isset($_GET['rare']) ? (int)$_GET['rare'] : 0;
$recommended = isset($_GET['recommended']) ? (int)$_GET['recommended'] : 0;

// Set header to JSON
header('Content-Type: application/json');

try {
    
    // Create SQL query - modified to support lists view
    if ($isListsView) {
        // For lists view, we need additional fields including images
        $sql = "SELECT 
                    p.prod_id, 
                    p.title, 
                    p.status,
                    s.status_sv_name as status_name,
                    p.shelf_id,
                    sh.shelf_sv_name as shelf_name,
                    GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
                    c.category_sv_name as category_name,
                    p.category_id,
                    GROUP_CONCAT(DISTINCT g.genre_sv_name SEPARATOR ', ') AS genre_names,
                    con.condition_sv_name as condition_name,
                    p.price,
                    IFNULL(l.language_sv_name, '') as language,
                    p.year,
                    p.publisher,
                    p.notes,
                    p.internal_notes,
                    p.special_price,
                    p.rare,
                    p.recommended,
                    p.date_added,
                    GROUP_CONCAT(DISTINCT img.image_path SEPARATOR ', ') AS image_paths
                FROM product p
                LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN author a ON pa.author_id = a.author_id
                JOIN category c ON p.category_id = c.category_id
                LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
                LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
                LEFT JOIN genre g ON pg.genre_id = g.genre_id
                LEFT JOIN image img ON p.prod_id = img.prod_id
                LEFT JOIN `condition` con ON p.condition_id = con.condition_id
                LEFT JOIN `status` s ON p.status = s.status_id
                LEFT JOIN `language` l ON p.language_id = l.language_id";
    } else {
        // Use existing query for admin search view
        $sql = "SELECT 
                    p.prod_id, 
                    p.title, 
                    p.status,
                    s.status_sv_name as status_name,
                    p.shelf_id,
                    sh.shelf_sv_name as shelf_name,
                    GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
                    c.category_sv_name as category_name,
                    p.category_id,
                    GROUP_CONCAT(DISTINCT g.genre_sv_name SEPARATOR ', ') AS genre_names,
                    con.condition_sv_name as condition_name,
                    p.price,
                    IFNULL(l.language_sv_name, '') as language,
                    p.year,
                    p.publisher,
                    p.notes,
                    p.internal_notes,
                    p.special_price,
                    p.rare,
                    p.recommended,
                    p.date_added
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
    }
    
    // Add WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Smart search condition - UPDATED SECTION
    if (!empty($search)) {
        $searchResult = buildSmartSearch($search);
        if (!empty($searchResult['where'])) {
            $whereConditions[] = $searchResult['where'];
            $params = array_merge($params, $searchResult['params']);
        }
    }
    
    // Category condition
    if (!empty($category)) {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $category;
    }
    
    if (!empty($genre)) {
        $whereConditions[] = "g.genre_sv_name = ?";
        $params[] = $genre;
    }
    
    if (!empty($condition)) {
        $whereConditions[] = "con.condition_sv_name = ?";
        $params[] = $condition;
    }
    
    if (!empty($shelf)) {
        $whereConditions[] = "sh.shelf_sv_name = ?";
        $params[] = $shelf;
    }
    
    if ($priceMin > 0) {
        $whereConditions[] = "p.price >= ?";
        $params[] = $priceMin;
    }
    
    if ($priceMax > 0) {
        $whereConditions[] = "p.price <= ?";
        $params[] = $priceMax;
    }
    
    if (!empty($dateMin)) {
        $whereConditions[] = "DATE(p.date_added) >= ?";
        $params[] = $dateMin;
    }
    
    if (!empty($dateMax)) {
        $whereConditions[] = "DATE(p.date_added) <= ?";
        $params[] = $dateMax;
    }
    
    // Status condition - modified for lists view
    if ($isListsView) {
        // For lists view: empty status means available only, 'all' means all statuses
        if (empty($status) || $status === '') {
            $whereConditions[] = "p.status = 1"; // Default to available only
        } elseif ($status !== 'all') {
            if ($status === 'Tillgänglig') {
                $whereConditions[] = "p.status = 1";
            } elseif ($status === 'Såld') {
                $whereConditions[] = "p.status = 2";
            } else {
                $whereConditions[] = "s.status_sv_name = ?";
                $params[] = $status;
            }
        }
    } else {
        // Existing status handling for admin search
        if (!$showAllStatuses) {
            $whereConditions[] = "p.status = 1";
        } else if ($status !== 'all' && !empty($status)) {
            $whereConditions[] = "p.status = ?";
            $params[] = $status;
        }
    }
    
    // Special filters for lists view
    if ($isListsView) {
        if ($noPrice) {
            $whereConditions[] = "(p.price IS NULL OR p.price = 0 OR p.price = '')";
        }
        
        if ($poorCondition) {
            $whereConditions[] = "p.condition_id = 4"; // Assuming 4 is the lowest condition
        }
        
        if ($yearThreshold > 0) {
            $whereConditions[] = "p.year <= ?";
            $params[] = $yearThreshold;
        }
        
        // Special marking filters
        if ($specialPrice > 0) {
            $whereConditions[] = "p.special_price = 1";
        }
        
        if ($rare > 0) {
            $whereConditions[] = "p.rare = 1";
        }
        
        if ($recommended > 0) {
            $whereConditions[] = "p.recommended = 1";
        }
    }
    
    // Add WHERE clause if we have conditions
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add GROUP BY clause
    $sql .= " GROUP BY p.prod_id";
    
    // Add ORDER BY clause - modified for lists view
    if ($isListsView) {
        $sql .= " ORDER BY p.title ASC"; // Order by title for lists view
    } else {
        $sql .= " ORDER BY p.title ASC"; // Keep consistent ordering
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
    
    // Process products to format data - enhanced for lists view
    $formattedProducts = [];
    foreach ($products as $product) {
        // Format price using Formatter
        $product['formatted_price'] = $formatter->formatPrice($product['price']);
        
        // Format date using Formatter
        $product['formatted_date'] = $formatter->formatDate($product['date_added']);
        
        // For lists view, ensure we have the marking fields
        if ($isListsView) {
            $product['special_price'] = $product['special_price'] ?? 0;
            $product['rare'] = $product['rare'] ?? 0;
            $product['recommended'] = $product['recommended'] ?? 0;
            $product['image_paths'] = $product['image_paths'] ?? '';
        }
        
        // Add href only for admin search view (not for lists view)
        if (!$isListsView) {
            $product['href'] = url("admin/adminsingleproduct.php?id=" . $product['prod_id']);
        }
        
        // Add to formatted array
        $formattedProducts[] = $product;
    }
    
    // Generate HTML for products (if needed)
    $html = '';
    if (!empty($formattedProducts)) {
        ob_start();
        
        if ($isListsView) {
            // HTML for lists view (with checkboxes and märkning column)
            foreach ($formattedProducts as $product) {
                $statusClass = (int)$product['status'] === 1 ? 'text-success' : 'text-danger';
                $formattedPrice = $product['price'] ? $product['formatted_price'] : 'Inget pris';
                
                // Create marking badges
                $markings = '';
                if ($product['special_price'] == 1) {
                    $markings .= '<span class="badge bg-danger me-1">Rea</span>';
                }
                if ($product['rare'] == 1) {
                    $markings .= '<span class="badge bg-warning text-dark me-1">Sällsynt</span>';
                }
                if ($product['recommended'] == 1) {
                    $markings .= '<span class="badge bg-primary me-1">Rekommenderas</span>';
                }
                ?>
                <tr>
                    <td><input type="checkbox" name="list-item" value="<?= safeEcho($product['prod_id']) ?>"></td>
                    <td><?= safeEcho($product['title']) ?></td>
                    <td><?= safeEcho($product['author_name']) ?></td>
                    <td><?= safeEcho($product['category_name']) ?></td>
                    <td><?= safeEcho($product['shelf_name']) ?></td>
                    <td><?= safeEcho($product['condition_name']) ?></td>
                    <td><?= safeEcho($formattedPrice) ?></td>
                    <td class="<?= safeEcho($statusClass) ?>"><?= safeEcho($product['status_name']) ?></td>
                    <td><?= $markings ?></td>
                    <td><?= safeEcho($product['formatted_date']) ?></td>
                </tr>
                <?php
            }
        } else {
            // HTML for admin search view (existing code)
            foreach ($formattedProducts as $product) {
                $statusClass = (int)$product['status'] === 1 ? 'text-success' : 'text-danger';
                ?>
                <tr class="clickable-row" data-href="<?= safeEcho(url('admin/adminsingleproduct.php', ['id' => $product['prod_id']])) ?>">
                    <td>
                        <?= safeEcho($product['title']) ?>
                        <?php if (!empty($product['internal_notes'])): ?>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-sticky-note" title="Interna anteckningar"></i> 
                                <?= safeEcho($formatter->formatText($product['internal_notes'], 50)) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= safeEcho($product['author_name']) ?></td>
                    <td><?= safeEcho($product['category_name']) ?></td>
                    <td><?= safeEcho($product['shelf_name']) ?></td>
                    <td>
                        <?php if ($product['price'] !== null): ?>
                            <?= safeEcho($product['formatted_price']) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="<?= safeEcho($statusClass) ?>"><?= safeEcho($product['status_name']) ?></td>
                    <td>
                        <?php if ((int)$product['special_price'] === 1): ?>
                            <span class="badge bg-danger">Rea</span>
                        <?php endif; ?>
                        <?php if ((int)$product['rare'] === 1): ?>
                            <span class="badge bg-warning text-dark">Sällsynt</span>
                        <?php endif; ?>
                        <?php if ((int)$product['recommended'] === 1): ?>
                            <span class="badge bg-primary">Rekommenderas</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <?php if ((int)$product['status'] === 1): ?>
                                <button class="btn btn-outline-success quick-sell" style="width: 70px" data-id="<?= safeEcho($product['prod_id']) ?>" title="Markera som såld">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-warning quick-return" style="width: 70px" data-id="<?= safeEcho($product['prod_id']) ?>" title="Återställ till tillgänglig">
                                    <i class="fas fa-undo"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
            }
        }
        $html = ob_get_clean();
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
            'lastRecord' => $lastRecord
        ]
    ];
    
    // Output JSON response
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log the detailed error for debugging
    error_log('Error in get_products.php: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
    
    // Send error response
    echo json_encode([
        'success' => false,
        'message' => 'Ett fel inträffade vid hämtning av produkter'
    ]);
}
?>