<?php
/**
 * Add/Manage Authors for Admin
 * 
 * Provides comprehensive author management functionality including:
 * - Author creation with duplicate validation
 * - Search and filter functionality with pagination
 * - Author editing with conflict checking
 * - Safe author deletion with dependency validation
 * - AJAX-enabled interface for seamless user experience
 * 
 * Security Features:
 * - Input validation and sanitization
 * - SQL injection protection via prepared statements
 * - XSS prevention through proper output encoding
 * - Authentication and authorization checks
 * - Activity logging for audit trails
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    2.1
 * @since      2.0
 * @requires   PHP 7.4+
 * @requires   PDO MySQL extension
 */

// Include initialization file
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has admin or editor permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Determine if request is AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/**
 * Modern input sanitization function to replace deprecated FILTER_SANITIZE_STRING
 * 
 * @param string $input Input string to sanitize
 * @param int $maxLength Maximum allowed length
 * @return string Sanitized string
 */
function sanitizeString($input, $maxLength = 255) {
    if (!is_string($input)) {
        return '';
    }
    
    // Remove null bytes and control characters
    $input = str_replace(chr(0), '', $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    
    // Trim whitespace
    $input = trim($input);
    
    // Limit length
    if (strlen($input) > $maxLength) {
        $input = substr($input, 0, $maxLength);
    }
    
    return $input;
}

/**
 * Get authors with advanced filtering and pagination
 * 
 * Retrieves authors from database with support for search filtering,
 * sorting, and pagination. Includes comprehensive error handling and
 * input validation.
 * 
 * @param string $searchTerm Search term for filtering author names
 * @param string $sortBy Sort field (name|id) - defaults to 'name'
 * @param string $sortOrder Sort order (asc|desc) - defaults to 'asc'
 * @param int $page Page number (1-based) - defaults to 1
 * @param int $limit Items per page (1-100) - defaults to 20
 * @return array Associative array containing 'authors' and 'pagination' data
 * @throws PDOException When database operation fails
 * @since 2.0
 */
function getAuthors($searchTerm = '', $sortBy = 'name', $sortOrder = 'asc', $page = 1, $limit = 20) {
    global $pdo;
    
    // Input validation and sanitization
    $searchTerm = sanitizeString($searchTerm, 100);
    $sortBy = in_array($sortBy, ['name', 'id']) ? $sortBy : 'name';
    $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'asc';
    $page = max(1, (int)$page);
    $limit = max(1, min(100, (int)$limit)); // Cap at 100 items per page
    
    try {
        // Build WHERE clause with proper parameterization
        $whereConditions = [];
        $params = [];
        
        // Search term filter - only add if not empty
        if (!empty($searchTerm)) {
            $whereConditions[] = "author_name LIKE :search";
            $params[':search'] = '%' . $searchTerm . '%';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Determine sort column with whitelist validation
        $sortColumn = ($sortBy === 'id') ? 'author_id' : 'author_name';
        $sortDirection = strtoupper($sortOrder);
        
        // Count total records for pagination
        $countSql = "SELECT COUNT(*) as total FROM author " . $whereClause;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calculate pagination values
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        
        // Prepare main query with proper parameterization
        $sql = "
            SELECT 
                author_id,
                author_name
            FROM author
            {$whereClause}
            ORDER BY {$sortColumn} {$sortDirection}
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind search parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        
        // Bind pagination parameters
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Sanitize output data
        $sanitizedAuthors = [];
        foreach ($authors as $author) {
            $sanitizedAuthors[] = [
                'author_id' => (int)$author['author_id'],
                'author_name' => htmlspecialchars($author['author_name'], ENT_QUOTES, 'UTF-8')
            ];
        }
        
        return [
            'authors' => $sanitizedAuthors,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalRecords,
                'itemsPerPage' => $limit,
                'firstRecord' => $totalRecords > 0 ? $offset + 1 : 0,
                'lastRecord' => min($offset + $limit, $totalRecords)
            ]
        ];
        
    } catch (PDOException $e) {
        // Return safe error response without exposing database details
        return [
            'authors' => [],
            'pagination' => [
                'currentPage' => 1,
                'totalPages' => 0,
                'totalItems' => 0,
                'itemsPerPage' => $limit,
                'firstRecord' => 0,
                'lastRecord' => 0
            ],
            'error' => 'Database error occurred while retrieving authors.'
        ];
    }
}

/**
 * Create new author with validation and duplicate checking
 * 
 * Creates a new author entry in the database after performing
 * comprehensive validation and duplicate checking. Logs the
 * creation event for audit purposes.
 * 
 * @param string $authorName The full name of the author to create
 * @return array Result array with 'success' boolean and 'message' string
 * @throws PDOException When database operation fails
 * @since 2.0
 */

 function createAuthor($authorName) {
    global $pdo;
    
    // Input validation and sanitization
    $authorName = sanitizeString($authorName, 255);
    
    // Validate required field
    if (empty($authorName)) {
        return [
            'success' => false,
            'message' => 'Vänligen fyll i namnet.'
        ];
    }
    
    // Validate name length (reasonable limits)
    if (strlen($authorName) > 255) {
        return [
            'success' => false,
            'message' => 'Författarens namn får inte vara längre än 255 tecken.'
        ];
    }
    
    try {
        // Check if author already exists (case-insensitive)
        $checkStmt = $pdo->prepare("SELECT author_id FROM author WHERE LOWER(author_name) = LOWER(:name)");
        $checkStmt->bindValue(':name', $authorName, PDO::PARAM_STR);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'Författaren finns redan i databasen.'
            ];
        }
        
        // Insert new author with prepared statement
        $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (:name)");
        $stmt->bindValue(':name', $authorName, PDO::PARAM_STR);
        $insertSuccess = $stmt->execute();
        
        // Check if the insert was actually successful
        if (!$insertSuccess || $stmt->rowCount() === 0) {
            return [
                'success' => false,
                'message' => 'Databasfel: Kunde inte lägga till författaren.'
            ];
        }
        
        // Get the inserted author ID for verification
        $newAuthorId = $pdo->lastInsertId();
        
        // Optional: Log the action for audit trail (but don't fail if logging fails)
        try {
            $currentUser = getSessionUser();
            if ($currentUser && isset($currentUser['user_id'])) {
                $logStmt = $pdo->prepare("
                    INSERT INTO event_log (user_id, event_type, event_description, created_at)
                    VALUES (:user_id, :event_type, :description, NOW())
                ");
                $logStmt->bindValue(':user_id', (int)$currentUser['user_id'], PDO::PARAM_INT);
                $logStmt->bindValue(':event_type', 'create_author', PDO::PARAM_STR);
                $logStmt->bindValue(':description', 'Author created: ' . $authorName, PDO::PARAM_STR);
                $logStmt->execute();
            }
        } catch (PDOException $logError) {
            // Log the logging error but don't fail the main operation
            // The author was successfully created even if logging failed
        }
        
        return [
            'success' => true,
            'message' => 'Författare tillagd i databasen!',
            'author_id' => $newAuthorId
        ];
        
    } catch (PDOException $e) {
        // Check if this is a duplicate key error (alternative check)
        if ($e->getCode() == 23000) { // Integrity constraint violation
            return [
                'success' => false,
                'message' => 'Författaren finns redan i databasen.'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte lägga till författaren.'
        ];
    }
}

/**
 * Update existing author with validation and conflict checking
 * 
 * Updates an author's information after performing validation,
 * existence checking, and duplicate name checking. Logs the
 * update event for audit purposes.
 * 
 * @param int $authorId The ID of the author to update
 * @param string $authorName The new name for the author
 * @return array Result array with 'success' boolean and 'message' string
 * @throws PDOException When database operation fails
 * @since 2.0
 */
function updateAuthor($authorId, $authorName) {
    global $pdo;
    
    // Input validation and sanitization
    $authorId = (int)$authorId;
    $authorName = sanitizeString($authorName, 255);
    
    // Validate inputs
    if ($authorId <= 0) {
        return [
            'success' => false,
            'message' => 'Ogiltigt författar-ID.'
        ];
    }
    
    if (empty($authorName)) {
        return [
            'success' => false,
            'message' => 'Vänligen fyll i namnet.'
        ];
    }
    
    if (strlen($authorName) > 255) {
        return [
            'success' => false,
            'message' => 'Författarens namn får inte vara längre än 255 tecken.'
        ];
    }
    
    try {
        // Get original name for logging and existence check
        $getStmt = $pdo->prepare("SELECT author_name FROM author WHERE author_id = :id");
        $getStmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $getStmt->execute();
        $originalName = $getStmt->fetchColumn();
        
        if (!$originalName) {
            return [
                'success' => false,
                'message' => 'Författaren hittades inte.'
            ];
        }
        
        // Check if new name already exists (excluding current author, case-insensitive)
        $checkStmt = $pdo->prepare("
            SELECT author_id 
            FROM author 
            WHERE LOWER(author_name) = LOWER(:name) AND author_id != :id
        ");
        $checkStmt->bindValue(':name', $authorName, PDO::PARAM_STR);
        $checkStmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'En författare med detta namn finns redan.'
            ];
        }
        
        // Update author with prepared statement
        $stmt = $pdo->prepare("UPDATE author SET author_name = :name WHERE author_id = :id");
        $stmt->bindValue(':name', $authorName, PDO::PARAM_STR);
        $stmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Log the action for audit trail
        $currentUser = getSessionUser();
        if ($currentUser && isset($currentUser['user_id'])) {
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description, created_at)
                VALUES (:user_id, :event_type, :description, NOW())
            ");
            $logStmt->bindValue(':user_id', (int)$currentUser['user_id'], PDO::PARAM_INT);
            $logStmt->bindValue(':event_type', 'update_author', PDO::PARAM_STR);
            $logStmt->bindValue(':description', "Author updated: '{$originalName}' to '{$authorName}'", PDO::PARAM_STR);
            $logStmt->execute();
        }
        
        return [
            'success' => true,
            'message' => 'Författaren har uppdaterats.'
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte uppdatera författaren.'
        ];
    }
}

