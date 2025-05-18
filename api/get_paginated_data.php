<?php

// Add at the very beginning of get_public_products.php, after the initial PHP tag
error_log('get_public_products.php called with params: ' . json_encode($_GET));

// Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log request details
error_log('API Request: ' . json_encode($_REQUEST));
error_log('POST data: ' . file_get_contents('php://input'));
error_log('Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));


/**
 * Centralized API Endpoint for Paginated Data
 * 
 * Provides a single endpoint for retrieving paginated data across
 * the entire Karis Antikvariat inventory management system.
 * 
 * @package    KarisAntikvariat
 * @subpackage API
 * @author     Axxell
 * @version    2.0
 */

// Define BASE_PATH if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Include necessary files
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/db_functions.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/Paginator.php';
require_once BASE_PATH . '/includes/Formatter.php';

// Set JSON content type
header('Content-Type: application/json');

// Check for AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Get request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Get request parameters (handle both GET and POST)
$params = ($requestMethod === 'POST') ? $_POST : $_GET;

// If Content-Type is application/json, parse the JSON body
if ($requestMethod === 'POST' && isset($_SERVER['CONTENT_TYPE']) && 
    strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $jsonInput = file_get_contents('php://input');
    $params = json_decode($jsonInput, true) ?: [];
}

// Required parameters
$entity = $params['entity'] ?? '';                 // Which entity to query (products, authors, etc.)
$action = $params['action'] ?? 'list';             // Action to perform (list, count, etc.)
$viewType = $params['view_type'] ?? 'public';      // View type (public, admin, lists)

// Pagination parameters
$page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
$limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 20; // Default 20 items per page

// Sorting parameters
$sortColumn = isset($params['sort']) ? $params['sort'] : '';
$sortDirection = (isset($params['order']) && strtolower($params['order']) === 'desc') ? 'desc' : 'asc';

// Whether to include HTML rendering in response
$renderHtml = isset($params['render_html']) && $params['render_html'];

// Authentication check for protected entities
$protectedEntities = ['users', 'settings', 'event_log'];
if (in_array($entity, $protectedEntities)) {
    checkAuth(2); // Role 2 (Editor) or above required
    
    // Additional auth check for user management (admin only)
    if ($entity === 'users' && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] > 1)) {
        sendErrorResponse('Unauthorized access', 403);
        exit;
    }
}

// Create paginator instance
// Create paginator instance with allowed sort columns
$allowedSortColumns = getAllowedSortColumns($entity);
$paginator = new Paginator(0, $limit, $page, $sortColumn, $sortDirection, $allowedSortColumns);

/**
 * Get allowed sort columns for an entity
 * 
 * @param string $entity Entity name
 * @return array Allowed sort columns
 */
function getAllowedSortColumns($entity) {
    switch ($entity) {
        case 'products':
            return ['prod_id', 'title', 'author_name', 'price', 'category_name', 
                    'status', 'condition_name', 'date_added'];
        case 'authors':
            return ['author_id', 'author_name'];
        case 'categories':
            return ['category_id', 'category_name'];
        case 'shelves':
            return ['shelf_id', 'shelf_name'];
        case 'users':
            return ['user_id', 'user_username', 'user_created_at', 'user_last_login'];
        case 'event_log':
            return ['event_id', 'event_timestamp', 'event_type', 'user_username'];
        default:
            return [];
    }
}

try {
    // Validate entity
    $allowedEntities = ['products', 'authors', 'categories', 'shelves', 'genres', 'conditions', 'users', 'event_log'];
    if (!in_array($entity, $allowedEntities)) {
        sendErrorResponse('Invalid entity specified');
        exit;
    }
    
    // Initialize Formatter for consistent data formatting
    $formatter = new Formatter();
    
    // Process request based on entity and action
    switch ($entity) {
        case 'products':
            handleProductsRequest($params, $paginator, $formatter);
            break;
            
        case 'authors':
            handleAuthorsRequest($params, $paginator, $formatter);
            break;
            
        case 'categories':
            handleCategoriesRequest($params, $paginator, $formatter);
            break;
            
        case 'shelves':
            handleShelvesRequest($params, $paginator, $formatter);
            break;
            
        case 'genres':
            handleGenresRequest($params, $paginator, $formatter);
            break;
            
        case 'conditions':
            handleConditionsRequest($params, $paginator, $formatter);
            break;
            
        case 'users':
            handleUsersRequest($params, $paginator, $formatter);
            break;
            
        case 'event_log':
            handleEventLogRequest($params, $paginator, $formatter);
            break;
            
        default:
            sendErrorResponse('Entity not supported');
            break;
    }
} catch (Exception $e) {
    // Log the error
    error_log('API Error: ' . $e->getMessage());
    
    // Send error response
    sendErrorResponse('An error occurred: ' . $e->getMessage());
}

