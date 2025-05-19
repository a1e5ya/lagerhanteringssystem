<?php
/**
 * Export functionality for product lists
 * 
 * Exports data in various formats (CSV, etc.)
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

// Handle selected items if provided
if (isset($_GET['selected_items'])) {
    $selectedItems = json_decode($_GET['selected_items'], true);
    if (is_array($selectedItems) && !empty($selectedItems)) {
        $filters['selected_items'] = $selectedItems;
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
    
    // Define CSV headers
    $headers = [
        'ID', 
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
        'Rare',
        'Special pris',
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
        $price = number_format($product['price'], 2, '.', '');
        
        // Create row
        $row = [
            $product['prod_id'],
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
            $product['rare'] ? 'Ja' : 'Nej',
            $product['special_price'] ? 'Ja' : 'Nej',
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
 * Get products for export based on filters
 * 
 * @param array $filters Filters to apply
 * @return array Products data
 */
function getProductsForExport($filters) {
    global $pdo;
    
    // Build SQL query with appropriate filters
    $sql = "SELECT
                p.prod_id,
                p.title,
                p.status,
                s.status_name,
                p.shelf_id,
                sh.shelf_name,
                GROUP_CONCAT(DISTINCT CONCAT(a.first_name, ' ', a.last_name) SEPARATOR ', ') AS author_name,
                cat.category_name,
                p.category_id,
                GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ', ') AS genre_names,
                con.condition_name,
                p.price,
                p.language,
                p.year,
                p.publisher,
                p.rare,
                p.special_price,
                p.date_added,
                p.notes
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
    
    // Handle selected items first (takes precedence over other filters)
    if (isset($filters['selected_items']) && is_array($filters['selected_items']) && !empty($filters['selected_items'])) {
        $placeholders = implode(',', array_fill(0, count($filters['selected_items']), '?'));
        $sql .= " AND p.prod_id IN ($placeholders)";
        $params = array_merge($params, $filters['selected_items']);
    } else {
        // Apply other filters only if not filtering by selected items
        
        // Category filter
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        // Genre filter
        if (!empty($filters['genre'])) {
            $sql .= " AND g.genre_name = ?";
            $params[] = $filters['genre'];
        }
        
        // Shelf filter
        if (!empty($filters['shelf'])) {
            $sql .= " AND sh.shelf_name = ?";
            $params[] = $filters['shelf'];
        }
        
        // Condition filter
        if (!empty($filters['condition'])) {
            $sql .= " AND con.condition_name = ?";
            $params[] = $filters['condition'];
        }
        
        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $filters['status'];
        }
        
        // Price range
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        // Date range
        if (!empty($filters['min_date'])) {
            $sql .= " AND p.date_added >= ?";
            $params[] = $filters['min_date'];
        }
        
        if (!empty($filters['max_date'])) {
            $sql .= " AND p.date_added <= ?";
            $params[] = $filters['max_date'];
        }
        
        // Year threshold filter
        if (!empty($filters['year_threshold'])) {
            $sql .= " AND p.year <= ?";
            $params[] = $filters['year_threshold'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR a.first_name LIKE ? OR a.last_name LIKE ? OR p.notes LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
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
    }
    
    // Group by to avoid duplicates due to JOIN with authors and genres
    $sql .= " GROUP BY p.prod_id";
    
    // Order by for consistent export ordering
    $sql .= " ORDER BY p.category_id, p.shelf_id, p.title";
    
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