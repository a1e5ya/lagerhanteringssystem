<?php
/**
 * Product Operations Log for Admin
 * 
 * Provides comprehensive logging and tracking for product-related operations
 * including CRUD operations, batch operations, and author management.
 * Features secure filtering, pagination, and AJAX-powered interface.
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    1.2
 * @since      1.0
 */

// Include initialization file
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has admin or editor permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

/**
 * Get product operations log with comprehensive filtering and pagination
 * 
 * Retrieves filtered log entries for product-related operations including
 * product CRUD, author management, and batch operations with secure
 * parameter binding and input validation.
 * 
 * @param string $searchTerm Search term for filtering descriptions, usernames, and product titles
 * @param string $eventType Event type filter (all, create, update, etc.)
 * @param int $page Current page number for pagination
 * @param int $limit Number of items per page
 * @return array Associative array containing log entries and pagination data
 * @throws PDOException When database query fails
 */
function getProductOperationsLog($searchTerm = '', $eventType = 'all', $page = 1, $limit = 20) {
    global $pdo;
    
    try {
        // Validate and sanitize input parameters
        $searchTerm = sanitizeInput($searchTerm, 'string', 255);
        $eventType = sanitizeInput($eventType, 'string', 50);
        $page = sanitizeInput($page, 'int', null, ['min' => 1]) ?: 1;
        $limit = sanitizeInput($limit, 'int', null, ['min' => 1, 'max' => 200]) ?: 20;
        
        // Build WHERE clause with secure parameter binding
        $whereConditions = [];
        $params = [];
        
        // Define allowed product event types (whitelist approach)
        $productEventTypes = [
            'create', 'update', 'sell', 'return', 'delete',
            'create_author', 'update_author', 'delete_author',
            'batch_update_status', 'batch_update_price', 'batch_move_shelf',
            'batch_set_special_price', 'batch_set_rare', 'batch_set_recommended',
            'batch_delete'
        ];
        
        // Create placeholders for IN clause
        $placeholders = [];
        foreach ($productEventTypes as $index => $type) {
            $placeholders[] = ':event_type_' . $index;
            $params[':event_type_' . $index] = $type;
        }
        $whereConditions[] = "el.event_type IN (" . implode(', ', $placeholders) . ")";
        
        // Additional event type filter with validation
        if ($eventType !== 'all' && in_array($eventType, $productEventTypes)) {
            $whereConditions[] = "el.event_type = :selected_event_type";
            $params[':selected_event_type'] = $eventType;
        }
        
        // Search term filter with secure parameter binding
        if (!empty($searchTerm)) {
            $whereConditions[] = "(el.event_description LIKE :search OR u.user_username LIKE :search2 OR p.title LIKE :search3)";
            $searchPattern = '%' . $searchTerm . '%';
            $params[':search'] = $searchPattern;
            $params[':search2'] = $searchPattern;
            $params[':search3'] = $searchPattern;
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count total records with prepared statement
        $countSql = "
            SELECT COUNT(*) as total
            FROM event_log el
            LEFT JOIN user u ON el.user_id = u.user_id
            LEFT JOIN product p ON el.product_id = p.prod_id
            $whereClause
        ";
        
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calculate pagination values
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        
        // Get log entries with prepared statement
        $sql = "
            SELECT 
                el.event_id,
                el.event_type,
                el.event_description,
                el.event_timestamp,
                el.product_id,
                u.user_username,
                p.title as product_title
            FROM event_log el
            LEFT JOIN user u ON el.user_id = u.user_id
            LEFT JOIN product p ON el.product_id = p.prod_id
            $whereClause
            ORDER BY el.event_timestamp DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind all parameters with proper types
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $logEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'entries' => $logEntries,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalRecords,
                'itemsPerPage' => $limit,
                'firstRecord' => $offset + 1,
                'lastRecord' => min($offset + $limit, $totalRecords)
            ]
        ];
        
    } catch (PDOException $e) {
        // Log error without exposing sensitive information
        return [
            'entries' => [],
            'pagination' => [
                'currentPage' => 1,
                'totalPages' => 0,
                'totalItems' => 0,
                'itemsPerPage' => $limit,
                'firstRecord' => 0,
                'lastRecord' => 0
            ]
        ];
    }
}

