<?php
/**
 * Export Functionality for Product Lists
 * 
 * Exports data in various formats (CSV, etc.) with comprehensive security validation
 * Supports "select all pages" functionality and additional filters
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

require_once '../init.php';

// Check if user is authenticated and has admin or editor permissions
// Only Admin (1) or Editor (2) roles can access this page
checkAuth(2); // Role 2 (Editor) or above required

try {
    // Get and validate requested format
    $format = sanitizeInput($_GET['format'] ?? 'csv', 'string', 10);
    
    // Validate format against allowed values
    $allowedFormats = ['csv'];
    if (!in_array($format, $allowedFormats)) {
        $format = 'csv'; // Default to CSV if invalid format
    }
    
    // Get and sanitize filter parameters
    $filters = sanitizeFilters($_GET);
    
    // Generate secure filename with timestamp
    $timestamp = date('Y-m-d_His');
    $filename = sanitizeInput("karis_antikvariat_export_{$timestamp}", 'filename');
    
    // Handle different export formats
    switch ($format) {
        case 'csv':
            exportCSV($filters, $filename);
            break;
        
        default:
            // Default to CSV if format not recognized
            exportCSV($filters, $filename);
            break;
    }
    
} catch (InvalidArgumentException $e) {
    // Log the error and return user-friendly message
    error_log('Export validation error: ' . $e->getMessage());
    http_response_code(400);
    die('Ogiltiga exportparametrar');
} catch (Exception $e) {
    // Log the error and return generic message
    error_log('Export error: ' . $e->getMessage());
    http_response_code(500);
    die('Ett fel inträffade vid export');
}

/**
 * Export data as CSV with security validation
 * 
 * @param array $filters Sanitized filters to apply when selecting products
 * @param string $filename Sanitized base filename (without extension)
 * @throws Exception If export fails
 */
function exportCSV($filters, $filename) {
    global $pdo;
    
    // Validate filename
    if (empty($filename) || !preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
        throw new InvalidArgumentException('Ogiltigt filnamn');
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output UTF-8 BOM
    echo "\xEF\xBB\xBF";
    
    // Define headers without special characters initially
    $headers = [
        'Titel', 
        'Forfattare', 
        'Kategori', 
        'Hylla', 
        'Skick', 
        'Pris', 
        'Status', 
        'Sprak',
        'Ar',
        'Forlag',
        'Markning',
        'Bilder',
        'Tillagd_datum',
        'Anteckningar'
    ];
    
    // Output headers
    echo implode(';', $headers) . "\r\n";
    
    // Get products
    $products = getProductsForExport($filters);
    
    // Output data rows
    foreach ($products as $product) {
        $row = [
            escapeCsvField($product['title'] ?? ''),
            escapeCsvField($product['author_name'] ?? ''),
            escapeCsvField($product['category_name'] ?? ''),
            escapeCsvField($product['shelf_name'] ?? ''),
            escapeCsvField($product['condition_name'] ?? ''),
            $product['price'] ? number_format((float)$product['price'], 2, '.', '') : '',
            escapeCsvField($product['status_name'] ?? ''),
            escapeCsvField($product['language'] ?? ''),
            $product['year'] ?? '',
            escapeCsvField($product['publisher'] ?? ''),
            escapeCsvField(formatMarkings($product)),
            escapeCsvField($product['image_paths'] ?? ''),
            $product['date_added'] ? date('Y-m-d', strtotime($product['date_added'])) : '',
            escapeCsvField($product['notes'] ?? '')
        ];
        
        echo implode(';', $row) . "\r\n";
    }
    
    exit;
}

/**
 * Escape CSV field properly
 * 
 * @param string $field Field to escape
 * @return string Escaped field
 */
function escapeCsvField($field) {
    // If field contains semicolon, quote, or newline, wrap in quotes
    if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
        return '"' . str_replace('"', '""', $field) . '"';
    }
    return $field;
}