/**
 * Delete author with dependency validation
 * 
 * Safely deletes an author after checking for dependencies
 * in related tables. Prevents deletion if author is referenced
 * by products. Logs the deletion event for audit purposes.
 * 
 * @param int $authorId The ID of the author to delete
 * @return array Result array with 'success' boolean and 'message' string
 * @throws PDOException When database operation fails
 * @since 2.0
 */
function deleteAuthor($authorId) {
    global $pdo;
    
    // Input validation
    $authorId = (int)$authorId;
    
    if ($authorId <= 0) {
        return [
            'success' => false,
            'message' => 'Ogiltigt författar-ID.'
        ];
    }
    
    try {
        // Get author name for logging and existence check
        $getStmt = $pdo->prepare("SELECT author_name FROM author WHERE author_id = :id");
        $getStmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $getStmt->execute();
        $authorName = $getStmt->fetchColumn();
        
        if (!$authorName) {
            return [
                'success' => false,
                'message' => 'Författaren hittades inte.'
            ];
        }
        
        // Check if author is used in any products (dependency validation)
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM product_author WHERE author_id = :id");
        $checkStmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $checkStmt->execute();
        $productCount = (int)$checkStmt->fetchColumn();
        
        if ($productCount > 0) {
            return [
                'success' => false,
                'message' => "Kan inte ta bort författaren eftersom den används i {$productCount} produkt(er)."
            ];
        }
        
        // Delete author with prepared statement
        $stmt = $pdo->prepare("DELETE FROM author WHERE author_id = :id");
        $stmt->bindValue(':id', $authorId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Log the action for audit trail
        $currentUser = getSessionUser();
        if ($currentUser && isset($currentUser['user_id'])) {
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description, created_at)
                VALUES (:user_id, :event_type, :description, NOW())
            ");
            $logStmt->bindValue(':user_id', (int)$currentUser['user_id'], PDO::PARAM_INT);
            $logStmt->bindValue(':event_type', 'delete_author', PDO::PARAM_STR);
            $logStmt->bindValue(':description', 'Author deleted: ' . $authorName, PDO::PARAM_STR);
            $logStmt->execute();
        }
        
        return [
            'success' => true,
            'message' => 'Författaren har tagits bort.'
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte ta bort författaren.'
        ];
    }
}

