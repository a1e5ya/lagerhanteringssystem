<?php
/**
 * Export functionality for product lists - UPDATED
 * 
 * Exports data in various formats (CSV, etc.)
 * Now supports "select all pages" functionality and additional filters
 */

require_once '../init.php';

// Check if user is authenticated and has admin or editor permissions
// Only Admin (1) or Editor (2) roles can access this page
checkAuth(2); // Role 2 (Editor) or above required

// Get requested format
$format = $_GET['format'] ?? 'csv';

// Get filter parameters - the same ones used in lists.php
$filters = [];

// Extract all parameters from GET
foreach ($_GET as $key => $value) {
    if ($key !== 'format') {
        $filters[$key] = $value;
    }
}

// Handle "select all with filters" scenario
$selectAllWithFilters = isset($_GET['select_all_with_filters']) && $_GET['select_all_with_filters'] === 'true';

// Handle selected items if provided
if (isset($_GET['selected_items']) && !$selectAllWithFilters) {
    $selectedItems = json_decode($_GET['selected_items'], true);
    if (is_array($selectedItems) && !empty($selectedItems)) {
        $filters['selected_items'] = $selectedItems;
    }
}

// Handle filters from "select all pages" scenario
if ($selectAllWithFilters && isset($_GET['filters'])) {
    $filtersFromSelectAll = json_decode($_GET['filters'], true);
    if (is_array($filtersFromSelectAll)) {
        $filters = array_merge($filters, $filtersFromSelectAll);
        $filters['select_all_with_filters'] = true;
    }
}

// Generate filename
$timestamp = date('Y-m-d_His');
$filename = "karis_antikvariat_export_{$timestamp}";

// Handle different export formats
switch ($format) {
    case 'csv':
        exportCSV($filters, $filename);
        break;
    
    // Add more formats here if needed
    
    default:
        // Default to CSV if format not recognized
        exportCSV($filters, $filename);
        break;
}

/**
 * Export data as CSV
 * 
 * @param array $filters Filters to apply when selecting products
 * @param string $filename Base filename (without extension)
 */
function exportCSV($filters, $filename) {
    global $pdo;
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Define CSV headers - Updated to exclude ID and include new columns
    $headers = [
        'Titel', 
        'Författare', 
        'Kategori', 
        'Hylla', 
        'Skick', 
        'Pris (€)', 
        'Status', 
        'Språk',
        'År',
        'Förlag',
        'Märkning',
        'Bilder',
        'Tillagd datum',
        'Anteckningar'
    ];
    
    // Output the column headings
    fputcsv($output, $headers);
    
    // Get the products based on filters
    $products = getProductsForExport($filters);
    
    // Output each row of the data
    foreach ($products as $product) {
        // Format date
        $dateAdded = date('Y-m-d', strtotime($product['date_added']));
        
        // Format price
        $price = $product['price'] ? number_format($product['price'], 2, '.', '') : '';
        
        // Format marking/tags
        $markings = [];
        if ($product['special_price']) $markings[] = 'Rea';
        if ($product['rare']) $markings[] = 'Sällsynt';
        if ($product['recommended']) $markings[] = 'Rekommenderas';
        $markingString = implode(', ', $markings);
        
        // Format images - all images comma-separated
        $imageString = $product['image_paths'] ?? '';
        
        // Create row
        $row = [
            $product['title'],
            $product['author_name'],
            $product['category_name'],
            $product['shelf_name'],
            $product['condition_name'],
            $price,
            $product['status_name'],
            $product['language'],
            $product['year'],
            $product['publisher'],
            $markingString,
            $imageString,
            $dateAdded,
            $product['notes']
        ];
        
        fputcsv($output, $row);
    }
    
    // Close the file pointer
    fclose($output);
    exit;
}

/**
 * Get products for export based on filters - UPDATED
 * 
 * @param array $filters Filters to apply
 * @return array Products data
 */