/**
 * Format special markings for export
 * 
 * @param array $product Product data
 * @return string Formatted markings string
 */
function formatMarkings($product) {
    $markings = [];
    
    if (!empty($product['special_price']) && $product['special_price'] == 1) {
        $markings[] = 'Rea';
    }
    if (!empty($product['rare']) && $product['rare'] == 1) {
        $markings[] = 'Sallsynt';
    }
    if (!empty($product['recommended']) && $product['recommended'] == 1) {
        $markings[] = 'Rekommenderas';
    }
    
    return implode(', ', $markings);
}

/**
 * Sanitize and validate all filter parameters
 * 
 * @param array $rawFilters Raw filter data from GET parameters
 * @return array Sanitized filter array
 * @throws InvalidArgumentException If validation fails
 */
function sanitizeFilters($rawFilters) {
    $filters = [];
    
    // Handle "select all with filters" scenario
    $selectAllWithFilters = isset($rawFilters['select_all_with_filters']) && 
                           $rawFilters['select_all_with_filters'] === 'true';
    
    // Handle selected items if provided
    if (isset($rawFilters['selected_items']) && !$selectAllWithFilters) {
        $selectedItemsJson = sanitizeInput($rawFilters['selected_items'], 'json', 10000);
        $selectedItems = json_decode($selectedItemsJson, true);
        
        if (is_array($selectedItems) && !empty($selectedItems)) {
            // Validate all selected items are positive integers
            $validatedItems = [];
            foreach ($selectedItems as $item) {
                $validatedItem = sanitizeInput($item, 'int', null, ['min' => 1]);
                if ($validatedItem > 0) {
                    $validatedItems[] = $validatedItem;
                }
            }
            
            if (!empty($validatedItems)) {
                $filters['selected_items'] = $validatedItems;
            }
        }
    }
    
    // Handle filters from "select all pages" scenario
    if ($selectAllWithFilters && isset($rawFilters['filters'])) {
        $filtersJson = sanitizeInput($rawFilters['filters'], 'json', 5000);
        $filtersFromSelectAll = json_decode($filtersJson, true);
        
        if (is_array($filtersFromSelectAll)) {
            $filters = array_merge($filters, sanitizeIndividualFilters($filtersFromSelectAll));
            $filters['select_all_with_filters'] = true;
        }
    } else {
        // Sanitize individual filter parameters
        $filters = array_merge($filters, sanitizeIndividualFilters($rawFilters));
    }
    
    return $filters;
}

/**
 * Sanitize individual filter parameters
 * 
 * @param array $rawFilters Raw filter parameters
 * @return array Sanitized filters
 * @throws InvalidArgumentException If validation fails
 */
