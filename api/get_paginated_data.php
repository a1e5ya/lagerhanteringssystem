<?php

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
            // For products, delegate to the specialized product API endpoints
            if ($viewType === 'public') {
                // Redirect to public products API
                header('Location: get_public_products.php?' . http_build_query($params));
                exit;
            } else {
                // Redirect to admin products API
                header('Location: ../admin/get_products.php?' . http_build_query($params));
                exit;
            }
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