function getProductsForExport($filters) {
    global $pdo;
    
    // Build SQL query with appropriate filters - updated to include new fields and exclude ID
    $sql = "SELECT
                p.title,
                p.status,
                p.special_price,
                p.rare,
                p.recommended,
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
                p.date_added,
                p.notes,
                GROUP_CONCAT(DISTINCT img.image_path SEPARATOR ', ') AS image_paths
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            JOIN category c ON p.category_id = c.category_id
            LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
            LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
            LEFT JOIN genre g ON pg.genre_id = g.genre_id
            LEFT JOIN image img ON p.prod_id = img.prod_id
            JOIN `condition` con ON p.condition_id = con.condition_id
            JOIN `status` s ON p.status = s.status_id
            LEFT JOIN `language` l ON p.language_id = l.language_id
            WHERE 1=1";
    
    $params = [];
    
    // Handle selected items first (takes precedence over other filters)
    if (isset($filters['selected_items']) && is_array($filters['selected_items']) && !empty($filters['selected_items'])) {
        $placeholders = implode(',', array_fill(0, count($filters['selected_items']), '?'));
        $sql .= " AND p.prod_id IN ($placeholders)";
        $params = array_merge($params, $filters['selected_items']);
    } else {
        // Apply other filters only if not filtering by selected items
        
        // Default to available products only if no status filter is specified
        if (!isset($filters['status']) || $filters['status'] === '') {
            $sql .= " AND p.status = 1";
        } elseif ($filters['status'] === 'all') {
            // Show all statuses - no additional filter
        } elseif ($filters['status'] === 'Såld') {
            $sql .= " AND p.status = 2";
        } else {
            $sql .= " AND s.status_sv_name = ?";
            $params[] = $filters['status'];
        }
        
        // Category filter
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        // Genre filter
        if (!empty($filters['genre'])) {
            $sql .= " AND g.genre_sv_name = ?";
            $params[] = $filters['genre'];
        }
        
        // Shelf filter
        if (!empty($filters['shelf'])) {
            $sql .= " AND sh.shelf_sv_name = ?";
            $params[] = $filters['shelf'];
        }
        
        // Condition filter
        if (!empty($filters['condition'])) {
            $sql .= " AND con.condition_sv_name = ?";
            $params[] = $filters['condition'];
        }
        
        // Price range
        if (!empty($filters['price_min']) && floatval($filters['price_min']) > 0) {
            $sql .= " AND p.price >= ?";
            $params[] = floatval($filters['price_min']);
        }
        
        if (!empty($filters['price_max']) && floatval($filters['price_max']) > 0) {
            $sql .= " AND p.price <= ?";
            $params[] = floatval($filters['price_max']);
        }
        
        // Date range
        if (!empty($filters['date_min'])) {
            $sql .= " AND DATE(p.date_added) >= ?";
            $params[] = $filters['date_min'];
        }
        
        if (!empty($filters['date_max'])) {
            $sql .= " AND DATE(p.date_added) <= ?";
            $params[] = $filters['date_max'];
        }
        
        // Year threshold filter
        if (!empty($filters['year_threshold'])) {
            $sql .= " AND p.year <= ?";
            $params[] = $filters['year_threshold'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR a.author_name LIKE ? OR p.notes LIKE ? OR p.internal_notes LIKE ? OR p.publisher LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Special filters
        if (isset($filters['no_price']) && $filters['no_price']) {
            $sql .= " AND (p.price IS NULL OR p.price = 0)";
        }
        
        if (isset($filters['poor_condition']) && $filters['poor_condition']) {
            $sql .= " AND p.condition_id = 4"; // Assuming 4 is 'Acceptabelt' (lowest condition)
        }
        
        // NEW: Special marking filters
        if (isset($filters['special_price']) && intval($filters['special_price']) > 0) {
            $sql .= " AND p.special_price = 1";
        }
        
        if (isset($filters['rare']) && intval($filters['rare']) > 0) {
            $sql .= " AND p.rare = 1";
        }
        
        if (isset($filters['recommended']) && intval($filters['recommended']) > 0) {
            $sql .= " AND p.recommended = 1";
        }
    }
    
    // Group by to avoid duplicates due to JOIN with authors and genres
    $sql .= " GROUP BY p.prod_id";
    
    // Order by for consistent export ordering - changed from category/shelf to title
    $sql .= " ORDER BY p.title ASC";
    
    try {
        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Fetch all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Export query error: " . $e->getMessage());
        return []; // Return empty array on error
    }
}