function sanitizeIndividualFilters($rawFilters) {
    $filters = [];
    
    // Status filter
    if (isset($rawFilters['status'])) {
        $status = sanitizeInput($rawFilters['status'], 'string', 50);
        $allowedStatuses = ['', 'all', 'Såld', 'Tillgänglig', 'Reserverad'];
        if (in_array($status, $allowedStatuses)) {
            $filters['status'] = $status;
        }
    }
    
    // Category filter (integer ID)
    if (isset($rawFilters['category'])) {
        $category = sanitizeInput($rawFilters['category'], 'int', null, ['min' => 1]);
        if ($category > 0) {
            $filters['category'] = $category;
        }
    }
    
    // Genre filter (string name)
    if (isset($rawFilters['genre'])) {
        $genre = sanitizeInput($rawFilters['genre'], 'string', 100);
        if (!empty($genre)) {
            $filters['genre'] = $genre;
        }
    }
    
    // Shelf filter (string name)
    if (isset($rawFilters['shelf'])) {
        $shelf = sanitizeInput($rawFilters['shelf'], 'string', 100);
        if (!empty($shelf)) {
            $filters['shelf'] = $shelf;
        }
    }
    
    // Condition filter (string name)
    if (isset($rawFilters['condition'])) {
        $condition = sanitizeInput($rawFilters['condition'], 'string', 100);
        if (!empty($condition)) {
            $filters['condition'] = $condition;
        }
    }
    
    // Price range filters
    if (isset($rawFilters['price_min'])) {
        $priceMin = sanitizeInput($rawFilters['price_min'], 'float');
        if ($priceMin > 0) {
            $filters['price_min'] = $priceMin;
        }
    }
    
    if (isset($rawFilters['price_max'])) {
        $priceMax = sanitizeInput($rawFilters['price_max'], 'float');
        if ($priceMax > 0) {
            $filters['price_max'] = $priceMax;
        }
    }
    
    // Date range filters
    if (isset($rawFilters['date_min'])) {
        $dateMin = sanitizeInput($rawFilters['date_min'], 'string', 10);
        if (validateDate($dateMin, 'Y-m-d')) {
            $filters['date_min'] = $dateMin;
        }
    }
    
    if (isset($rawFilters['date_max'])) {
        $dateMax = sanitizeInput($rawFilters['date_max'], 'string', 10);
        if (validateDate($dateMax, 'Y-m-d')) {
            $filters['date_max'] = $dateMax;
        }
    }
    
    // Year threshold filter
    if (isset($rawFilters['year_threshold'])) {
        $yearThreshold = sanitizeInput($rawFilters['year_threshold'], 'int', null, ['min' => 1000, 'max' => 9999]);
        if ($yearThreshold > 0) {
            $filters['year_threshold'] = $yearThreshold;
        }
    }
    
    // Search filter
    if (isset($rawFilters['search'])) {
        $search = sanitizeInput($rawFilters['search'], 'string', 255);
        if (!empty($search)) {
            $filters['search'] = $search;
        }
    }
    
    // Boolean filters
    $booleanFilters = ['no_price', 'poor_condition', 'special_price', 'rare', 'recommended'];
    foreach ($booleanFilters as $filter) {
        if (isset($rawFilters[$filter])) {
            $value = sanitizeInput($rawFilters[$filter], 'int', null, ['min' => 0, 'max' => 1]);
            if ($value !== null) {
                $filters[$filter] = $value;
            }
        }
    }
    
    return $filters;
}

/**
 * Get products for export based on sanitized filters
 * 
 * @param array $filters Sanitized filters to apply
 * @return array Products data
 * @throws Exception If query fails
 */
function getProductsForExport($filters) {
    global $pdo;
    
    // Build SQL query with appropriate filters
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
            $sql .= " AND p.status = ?";
            $params[] = 1;
        } elseif ($filters['status'] === 'all') {
            // Show all statuses - no additional filter
        } elseif ($filters['status'] === 'Såld') {
            $sql .= " AND p.status = ?";
            $params[] = 2;
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
        if (!empty($filters['price_min'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['price_min'];
        }
        
        if (!empty($filters['price_max'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['price_max'];
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
            $searchTerm = "%" . $filters['search'] . "%";
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
            $sql .= " AND p.condition_id = ?";
            $params[] = 4; // Assuming 4 is 'Acceptabelt' (lowest condition)
        }
        
        // Special marking filters
        if (isset($filters['special_price']) && $filters['special_price'] > 0) {
            $sql .= " AND p.special_price = ?";
            $params[] = 1;
        }
        
        if (isset($filters['rare']) && $filters['rare'] > 0) {
            $sql .= " AND p.rare = ?";
            $params[] = 1;
        }
        
        if (isset($filters['recommended']) && $filters['recommended'] > 0) {
            $sql .= " AND p.recommended = ?";
            $params[] = 1;
        }
    }
    
    // Group by to avoid duplicates due to JOIN with authors and genres
    $sql .= " GROUP BY p.prod_id";
    
    // Order by for consistent export ordering
    $sql .= " ORDER BY p.title ASC";
    
    try {
        // Prepare and execute the query with parameter binding
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Fetch all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Export query error: " . $e->getMessage());
        throw new Exception('Databasfrågan misslyckades');
    }
}
?>