/**
 * Handle Products data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleProductsRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    
    // Get filter parameters
    $filters = getProductFilters($params);

// Handle initial display of products
// We'll show random samples for initial page loads
$isInitialPageLoad = empty($filters['search']) && 
                    (empty($filters['category']) || $filters['category'] === 'all' || $filters['category'] == 0);

// For initial page loads, show random samples
if ($isInitialPageLoad && $action === 'list') {
    // Get random samples
    $sampleProducts = getRandomSampleProducts($pdo, 2); // 2 samples per category
    
    // Format the products data
    $formattedProducts = formatProductsData($sampleProducts, $formatter);
    
    // Set total items in paginator
    $totalItems = count($formattedProducts);
    $paginator->setTotalItems($totalItems);
    
    // Prepare response with formatted sample products
    $response = [
        'success' => true,
        'items' => $formattedProducts,
        'pagination' => $paginator->toArray()
    ];
    
    // Add HTML rendering if requested
    if (isset($params['render_html']) && $params['render_html']) {
        $response['html'] = renderProductsTable($formattedProducts, $params['view_type'] ?? 'public');
    }
    
    sendJsonResponse($response);
    return;
}
    
    try {
        switch ($action) {
            case 'list':
                // Build SQL query with appropriate joins and filters
                list($sql, $countSql, $sqlParams) = buildProductsQuery($filters, $paginator);
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Format the products data
                $formattedProducts = formatProductsData($products, $formatter);
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $formattedProducts,
                    'pagination' => $paginator->toArray()
                ];
                
                // Add HTML rendering if requested
                if (isset($params['render_html']) && $params['render_html']) {
                    $response['html'] = renderProductsTable($formattedProducts, $params['view_type'] ?? 'admin');
                }
                
                sendJsonResponse($response);
                break;
                
            case 'count':
                // Build count query
                $sql = "SELECT COUNT(*) FROM product p";
                
                // Apply filters to the query
                list($sql, $sqlParams) = applyProductFilters($sql, $filters);
                
                // Execute the query
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $count = (int)$stmt->fetchColumn();
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'count' => $count
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for products entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleProductsRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Authors data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleAuthorsRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT author_id, author_name FROM author";
                $countSql = "SELECT COUNT(*) FROM author";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE author_name LIKE :search";
                    $countSql .= " WHERE author_name LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY author_name ASC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $authors,
                    'pagination' => $paginator->toArray()
                ];
                
                // Add HTML rendering if requested
                if (isset($params['render_html']) && $params['render_html']) {
                    $response['html'] = renderAuthorsTable($authors);
                }
                
                sendJsonResponse($response);
                break;
                
            case 'count':
                // Build count query
                $sql = "SELECT COUNT(*) FROM author";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE author_name LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                // Execute the query
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $count = (int)$stmt->fetchColumn();
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'count' => $count
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for authors entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleAuthorsRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Categories data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleCategoriesRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get language from session or default to Swedish
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which field to use based on language
    $nameField = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT category_id, {$nameField} AS category_name FROM category";
                $countSql = "SELECT COUNT(*) FROM category";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE {$nameField} LIKE :search";
                    $countSql .= " WHERE {$nameField} LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY {$nameField} ASC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $categories,
                    'pagination' => $paginator->toArray()
                ];
                
                sendJsonResponse($response);
                break;
                
            case 'all':
                // Get all categories without pagination
                $sql = "SELECT category_id, {$nameField} AS category_name FROM category ORDER BY {$nameField} ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'items' => $categories
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for categories entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleCategoriesRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Shelves data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleShelvesRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get language from session or default to Swedish
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which field to use based on language
    $nameField = ($language === 'fi') ? 'shelf_fi_name' : 'shelf_sv_name';
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT shelf_id, {$nameField} AS shelf_name FROM shelf";
                $countSql = "SELECT COUNT(*) FROM shelf";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE {$nameField} LIKE :search";
                    $countSql .= " WHERE {$nameField} LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY {$nameField} ASC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $shelves = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $shelves,
                    'pagination' => $paginator->toArray()
                ];
                
                sendJsonResponse($response);
                break;
                
            case 'all':
                // Get all shelves without pagination
                $sql = "SELECT shelf_id, {$nameField} AS shelf_name FROM shelf ORDER BY {$nameField} ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $shelves = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'items' => $shelves
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for shelves entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleShelvesRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Genres data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleGenresRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get language from session or default to Swedish
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which field to use based on language
    $nameField = ($language === 'fi') ? 'genre_fi_name' : 'genre_sv_name';
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT genre_id, {$nameField} AS genre_name FROM genre";
                $countSql = "SELECT COUNT(*) FROM genre";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE {$nameField} LIKE :search";
                    $countSql .= " WHERE {$nameField} LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
            
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY {$nameField} ASC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $genres,
                    'pagination' => $paginator->toArray()
                ];
                
                sendJsonResponse($response);
                break;
                
            case 'all':
                // Get all genres without pagination
                $sql = "SELECT genre_id, {$nameField} AS genre_name FROM genre ORDER BY {$nameField} ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'items' => $genres
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for genres entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleGenresRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Conditions data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleConditionsRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get language from session or default to Swedish
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which field to use based on language
    $nameField = ($language === 'fi') ? 'condition_fi_name' : 'condition_sv_name';
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    
    try {
        switch ($action) {
            case 'list':
            case 'all':
                // For conditions, we'll always return all of them (it's a small table)
                $sql = "SELECT condition_id, {$nameField} AS condition_name, condition_code, condition_description 
                        FROM `condition` ORDER BY condition_id ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'items' => $conditions
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for conditions entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleConditionsRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Users data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleUsersRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Verify admin privileges again (belt and suspenders)
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] > 1) {
        sendErrorResponse('Unauthorized access', 403);
        exit;
    }
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT user_id, user_username, user_email, user_role, user_last_login, 
                        user_created_at, user_is_active FROM user";
                $countSql = "SELECT COUNT(*) FROM user";
                $sqlParams = [];
                
                // Add search condition if provided
                if (!empty($search)) {
                    $sql .= " WHERE user_username LIKE :search OR user_email LIKE :search";
                    $countSql .= " WHERE user_username LIKE :search OR user_email LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY user_username ASC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Format date fields
                foreach ($users as &$user) {
                    $user['user_last_login'] = $user['user_last_login'] ? 
                        $formatter->formatDate($user['user_last_login']) : 'Never';
                    $user['user_created_at'] = $formatter->formatDate($user['user_created_at']);
                    
                    // Map role ID to name
                    switch ($user['user_role']) {
                        case 1:
                            $user['role_name'] = 'Admin';
                            break;
                        case 2:
                            $user['role_name'] = 'Editor';
                            break;
                        default:
                            $user['role_name'] = 'Guest';
                            break;
                    }
                }
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $users,
                    'pagination' => $paginator->toArray()
                ];
                
                // Add HTML rendering if requested
                if (isset($params['render_html']) && $params['render_html']) {
                    $response['html'] = renderUsersTable($users);
                }
                
                sendJsonResponse($response);
                break;
                
            default:
                sendErrorResponse('Invalid action for users entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleUsersRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

/**
 * Handle Event Log data requests
 * 
 * @param array $params Request parameters
 * @param Paginator $paginator Paginator instance
 * @param Formatter $formatter Formatter instance
 * @return void
 */