// Handle AJAX requests with proper validation and security
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    // Validate and sanitize all input parameters
    $searchTerm = sanitizeInput($_GET['search'] ?? '', 'string', 255);
    $eventType = sanitizeInput($_GET['event_type'] ?? 'all', 'string', 50);
    $page = sanitizeInput($_GET['page'] ?? 1, 'int', null, ['min' => 1]) ?: 1;
    $limit = sanitizeInput($_GET['limit'] ?? 20, 'int', null, ['min' => 1, 'max' => 200]) ?: 20;
    
    // Get log data
    $result = getProductOperationsLog($searchTerm, $eventType, $page, $limit);
    
    // Set proper JSON headers and return response
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    echo json_encode([
        'success' => true,
        'entries' => $result['entries'],
        'pagination' => $result['pagination']
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<div class="tab-pane fade show active" id="product-log">
    
    <!-- Filter Form with CSRF Protection -->
    <form id="log-filter-form" class="mb-4">
        <?php echo getCSRFTokenField(); ?>
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="log-search-term" class="form-label">Sök</label>
                <input type="text" class="form-control" id="log-search-term" name="search" 
                    placeholder="Sök efter beskrivning, användare, produkttitel, författare..."
                    maxlength="255">
            </div>
            <div class="col-md-3">
                <label for="event-type-filter" class="form-label">Operationstyp</label>
                <select class="form-select" id="event-type-filter" name="event_type">
                    <option value="all">Alla operationer</option>
                    <optgroup label="Produktoperationer">
                        <option value="create">Skapa produkt</option>
                        <option value="update">Uppdatera produkt</option>
                        <option value="sell">Sälj produkt</option>
                        <option value="return">Återställ produkt</option>
                        <option value="delete">Ta bort produkt</option>
                    </optgroup>
                    <optgroup label="Författaroperationer">
                        <option value="create_author">Skapa författare</option>
                        <option value="update_author">Uppdatera författare</option>
                        <option value="delete_author">Ta bort författare</option>
                    </optgroup>
                    <optgroup label="Batchoperationer">
                        <option value="batch_update_status">Batch - Ändra status</option>
                        <option value="batch_update_price">Batch - Uppdatera pris</option>
                        <option value="batch_move_shelf">Batch - Flytta hylla</option>
                        <option value="batch_set_special_price">Batch - Rea</option>
                        <option value="batch_set_rare">Batch - Raritet</option>
                        <option value="batch_set_recommended">Batch - Rekommenderat</option>
                        <option value="batch_delete">Batch - Ta bort</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-2">
                <label for="log-page-size" class="form-label">Visa</label>
                <select class="form-select" id="log-page-size">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrera
                </button>
            </div>
        </div>
    </form>

    <!-- Log Table with Proper Structure -->
    <div class="table-responsive">
        <table class="table table-hover" id="product-log-table">
            <thead class="table-light">
                <tr>
                    <th scope="col">Tidpunkt</th>
                    <th scope="col">Användare</th>
                    <th scope="col">Operationstyp</th>
                    <th scope="col">Produkt</th>
                    <th scope="col">Beskrivning</th>
                </tr>
            </thead>
            <tbody id="log-table-body">
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Laddar...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Controls -->
    <div class="mt-3" id="log-pagination-controls">
        <div class="row align-items-center">
            <!-- Page Information Display -->
            <div class="col-md-6 mb-2 mb-md-0">
                <div id="log-pagination-info">
                    Visar <span id="log-showing-start">0</span> till 
                    <span id="log-showing-end">0</span> av 
                    <span id="log-total-items">0</span> poster
                </div>
            </div>
            
            <!-- Page Navigation Links -->
            <div class="col-md-6 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="log-pagination-links">
                    <!-- Pagination links will be inserted here by JavaScript -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Product Log Management JavaScript
 * 
 * Handles AJAX-based log loading, filtering, and pagination
 * with proper error handling and user feedback.
 */
$(document).ready(function() {
    // Initialize product log functionality
    initializeProductLog();
});

/**
 * Initialize product log functionality
 * 
 * Sets up event handlers and loads initial data
 */
function initializeProductLog() {
    // Load initial log data
    loadProductLog();
    
    // Filter form submission handler
    $('#log-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadProductLog();
    });
    
    // Event type filter change handler
    $('#event-type-filter').on('change', function() {
        loadProductLog();
    });
    
    // Page size change handler
    $('#log-page-size').on('change', function() {
        loadProductLog();
    });
}

/**
 * Load product operations log with AJAX
 * 
 * Fetches log data based on current filter settings and page number.
 * Handles loading states, error conditions, and response processing.
 * 
 * @param {number} page - Page number to load (default: 1)
 */
function loadProductLog(page = 1) {
    // Get current filter values with validation
    const searchTerm = $('#log-search-term').val().trim();
    const eventType = $('#event-type-filter').val();
    const limit = parseInt($('#log-page-size').val(), 10) || 20;
    
    // Validate page parameter
    page = parseInt(page, 10) || 1;
    if (page < 1) page = 1;
    
    // Show loading state
    displayLoadingState();
    
    // Make AJAX request with proper error handling
    $.ajax({
        url: BASE_URL + '/admin/productlog.php',
        type: 'GET',
        data: {
            ajax: 1,
            search: searchTerm,
            event_type: eventType,
            page: page,
            limit: limit
        },
        dataType: 'json',
        timeout: 30000, // 30 second timeout
        success: function(data) {
            if (data && data.success) {
                renderLogEntries(data.entries);
                updateLogPagination(data.pagination);
            } else {
                displayErrorMessage('Ett fel inträffade vid hämtning av data');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid hämtning av data';
            
            if (status === 'timeout') {
                errorMessage = 'Begäran tog för lång tid - försök igen';
            } else if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att visa denna data';
            } else if (xhr.status === 404) {
                errorMessage = 'Resursen kunde inte hittas';
            }
            
            displayErrorMessage(errorMessage);
        }
    });
}

/**
 * Display loading state in table
 */
function displayLoadingState() {
    $('#log-table-body').html(`
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
}

/**
 * Display error message in table
 * 
 * @param {string} message - Error message to display
 */
function displayErrorMessage(message) {
    $('#log-table-body').html(`
        <tr>
            <td colspan="5" class="text-center text-danger">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </td>
        </tr>
    `);
}

/**
 * Render log entries in the table
 * 
 * Safely renders log entries with proper HTML escaping and formatting.
 * Handles empty states and creates appropriate badges for event types.
 * 
 * @param {Array} entries - Array of log entry objects
 */
function renderLogEntries(entries) {
    if (!entries || entries.length === 0) {
        $('#log-table-body').html(`
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> Inga loggposter hittades
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    
    entries.forEach(entry => {
        // Generate event type badge with proper escaping
        const eventTypeBadge = createEventTypeBadge(entry.event_type);
        
        // Create product link with proper escaping and validation
        const productCell = createProductCell(entry.product_id, entry.product_title);
        
        // Format timestamp
        const formattedTime = formatDateTime(entry.event_timestamp);
        
        // Build table row with escaped content
        html += `
        <tr>
            <td>${formattedTime}</td>
            <td>${entry.user_username ? $('<div>').text(entry.user_username).html() : '<span class="text-muted">System</span>'}</td>
            <td>${eventTypeBadge}</td>
            <td>${productCell}</td>
            <td>${entry.event_description ? $('<div>').text(entry.event_description).html() : ''}</td>
        </tr>`;
    });
    
    $('#log-table-body').html(html);
}

/**
 * Create event type badge with appropriate styling
 * 
 * @param {string} eventType - Event type identifier
 * @returns {string} HTML badge element
 */
function createEventTypeBadge(eventType) {
    const eventTypeMap = {
        'create': '<span class="badge bg-success">Skapa</span>',
        'update': '<span class="badge bg-warning">Uppdatera</span>',
        'sell': '<span class="badge bg-danger">Sälj</span>',
        'return': '<span class="badge bg-info">Återställ</span>',
        'delete': '<span class="badge bg-dark">Ta bort</span>',
        'create_author': '<span class="badge bg-success"><i class="fas fa-user-plus"></i> Författare</span>',
        'update_author': '<span class="badge bg-warning"><i class="fas fa-user-edit"></i> Författare</span>',
        'delete_author': '<span class="badge bg-dark"><i class="fas fa-user-minus"></i> Författare</span>',
        'batch_update_status': '<span class="badge bg-primary"><i class="fas fa-tasks"></i> Batch Status</span>',
        'batch_update_price': '<span class="badge bg-primary"><i class="fas fa-tags"></i> Batch Pris</span>',
        'batch_move_shelf': '<span class="badge bg-primary"><i class="fas fa-arrows-alt"></i> Batch Hylla</span>',
        'batch_set_special_price': '<span class="badge bg-danger"><i class="fas fa-percentage"></i> Batch Rea</span>',
        'batch_set_rare': '<span class="badge bg-warning"><i class="fas fa-gem"></i> Batch Raritet</span>',
        'batch_set_recommended': '<span class="badge bg-info"><i class="fas fa-star"></i> Batch Rekommenderat</span>',
        'batch_delete': '<span class="badge bg-danger"><i class="fas fa-trash-alt"></i> Batch Ta bort</span>'
    };
    
    return eventTypeMap[eventType] || `<span class="badge bg-secondary">${eventType ? $('<div>').text(eventType).html() : ''}</span>`;
}

/**
 * Create product cell with proper link and escaping
 * 
 * @param {number|string} productId - Product ID
 * @param {string} productTitle - Product title
 * @returns {string} HTML content for product cell
 */
function createProductCell(productId, productTitle) {
    if (productId && productTitle) {
        const escapedTitle = $('<div>').text(productTitle).html();
        const validatedId = parseInt(productId, 10);
        
        if (validatedId > 0) {
            return `<a href="${BASE_URL}/admin/adminsingleproduct.php?id=${validatedId}" class="text-decoration-none">${escapedTitle}</a>`;
        }
        return escapedTitle;
    } else if (productTitle) {
        return $('<div>').text(productTitle).html();
    }
    return '<span class="text-muted">-</span>';
}

/**
 * Update pagination controls for log display
 * 
 * Generates pagination links and updates page information display
 * with proper event handling and accessibility features.
 * 
 * @param {Object} pagination - Pagination data object
 */
function updateLogPagination(pagination) {
    // Update page information display
    $('#log-showing-start').text(pagination.firstRecord || 0);
    $('#log-showing-end').text(pagination.lastRecord || 0);
    $('#log-total-items').text(pagination.totalItems || 0);
    
    // Generate pagination links if needed
    if (pagination.totalPages > 0) {
        const html = generatePaginationLinks(pagination);
        $('#log-pagination-links').html(html);
        
        // Attach click handlers to pagination links
        attachPaginationHandlers();
    } else {
        $('#log-pagination-links').html('');
    }
}

/**
 * Generate pagination links HTML
 * 
 * @param {Object} pagination - Pagination data
 * @returns {string} Pagination HTML
 */
function generatePaginationLinks(pagination) {
    let html = '';
    
    // Previous page link
    html += `
        <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link log-page-link" href="#" data-page="${pagination.currentPage - 1}" 
               ${pagination.currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span aria-hidden="true">&laquo;</span>
                <span class="visually-hidden">Föregående</span>
            </a>
        </li>
    `;
    
    // Calculate page range
    const startPage = Math.max(1, pagination.currentPage - 2);
    const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
    
    // First page and ellipsis
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link log-page-link" href="#" data-page="1">1</a></li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Page number links
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                <a class="page-link log-page-link" href="#" data-page="${i}"
                   ${i === pagination.currentPage ? 'aria-current="page"' : ''}>${i}</a>
            </li>
        `;
    }
    
    // Last page and ellipsis
    if (endPage < pagination.totalPages) {
        if (endPage < pagination.totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link log-page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a></li>`;
    }
    
    // Next page link
    html += `
        <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
            <a class="page-link log-page-link" href="#" data-page="${pagination.currentPage + 1}"
               ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span class="visually-hidden">Nästa</span>
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    return html;
}

/**
 * Attach click handlers to pagination links
 */
function attachPaginationHandlers() {
    $('.log-page-link').on('click', function(e) {
        e.preventDefault();
        
        // Prevent double-clicks and disabled links
        if ($(this).closest('.page-item').hasClass('disabled')) {
            return false;
        }
        
        const page = parseInt($(this).data('page'), 10);
        if (!isNaN(page) && page > 0) {
            loadProductLog(page);
        }
        
        return false;
    });
}

/**
 * Format datetime for Swedish locale display
 * 
 * @param {string} timestamp - ISO timestamp string
 * @returns {string} Formatted datetime string
 */
function formatDateTime(timestamp) {
    if (!timestamp) return '';
    
    try {
        const date = new Date(timestamp);
        
        // Validate date
        if (isNaN(date.getTime())) {
            return timestamp; // Return original if invalid
        }
        
        return date.toLocaleString('sv-SE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    } catch (error) {
        return timestamp; // Return original on error
    }
}
</script>