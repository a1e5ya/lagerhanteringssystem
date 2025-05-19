<?php
/**
 * Get Products for Admin Search
 * 
 * Server-side proxy script to handle API requests
 */

require_once dirname(__DIR__) . '/init.php';

// Get parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$showAllStatuses = isset($_GET['show_all_statuses']) && $_GET['show_all_statuses'] === 'true';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Set header to JSON
header('Content-Type: application/json');

try {
    
    // Create SQL query - now including internal_notes
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
    
    // Add WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Search condition - now also including internal_notes
    if (!empty($search)) {
        $whereConditions[] = "(p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ? OR c.category_sv_name LIKE ? OR g.genre_sv_name LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam; // For internal_notes
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Category condition
    if (!empty($category)) {
        $whereConditions[] = "p.category_id = ?";
        $params[] = $category;
    }
    
    // Status condition - if not showing all statuses, default to available (1)
    if (!$showAllStatuses) {
        $whereConditions[] = "p.status = 1";
    } else if ($status !== 'all' && !empty($status)) {
        $whereConditions[] = "p.status = ?";
        $params[] = $status;
    }
    
    // Add WHERE clause if we have conditions
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add GROUP BY clause
    $sql .= " GROUP BY p.prod_id";
    
    // Add ORDER BY clause
    $sql .= " ORDER BY p.title ASC";
    
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
        
        // Add to formatted array
        $formattedProducts[] = $product;
    }
    
    // Generate HTML for products (if needed)
    $html = '';
    if (!empty($formattedProducts)) {
        ob_start();
        foreach ($formattedProducts as $product) {
            $statusClass = (int)$product['status'] === 1 ? 'text-success' : 'text-danger';
            ?>
            <tr class="clickable-row" data-href="<?= url('admin/adminsingleproduct.php', ['id' => $product['prod_id']]) ?>">
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
                <td class="<?= $statusClass ?>"><?= safeEcho($product['status_name']) ?></td>
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
    // Send error response
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}