function handleEventLogRequest(array $params, Paginator $paginator, Formatter $formatter): void {
    global $pdo;
    
    // Get request parameters
    $action = $params['action'] ?? 'list';
    $search = $params['search'] ?? '';
    $dateFrom = $params['date_from'] ?? '';
    $dateTo = $params['date_to'] ?? '';
    $eventType = $params['event_type'] ?? '';
    $userId = isset($params['user_id']) ? (int)$params['user_id'] : 0;
    $productId = isset($params['product_id']) ? (int)$params['product_id'] : 0;
    
    try {
        switch ($action) {
            case 'list':
                // Build the SQL query
                $sql = "SELECT el.event_id, el.event_timestamp, el.event_type, el.event_description, 
                        el.product_id, u.user_username 
                        FROM event_log el 
                        LEFT JOIN user u ON el.user_id = u.user_id";
                $countSql = "SELECT COUNT(*) FROM event_log el LEFT JOIN user u ON el.user_id = u.user_id";
                
                // Build WHERE clause
                $whereClauses = [];
                $sqlParams = [];
                
                if (!empty($search)) {
                    $whereClauses[] = "el.event_description LIKE :search";
                    $sqlParams[':search'] = "%{$search}%";
                }
                
                if (!empty($dateFrom)) {
                    $whereClauses[] = "el.event_timestamp >= :date_from";
                    $sqlParams[':date_from'] = $dateFrom . ' 00:00:00';
                }
                
                if (!empty($dateTo)) {
                    $whereClauses[] = "el.event_timestamp <= :date_to";
                    $sqlParams[':date_to'] = $dateTo . ' 23:59:59';
                }

                if (!empty($eventType)) {
                    $whereClauses[] = "el.event_type = :event_type";
                    $sqlParams[':event_type'] = $eventType;
                }
                
                if ($userId > 0) {
                    $whereClauses[] = "el.user_id = :user_id";
                    $sqlParams[':user_id'] = $userId;
                }
                
                if ($productId > 0) {
                    $whereClauses[] = "el.product_id = :product_id";
                    $sqlParams[':product_id'] = $productId;
                }
                
                // Add WHERE clause if conditions exist
                if (!empty($whereClauses)) {
                    $sql .= " WHERE " . implode(" AND ", $whereClauses);
                    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
                }
                
                // Add sorting
                if (!empty($paginator->getSortColumn())) {
                    $sql .= " " . $paginator->getOrderBySql();
                } else {
                    $sql .= " ORDER BY el.event_timestamp DESC";
                }
                
                // Add pagination
                $sql .= " " . $paginator->getLimitSql();
                
                // Get total count for pagination
                $stmt = $pdo->prepare($countSql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $totalItems = (int)$stmt->fetchColumn();
                
                // Set total items in paginator
                $paginator->setTotalItems($totalItems);
                
                // Get paginated results
                $stmt = $pdo->prepare($sql);
                if (!empty($sqlParams)) {
                    foreach ($sqlParams as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                }
                $stmt->execute();
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Format date fields
                foreach ($events as &$event) {
                    $event['event_timestamp'] = $formatter->formatDate($event['event_timestamp'], 'Y-m-d H:i:s');
                    
                    // Set user to 'System' if null
                    if (empty($event['user_username'])) {
                        $event['user_username'] = 'System';
                    }
                }
                
                // Prepare response
                $response = [
                    'success' => true,
                    'items' => $events,
                    'pagination' => $paginator->toArray()
                ];
                
                // Add HTML rendering if requested
                if (isset($params['render_html']) && $params['render_html']) {
                    $response['html'] = renderEventLogTable($events);
                }
                
                sendJsonResponse($response);
                break;
                
            case 'types':
                // Get all unique event types
                $sql = "SELECT DISTINCT event_type FROM event_log ORDER BY event_type";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Send response
                sendJsonResponse([
                    'success' => true,
                    'items' => $types
                ]);
                break;
                
            default:
                sendErrorResponse('Invalid action for event_log entity');
                break;
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Database error in handleEventLogRequest: ' . $e->getMessage());
        
        // Send error response
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
 }
 
 /**
 * Get filter parameters for products
 * 
 * @param array $params Request parameters
 * @return array Filter parameters
 */
 function getProductFilters(array $params): array {

    error_log('Raw parameters: ' . json_encode($params));
if (isset($params['category'])) {
    error_log('Category parameter: ' . $params['category'] . ' (type: ' . gettype($params['category']) . ')');
}
    return [
        'search' => $params['search'] ?? '',
        'category' => isset($params['category']) ? (int)$params['category'] : 0,
        'genre' => isset($params['genre']) ? (int)$params['genre'] : 0,
        'shelf' => isset($params['shelf']) ? (int)$params['shelf'] : 0,
        'condition' => isset($params['condition']) ? (int)$params['condition'] : 0,
        'status' => isset($params['status']) ? (int)$params['status'] : 0,
        'min_price' => isset($params['min_price']) ? (float)$params['min_price'] : 0,
        'max_price' => isset($params['max_price']) ? (float)$params['max_price'] : 0,
        'year_threshold' => isset($params['year_threshold']) ? (int)$params['year_threshold'] : 0,
        'date_from' => $params['date_from'] ?? '',
        'date_to' => $params['date_to'] ?? '',
        'special_price' => isset($params['special_price']) && $params['special_price'],
        'rare' => isset($params['rare']) && $params['rare'],
        'recommended' => isset($params['recommended']) && $params['recommended'],
        'no_price' => isset($params['no_price']) && $params['no_price'],
        'author' => isset($params['author']) ? (int)$params['author'] : 0,
        'language' => isset($params['language']) ? (int)$params['language'] : 0,
        'view_type' => $params['view_type'] ?? 'admin' // 'admin', 'public', 'lists'
    ];
 }
 
/**
 * Build the SQL query for products with filters
 * 
 * @param array $filters Filter parameters
 * @param Paginator $paginator Paginator instance
 * @return array [SQL query, Count SQL query, SQL parameters]
 */
function buildProductsQuery(array $filters, Paginator $paginator): array {
    // Get language from session or default to Swedish
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which fields to use based on language
    $categoryNameField = ($language === 'fi') ? 'cat.category_fi_name' : 'cat.category_sv_name';
    $shelfNameField = ($language === 'fi') ? 'sh.shelf_fi_name' : 'sh.shelf_sv_name';
    $statusNameField = ($language === 'fi') ? 's.status_fi_name' : 's.status_sv_name';
    $genreNameField = ($language === 'fi') ? 'g.genre_fi_name' : 'g.genre_sv_name';
    $conditionNameField = ($language === 'fi') ? 'con.condition_fi_name' : 'con.condition_sv_name';
    
    // Base SQL query
    $baseSql = "SELECT
        p.prod_id,
        p.title,
        p.status,
        {$statusNameField} as status_name,
        p.shelf_id,
        {$shelfNameField} as shelf_name,
        GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS author_name,
        {$categoryNameField} as category_name,
        p.category_id,
        GROUP_CONCAT(DISTINCT {$genreNameField} SEPARATOR ', ') AS genre_names,
        {$conditionNameField} as condition_name,
        p.price,
        IFNULL(lang.language_sv_name, '') as language,
        p.year,
        p.publisher,
        p.special_price,
        p.rare,
        p.recommended,
        p.date_added,
        (SELECT GROUP_CONCAT(i.image_path) FROM product_image pi JOIN image i ON pi.image_id = i.image_id WHERE pi.product_id = p.prod_id) AS image_paths,
        p.notes,
        p.internal_notes
    FROM product p
    LEFT JOIN product_author pa ON p.prod_id = pa.product_id
    LEFT JOIN author a ON pa.author_id = a.author_id
    JOIN category cat ON p.category_id = cat.category_id
    LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
    LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
    LEFT JOIN genre g ON pg.genre_id = g.genre_id
    JOIN `condition` con ON p.condition_id = con.condition_id
    JOIN `status` s ON p.status = s.status_id
    LEFT JOIN `language` lang ON p.language_id = lang.language_id";
    
    // Apply filters to the query
    list($sql, $sqlParams) = applyProductFilters($baseSql, $filters);
    
    // Create a GROUP BY clause to handle aggregations
    $sql .= " GROUP BY p.prod_id";
    
    // Add sorting
    if (!empty($paginator->getSortColumn())) {
        $sql .= " " . $paginator->getOrderBySql();
    } else {
        $sql .= " ORDER BY p.title ASC";
    }
    
    // Create count SQL by replacing SELECT with COUNT(DISTINCT ...)
    $countSql = "SELECT COUNT(DISTINCT p.prod_id) FROM product p
                LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN author a ON pa.author_id = a.author_id
                JOIN category cat ON p.category_id = cat.category_id
                LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
                LEFT JOIN product_genre pg ON p.prod_id = pg.product_id
                LEFT JOIN genre g ON pg.genre_id = g.genre_id
                JOIN `condition` con ON p.condition_id = con.condition_id
                JOIN `status` s ON p.status = s.status_id
                LEFT JOIN `language` lang ON p.language_id = lang.language_id";
    
    // Apply the same WHERE conditions to the count query
    list($countSql, $countParams) = applyProductFilters($countSql, $filters);
    
    // Add pagination to the main query
    $sql .= " " . $paginator->getLimitSql();
    
    // Return both queries and parameters
    return [$sql, $countSql, $sqlParams];
}
 
/**
 * Apply filter conditions to a product query
 * 
 * @param string $sql Base SQL query
 * @param array $filters Filter parameters
 * @return array [SQL query with WHERE clause, SQL parameters]
 */
function applyProductFilters(string $sql, array $filters): array {
    $whereClauses = [];
    $sqlParams = [];
    
    // Apply search filter - Enhanced to include more fields
    if (!empty($filters['search'])) {
        $searchTerm = $filters['search'];
        $whereClauses[] = "(p.title LIKE :search 
                           OR a.author_name LIKE :search 
                           OR p.notes LIKE :search 
                           OR p.publisher LIKE :search
                           OR cat.category_sv_name LIKE :search
                           OR cat.category_fi_name LIKE :search
                           OR g.genre_sv_name LIKE :search
                           OR g.genre_fi_name LIKE :search)";
        $sqlParams[':search'] = "%" . $searchTerm . "%";
    }
    
    // Apply category filter
    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        $whereClauses[] = "p.category_id = :category";
        $sqlParams[':category'] = $filters['category'];
    }
    
    // Apply genre filter
    if (!empty($filters['genre'])) {
        $whereClauses[] = "pg.genre_id = :genre";
        $sqlParams[':genre'] = $filters['genre'];
    }
    
    // Apply shelf filter
    if (!empty($filters['shelf'])) {
        $whereClauses[] = "p.shelf_id = :shelf";
        $sqlParams[':shelf'] = $filters['shelf'];
    }
    
    // Apply condition filter
    if (!empty($filters['condition'])) {
        $whereClauses[] = "p.condition_id = :condition";
        $sqlParams[':condition'] = $filters['condition'];
    }
    
    // Apply status filter
    if (!empty($filters['status'])) {
        $whereClauses[] = "p.status = :status";
        $sqlParams[':status'] = $filters['status'];
    } else {
        // Default to available products for public view
        if (isset($filters['view_type']) && $filters['view_type'] === 'public') {
            $whereClauses[] = "p.status = 1"; // 1 = Available
        }
    }
    
    // Apply price range filters
    if (!empty($filters['min_price'])) {
        $whereClauses[] = "p.price >= :min_price";
        $sqlParams[':min_price'] = $filters['min_price'];
    }
    
    if (!empty($filters['max_price'])) {
        $whereClauses[] = "p.price <= :max_price";
        $sqlParams[':max_price'] = $filters['max_price'];
    }
    
    // Apply date range filters
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "p.date_added >= :date_from";
        $sqlParams[':date_from'] = $filters['date_from'] . ' 00:00:00';
    }
    
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "p.date_added <= :date_to";
        $sqlParams[':date_to'] = $filters['date_to'] . ' 23:59:59';
    }
    
    // Apply year threshold filter
    if (!empty($filters['year_threshold'])) {
        $whereClauses[] = "p.year <= :year_threshold";
        $sqlParams[':year_threshold'] = $filters['year_threshold'];
    }
    
    // Apply special flag filters
    if ($filters['special_price']) {
        $whereClauses[] = "p.special_price = 1";
    }
    
    if ($filters['rare']) {
        $whereClauses[] = "p.rare = 1";
    }
    
    if ($filters['recommended']) {
        $whereClauses[] = "p.recommended = 1";
    }
    
    // Apply no price filter
    if ($filters['no_price']) {
        $whereClauses[] = "(p.price IS NULL OR p.price = 0)";
    }
    
    // Apply author filter
    if (!empty($filters['author'])) {
        $whereClauses[] = "pa.author_id = :author";
        $sqlParams[':author'] = $filters['author'];
    }
    
    // Apply language filter
    if (!empty($filters['language'])) {
        $whereClauses[] = "p.language_id = :language";
        $sqlParams[':language'] = $filters['language'];
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }
    
    return [$sql, $sqlParams];
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
                    p.image,
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
            $product['image_url'] = '/prog23/lagerhanteringssystem/' . $product['image'];
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
            $product['image_url'] = '/prog23/lagerhanteringssystem/' . $defaultImage;
        }
    }
    
    return $products;
}
 
 /**
 * Render products table HTML for different views
 * 
 * @param array $products Formatted products data
 * @param string $viewType View type ('admin', 'public', 'lists')
 * @return string HTML for products table
 */
 function renderProductsTable(array $products, string $viewType = 'admin'): string {
    ob_start();
    
    if (empty($products)) {
        echo '<tr><td colspan="10" class="text-center py-3">Inga produkter hittades.</td></tr>';
    } else {
        foreach ($products as $product) {
            switch ($viewType) {
                case 'admin':
                    renderAdminProductRow($product);
                    break;
                case 'public':
                    renderPublicProductRow($product);
                    break;
                case 'lists':
                    renderListsProductRow($product);
                    break;
                default:
                    renderAdminProductRow($product);
                    break;
            }
        }
    }
    
    return ob_get_clean();
 }
 
 /**
 * Render product row for admin view
 * 
 * @param array $product Product data
 * @return void
 */
 function renderAdminProductRow(array $product): void {
    $statusClass = (int)$product['status'] === 1 ? 'text-success' : 'text-danger';
    ?>
    <tr class="clickable-row" data-href="admin/adminsingleproduct.php?id=<?= safeEcho($product['prod_id']) ?>">
        <td><?= safeEcho($product['title']) ?></td>
        <td><?= safeEcho($product['author_name']) ?></td>
        <td><?= safeEcho($product['category_name']) ?></td>
        <td><?= safeEcho($product['shelf_name']) ?></td>
        <td><?= safeEcho($product['formatted_price']) ?></td>
        <td class="<?= $statusClass ?>"><?= safeEcho($product['status_name']) ?></td>
        <td>
            <?php if ($product['is_special']): ?>
                <span class="badge bg-danger">Rea</span>
            <?php endif; ?>
            <?php if ($product['is_rare']): ?>
                <span class="badge bg-warning text-dark">Sällsynt</span>
            <?php endif; ?>
            <?php if ($product['is_recommended']): ?>
                <span class="badge bg-info">Rekommenderas</span>
            <?php endif; ?>
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <?php if ((int)$product['status'] === 1): // Available ?>
                    <button class="btn btn-outline-success quick-sell" data-id="<?= safeEcho($product['prod_id']) ?>" title="Markera som såld">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                <?php else: // Sold ?>
                    <button class="btn btn-outline-warning quick-return" data-id="<?= safeEcho($product['prod_id']) ?>" title="Återställ till tillgänglig">
                        <i class="fas fa-undo"></i>
                    </button>
                <?php endif; ?>
                <a href="admin/adminsingleproduct.php?id=<?= safeEcho($product['prod_id']) ?>" class="btn btn-outline-primary" title="Redigera">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </td>
    </tr>
    <?php
 }
 
 /**
 * Render product row for public view
 * 
 * @param array $product Product data
 * @return void
 */