// Handle AJAX GET requests for author data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    // Sanitize and validate input parameters
    $searchTerm = isset($_GET['search']) ? sanitizeString($_GET['search'], 100) : '';
    $sortBy = isset($_GET['sort_by']) ? sanitizeString($_GET['sort_by'], 10) : 'name';
    $sortOrder = isset($_GET['sort_order']) ? sanitizeString($_GET['sort_order'], 10) : 'asc';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 20;
    
    $result = getAuthors($searchTerm, $sortBy, $sortOrder, $page, $limit);
    
    // Set proper JSON header and return response
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    echo json_encode([
        'success' => !isset($result['error']),
        'authors' => $result['authors'],
        'pagination' => $result['pagination'],
        'error' => $result['error'] ?? null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Handle POST requests for author management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize action parameter
    $action = isset($_POST['action']) ? sanitizeString($_POST['action'], 20) : '';
    
    // CSRF protection would typically be implemented here
    // if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    //     http_response_code(403);
    //     exit('Invalid CSRF token');
    // }
    
    if ($action === 'create_author') {
        $authorName = isset($_POST['author_name']) ? sanitizeString($_POST['author_name'], 255) : '';
        $result = createAuthor($authorName);
        
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
    } elseif ($action === 'update_author') {
        $authorId = isset($_POST['author_id']) ? (int)$_POST['author_id'] : 0;
        $authorName = isset($_POST['author_name']) ? sanitizeString($_POST['author_name'], 255) : '';
        $result = updateAuthor($authorId, $authorName);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
        
    } elseif ($action === 'delete_author') {
        $authorId = isset($_POST['author_id']) ? (int)$_POST['author_id'] : 0;
        $result = deleteAuthor($authorId);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        // Invalid action
        http_response_code(400);
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Ogiltig åtgärd.'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
?>

<div class="tab-pane fade show active" id="add-author">
    
    <!-- Add Author Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Lägg till ny författare</h5>
        </div>
        <div class="card-body">
            <form id="add-author-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="author_name" class="form-label">Författarens namn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="author_name" name="author_name" 
                               placeholder="Ange författarens fullständiga namn..." 
                               maxlength="255" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Lägg till författare
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Search and Filter Form -->
    <form id="author-filter-form" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="author-search-term" class="form-label">Sök författare</label>
                <input type="text" class="form-control" id="author-search-term" name="search" 
                    placeholder="Sök efter författarens namn..." maxlength="100">
            </div>
            <div class="col-md-3">
                <label for="sort-by" class="form-label">Sortera efter</label>
                <select class="form-select" id="sort-by" name="sort_by">
                    <option value="name">Namn</option>
                    <option value="id">ID</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sort-order" class="form-label">Ordning</label>
                <select class="form-select" id="sort-order" name="sort_order">
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100" title="Sök">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Authors Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="authors-table">
            <thead class="table-light">
                <tr>
                    <th width="80">ID</th>
                    <th>Författarens namn</th>
                    <th width="150">Åtgärder</th>
                </tr>
            </thead>
            <tbody id="authors-table-body">
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Laddar...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination controls -->
    <div class="mt-3" id="author-pagination-controls">
        <div class="row align-items-center">
            <!-- Page info -->
            <div class="col-md-6 mb-2 mb-md-0">
                <div id="author-pagination-info">
                    Visar <span id="author-showing-start">0</span> till 
                    <span id="author-showing-end">0</span> av 
                    <span id="author-total-items">0</span> författare
                </div>
            </div>
            
            <!-- Page navigation -->
            <div class="col-md-6 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="author-pagination-links">
                    <!-- Pagination links will be inserted here by JavaScript -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Edit Author Modal -->
<div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAuthorModalLabel">Redigera författare</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Stäng"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-author-id">
                <div class="mb-3">
                    <label for="edit-author-name" class="form-label">Författarens namn <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-author-name" 
                           maxlength="255" required>
                    <div class="form-text">Maximalt 255 tecken</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="save-author-edit">Spara ändringar</button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Author Management JavaScript Module
 * 
 * Handles all client-side interactions for the author management interface
 * including form submissions, table updates, pagination, and modal operations.
 * 
 * @author Axxell
 * @version 2.1
 * @requires jQuery
 * @requires Bootstrap 5
 */

$(document).ready(function() {
    /**
     * Initialize the author management interface
     * Load initial data and set up event handlers
     */
    loadAuthors();
    
    /**
     * Handle add author form submission
     * Validates input and submits via AJAX
     */
    $('#add-author-form').on('submit', function(e) {
        e.preventDefault();
        
        const authorName = $('#author_name').val().trim();
        if (!authorName) {
            showMessage('Vänligen fyll i författarens namn.', 'warning');
            return;
        }
        
        if (authorName.length > 255) {
            showMessage('Författarens namn får inte vara längre än 255 tecken.', 'warning');
            return;
        }
        
        // Disable form during submission
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Lägger till...');
        
        $.ajax({
            url: BASE_URL + '/admin/addauthor.php',
            type: 'POST',
            data: {
                action: 'create_author',
                author_name: authorName
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showMessage(data.message, 'success');
                    $('#author_name').val(''); // Clear form
                    loadAuthors(); // Reload table
                } else {
                    showMessage(data.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showMessage('Ett fel inträffade vid tillägg av författare', 'danger');
            },
            complete: function() {
                // Re-enable form
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    /**
     * Handle filter form submission
     * Triggers author reload with current filter settings
     */
    $('#author-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadAuthors();
    });
    
    /**
     * Handle sort/filter changes
     * Auto-reload authors when sort options change
     */
    $('#sort-by, #sort-order').on('change', function() {
        loadAuthors();
    });
    
    /**
     * Handle edit author modal save button
     * Validates and submits author updates
     */
    $('#save-author-edit').on('click', function() {
        const authorId = $('#edit-author-id').val();
        const authorName = $('#edit-author-name').val().trim();
        
        if (!authorName) {
            showMessage('Vänligen fyll i författarens namn.', 'warning');
            return;
        }
        
        if (authorName.length > 255) {
            showMessage('Författarens namn får inte vara längre än 255 tecken.', 'warning');
            return;
        }
        
        // Disable save button during submission
        const saveBtn = $(this);
        const originalText = saveBtn.html();
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sparar...');
        
        $.ajax({
            url: BASE_URL + '/admin/addauthor.php',
            type: 'POST',
            data: {
                action: 'update_author',
                author_id: authorId,
                author_name: authorName
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showMessage(data.message, 'success');
                    $('#editAuthorModal').modal('hide');
                    loadAuthors(); // Reload table
                } else {
                    showMessage(data.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showMessage('Ett fel inträffade vid uppdatering av författare', 'danger');
            },
            complete: function() {
                // Re-enable save button
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

/**
 * Load authors with current filter and pagination settings
 * 
 * Fetches author data from server and updates the table display.
 * Handles loading states, error conditions, and pagination updates.
 * 
 * @param {number} page - Page number to load (defaults to 1)
 */
function loadAuthors(page = 1) {
    const searchTerm = $('#author-search-term').val().trim();
    const sortBy = $('#sort-by').val();
    const sortOrder = $('#sort-order').val();
    const limit = 20; // Fixed limit for consistent UI
    
    // Validate page parameter
    page = Math.max(1, parseInt(page) || 1);
    
    // Show loading state
    $('#authors-table-body').html(`
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    // Clear pagination during loading
    $('#author-pagination-links').html('');
    
    // Make AJAX request with proper error handling
    $.ajax({
        url: BASE_URL + '/admin/addauthor.php',
        type: 'GET',
        data: {
            ajax: 1,
            search: searchTerm,
            sort_by: sortBy,
            sort_order: sortOrder,
            page: page,
            limit: limit
        },
        dataType: 'json',
        timeout: 10000, // 10 second timeout
        success: function(data) {
            if (data.success) {
                renderAuthors(data.authors);
                updateAuthorPagination(data.pagination);
            } else {
                const errorMessage = data.error || 'Ett fel inträffade';
                $('#authors-table-body').html(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}
                        </td>
                    </tr>
                `);
                showMessage(errorMessage, 'danger');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid hämtning av data';
            
            if (status === 'timeout') {
                errorMessage = 'Förfrågan tog för lång tid - försök igen';
            } else if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att visa denna data';
            } else if (xhr.status === 500) {
                errorMessage = 'Serverfel - kontakta administratören';
            }
            
            $('#authors-table-body').html(`
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}
                        <br><small class="text-muted mt-1">
                            <a href="#" onclick="loadAuthors(${page}); return false;">Försök igen</a>
                        </small>
                    </td>
                </tr>
            `);
            showMessage(errorMessage, 'danger');
        }
    });
}

/**
 * Render authors in the table
 * 
 * Creates table rows for author data and attaches event handlers
 * for edit and delete operations. Handles empty states gracefully.
 * 
 * @param {Array} authors - Array of author objects to display
 */
function renderAuthors(authors) {
    if (!authors || authors.length === 0) {
        $('#authors-table-body').html(`
            <tr>
                <td colspan="3" class="text-center text-muted">
                    <i class="fas fa-search me-2"></i>Inga författare hittades
                    <br><small>Prova att ändra sökkriterier eller lägg till en ny författare</small>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    
    authors.forEach(author => {
        // Escape data for safe HTML output (double-encoding protection)
        const authorId = parseInt(author.author_id);
        const authorName = $('<div>').text(author.author_name).html(); // jQuery text() handles escaping
        
        if (isNaN(authorId) || !authorName) {
            return; // Skip invalid entries
        }
        
        html += `
        <tr data-author-id="${authorId}">
            <td>${authorId}</td>
            <td>${authorName}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group" aria-label="Författaråtgärder">
                    <button class="btn btn-outline-primary edit-author" 
                            data-id="${authorId}"
                            data-name="${authorName}"
                            title="Redigera författare"
                            aria-label="Redigera ${authorName}">
                        <i class="fas fa-edit me-1"></i>Redigera
                    </button>
                    <button class="btn btn-outline-danger delete-author" 
                            data-id="${authorId}"
                            data-name="${authorName}"
                            title="Ta bort författare"
                            aria-label="Ta bort ${authorName}">
                        <i class="fas fa-trash me-1"></i>Ta bort
                    </button>
                </div>
            </td>
        </tr>`;
    });
    
    $('#authors-table-body').html(html);
    
    // Attach event handlers for edit buttons
    $('.edit-author').on('click', function() {
        const authorId = $(this).data('id');
        const authorName = $(this).data('name');
        
        if (!authorId || !authorName) {
            showMessage('Felaktig författardata', 'danger');
            return;
        }
        
        $('#edit-author-id').val(authorId);
        $('#edit-author-name').val(authorName);
        $('#editAuthorModal').modal('show');
        
        // Focus on name input when modal opens
        $('#editAuthorModal').on('shown.bs.modal', function() {
            $('#edit-author-name').focus().select();
        });
    });
    
    // Attach event handlers for delete buttons
    $('.delete-author').on('click', function() {
        const authorId = $(this).data('id');
        const authorName = $(this).data('name');
        
        if (!authorId || !authorName) {
            showMessage('Felaktig författardata', 'danger');
            return;
        }
        
        // Enhanced confirmation dialog
        const confirmed = confirm(
            `Är du säker på att du vill ta bort författaren "${authorName}"?\n\n` +
            `Denna åtgärd kan inte ångras. Författaren kan endast tas bort om den inte ` +
            `används i några produkter.`
        );
        
        if (confirmed) {
            deleteAuthor(authorId);
        }
    });
}

/**
 * Delete author with confirmation and error handling
 * 
 * Sends delete request to server and handles response.
 * Provides user feedback and reloads table on success.
 * 
 * @param {number} authorId - ID of author to delete
 */
function deleteAuthor(authorId) {
    if (!authorId || isNaN(authorId)) {
        showMessage('Ogiltigt författar-ID', 'danger');
        return;
    }
    
    // Find and disable the delete button during operation
    const deleteBtn = $(`.delete-author[data-id="${authorId}"]`);
    const originalText = deleteBtn.html();
    deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Tar bort...');
    
    $.ajax({
        url: BASE_URL + '/admin/addauthor.php',
        type: 'POST',
        data: {
            action: 'delete_author',
            author_id: authorId
        },
        dataType: 'json',
        timeout: 10000,
        success: function(data) {
            if (data.success) {
                showMessage(data.message, 'success');
                
                // Remove row with animation
                const row = $(`tr[data-author-id="${authorId}"]`);
                row.fadeOut(300, function() {
                    loadAuthors(); // Reload to update pagination
                });
            } else {
                showMessage(data.message, 'danger');
                // Re-enable button on failure
                deleteBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid borttagning av författare';
            
            if (status === 'timeout') {
                errorMessage = 'Förfrågan tog för lång tid - försök igen';
            } else if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att ta bort författare';
            }
            
            showMessage(errorMessage, 'danger');
            // Re-enable button on error
            deleteBtn.prop('disabled', false).html(originalText);
        }
    });
}

/**
 * Update pagination controls for authors
 * 
 * Generates pagination UI based on current page and total pages.
 * Handles edge cases and creates accessible navigation controls.
 * 
 * @param {Object} pagination - Pagination data from server
 */
function updateAuthorPagination(pagination) {
    if (!pagination) {
        $('#author-pagination-controls').hide();
        return;
    }
    
    // Update page information display
    $('#author-showing-start').text(pagination.firstRecord || 0);
    $('#author-showing-end').text(pagination.lastRecord || 0);
    $('#author-total-items').text(pagination.totalItems || 0);
    
    // Show/hide pagination controls based on data
    if (pagination.totalPages <= 1) {
        $('#author-pagination-links').html('');
        if (pagination.totalItems === 0) {
            $('#author-pagination-controls').hide();
        } else {
            $('#author-pagination-controls').show();
        }
        return;
    }
    
    $('#author-pagination-controls').show();
    
    // Generate pagination links with improved accessibility
    let html = '';
    const currentPage = pagination.currentPage;
    const totalPages = pagination.totalPages;
    
    // Previous page button
    const prevDisabled = currentPage <= 1;
    html += `
        <li class="page-item ${prevDisabled ? 'disabled' : ''}">
            <a class="page-link author-page-link" href="#" 
               data-page="${currentPage - 1}" 
               ${prevDisabled ? 'tabindex="-1" aria-disabled="true"' : ''}
               aria-label="Föregående sida">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Calculate page range for display
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    // Adjust start if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // First page and ellipsis
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link author-page-link" href="#" data-page="1" aria-label="Sida 1">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">...</span>
                </li>
            `;
        }
    }
    
    // Page number buttons
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === currentPage;
        html += `
            <li class="page-item ${isActive ? 'active' : ''}">
                <a class="page-link author-page-link" href="#" 
                   data-page="${i}" 
                   aria-label="Sida ${i}"
                   ${isActive ? 'aria-current="page"' : ''}>${i}</a>
            </li>
        `;
    }
    
    // Last page and ellipsis
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">...</span>
                </li>
            `;
        }
        html += `
            <li class="page-item">
                <a class="page-link author-page-link" href="#" data-page="${totalPages}" aria-label="Sida ${totalPages}">${totalPages}</a>
            </li>
        `;
    }
    
    // Next page button
    const nextDisabled = currentPage >= totalPages;
    html += `
        <li class="page-item ${nextDisabled ? 'disabled' : ''}">
            <a class="page-link author-page-link" href="#" 
               data-page="${currentPage + 1}" 
               ${nextDisabled ? 'tabindex="-1" aria-disabled="true"' : ''}
               aria-label="Nästa sida">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    $('#author-pagination-links').html(html);
    
    // Attach click handlers to pagination links
    $('.author-page-link').on('click', function(e) {
        e.preventDefault();
        
        const page = parseInt($(this).data('page'), 10);
        if (!isNaN(page) && page > 0 && page <= totalPages) {
            loadAuthors(page);
        }
    });
}

/**
 * Show message to user with enhanced functionality
 * 
 * Displays messages in a consistent, accessible format with
 * automatic dismissal and proper ARIA attributes.
 * 
 * @param {string} message - Message text to display
 * @param {string} type - Bootstrap alert type (success, danger, warning, info)
 */
function showMessage(message, type = 'info') {
    if (!message) return;
    
    const messageContainer = $('#message-container');
    if (!messageContainer.length) {
        // Create message container if it doesn't exist
        $('body').prepend('<div id="message-container" class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999; margin-top: 20px;"></div>');
    }
    
    // Clear previous messages
    messageContainer.html('');
    
    // Sanitize message for display
    const safeMessage = $('<div>').text(message).html();
    
    // Create message element with proper accessibility
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-sm" 
             role="alert" aria-live="polite" style="min-width: 300px; max-width: 500px;">
            <i class="fas fa-${getIconForType(type)} me-2" aria-hidden="true"></i>
            ${safeMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" 
                    aria-label="Stäng meddelande"></button>
        </div>
    `;
    
    // Add to container and show with animation
    messageContainer.html(alertHtml).show();
    
    // Auto-hide after 5 seconds (except for errors)
    if (type !== 'danger') {
        setTimeout(function() {
            const alert = $(`#${alertId}`);
            if (alert.length) {
                alert.removeClass('show');
                setTimeout(function() {
                    alert.remove();
                    
                    // Hide container if empty
                    if (messageContainer.children().length === 0) {
                        messageContainer.hide();
                    }
                }, 150);
            }
        }, 5000);
    }
}

/**
 * Get appropriate icon for message type
 * 
 * @param {string} type - Message type
 * @returns {string} FontAwesome icon class
 */
function getIconForType(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'danger': return 'exclamation-triangle';
        case 'warning': return 'exclamation-circle';
        case 'info': 
        default: return 'info-circle';
    }
}
</script>