function renderPublicProductRow(array $product): void {
    // Format the price using a fallback if formatted_price doesn't exist
    $formattedPrice = $product['formatted_price'] ?? (isset($product['price']) ? number_format($product['price'], 2, ',', ' ') . ' €' : '');
    $productUrl = "singleproduct.php?id=" . $product['prod_id'];
    ?>
    <tr onclick="window.location='<?= $productUrl ?>';" style="cursor: pointer;">
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
 
 /**
 * Render product row for lists view
 * 
 * @param array $product Product data
 * @return void
 */
 function renderListsProductRow(array $product): void {
    $statusClass = (int)$product['status'] === 1 ? 'bg-success' : 'bg-secondary';
    ?>
    <tr>
        <td><input type="checkbox" name="list-item" value="<?= safeEcho($product['prod_id']) ?>"></td>
        <td><?= safeEcho($product['prod_id']) ?></td>
        <td><?= safeEcho($product['title']) ?></td>
        <td><?= safeEcho($product['author_name']) ?></td>
        <td><?= safeEcho($product['category_name']) ?></td>
        <td><?= safeEcho($product['shelf_name']) ?></td>
        <td><?= safeEcho($product['condition_name']) ?></td>
        <td><?= safeEcho($product['formatted_price']) ?></td>
        <td><span class="badge <?= $statusClass ?>"><?= safeEcho($product['status_name']) ?></span></td>
        <td><?= safeEcho($product['formatted_date']) ?></td>
        <td>
            <?php if ($product['is_special']): ?>
                <span class="badge bg-danger">Rea</span>
            <?php endif; ?>
            <?php if ($product['is_rare']): ?>
                <span class="badge bg-warning text-dark">Sällsynt</span>
            <?php endif; ?>
            <?php if ($product['is_recommended']): ?>
                <span class="badge bg-info">Rekommenderas</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php
 }
 
 /**
 * Render authors table HTML
 * 
 * @param array $authors Authors data
 * @return string HTML for authors table
 */
 function renderAuthorsTable(array $authors): string {
    ob_start();
    
    if (empty($authors)) {
        echo '<tr><td colspan="3" class="text-center py-3">Inga författare hittades.</td></tr>';
    } else {
        foreach ($authors as $author) {
            ?>
            <tr>
                <td><?= safeEcho($author['author_id']) ?></td>
                <td><?= safeEcho($author['author_name']) ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="#" class="btn btn-outline-primary edit-item" 
                           data-id="<?= safeEcho($author['author_id']) ?>" 
                           data-type="author" 
                           data-name="<?= safeEcho($author['author_name']) ?>">Redigera</a>
                        <a href="javascript:void(0);" class="btn btn-outline-danger delete-item" 
                           data-id="<?= safeEcho($author['author_id']) ?>" 
                           data-type="author">Ta bort</a>
                    </div>
                </td>
            </tr>
            <?php
        }
    }
    
    return ob_get_clean();
 }
 
 /**
 * Render users table HTML
 * 
 * @param array $users Users data
 * @return string HTML for users table
 */
 function renderUsersTable(array $users): string {
    ob_start();
    
    if (empty($users)) {
        echo '<tr><td colspan="6" class="text-center py-3">Inga användare hittades.</td></tr>';
    } else {
        foreach ($users as $user) {
            $statusClass = (int)$user['user_is_active'] === 1 ? 'text-success' : 'text-danger';
            $statusText = (int)$user['user_is_active'] === 1 ? 'Aktiv' : 'Inaktiv';
            ?>
            <tr class="clickable-row" data-href="admin/usermanagement.php?tab=edit&user_id=<?= safeEcho($user['user_id']) ?>">
                <td><?= safeEcho($user['user_username']) ?></td>
                <td><?= safeEcho($user['user_email']) ?></td>
                <td><?= safeEcho($user['role_name']) ?></td>
                <td class="<?= $statusClass ?>"><?= $statusText ?></td>
                <td><?= safeEcho($user['user_last_login']) ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="admin/usermanagement.php?tab=edit&user_id=<?= safeEcho($user['user_id']) ?>" class="btn btn-outline-primary" title="Redigera">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php
        }
    }
    
    return ob_get_clean();
 }
 
 /**
 * Render event log table HTML
 * 
 * @param array $events Event log data
 * @return string HTML for event log table
 */
 function renderEventLogTable(array $events): string {
    ob_start();
    
    if (empty($events)) {
        echo '<tr><td colspan="5" class="text-center py-3">Inga händelser hittades.</td></tr>';
    } else {
        foreach ($events as $event) {
            // Determine event type label and class
            $eventTypeLabel = '';
            $eventTypeClass = '';
            
            switch ($event['event_type']) {
                case 'create':
                    $eventTypeLabel = 'Skapat';
                    $eventTypeClass = 'bg-success';
                    break;
                case 'update':
                    $eventTypeLabel = 'Uppdaterat';
                    $eventTypeClass = 'bg-primary';
                    break;
                case 'delete':
                    $eventTypeLabel = 'Raderat';
                    $eventTypeClass = 'bg-danger';
                    break;
                case 'login':
                    $eventTypeLabel = 'Inloggning';
                    $eventTypeClass = 'bg-info';
                    break;
                case 'logout':
                    $eventTypeLabel = 'Utloggning';
                    $eventTypeClass = 'bg-secondary';
                    break;
                case 'database_backup':
                    $eventTypeLabel = 'Databas backup';
                    $eventTypeClass = 'bg-warning text-dark';
                    break;
                default:
                    $eventTypeLabel = ucfirst($event['event_type']);
                    $eventTypeClass = 'bg-secondary';
                    break;
            }
            ?>
            <tr>
                <td><?= safeEcho($event['event_timestamp']) ?></td>
                <td><span class="badge <?= $eventTypeClass ?>"><?= safeEcho($eventTypeLabel) ?></span></td>
                <td><?= safeEcho($event['user_username']) ?></td>
                <td><?= safeEcho($event['event_description']) ?></td>
                <td>
                    <?php if (!empty($event['product_id'])): ?>
                        <a href="admin/adminsingleproduct.php?id=<?= safeEcho($event['product_id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Visa produkt
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }
    }
    
    return ob_get_clean();
 }
 
 /**
 * Send JSON response
 * 
 * @param array $data Response data
 * @param int $statusCode HTTP status code
 * @return void
 */
 function sendJsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
 }
 
 /**
 * Send error response
 * 
 * @param string $message Error message
 * @param int $statusCode HTTP status code
 * @return void
 */
 function sendErrorResponse(string $message, int $statusCode = 400): void {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
 }
 
