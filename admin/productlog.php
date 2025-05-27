<?php
/**
 * Product Operations Log for Admin
 * 
 * Contains:
 * - Product operations activity log
 * - Filter and search functionality
 * - User activity tracking for products
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    1.0
 */

// Include initialization file
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has admin or editor permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

/**
 * Get product operations log with filtering
 * 
 * @param string $searchTerm Search term for filtering
 * @param string $eventType Event type filter
 * @param int $page Page number
 * @param int $limit Items per page
 * @return array Log entries and pagination data
 */
function getProductOperationsLog($searchTerm = '', $eventType = 'all', $page = 1, $limit = 20) {
    global $pdo;
    
    try {
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        // Filter by event types related to products, authors, and batch operations
        $productEventTypes = [
            'create', 'update', 'sell', 'return', 'delete',
            'create_author', 'update_author', 'delete_author',
            'batch_update_status', 'batch_update_price', 'batch_move_shelf',
            'batch_set_special_price', 'batch_set_rare', 'batch_set_recommended',
            'batch_delete'
        ];
        $placeholders = ':event_type_' . implode(', :event_type_', range(0, count($productEventTypes) - 1));
        $whereConditions[] = "el.event_type IN ($placeholders)";
        
        foreach ($productEventTypes as $index => $type) {
            $params[':event_type_' . $index] = $type;
        }
        
        // Additional event type filter
        if ($eventType !== 'all') {
            $whereConditions[] = "el.event_type = :selected_event_type";
            $params[':selected_event_type'] = $eventType;
        }
        
        // Search term filter
        if (!empty($searchTerm)) {
            $whereConditions[] = "(el.event_description LIKE :search OR u.user_username LIKE :search2 OR p.title LIKE :search3)";
            $params[':search'] = "%$searchTerm%";
            $params[':search2'] = "%$searchTerm%";
            $params[':search3'] = "%$searchTerm%";
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count total records
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
        
        // Calculate pagination
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        
        // Get log entries
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
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
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
        error_log("Error getting product operations log: " . $e->getMessage());
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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $searchTerm = $_GET['search'] ?? '';
    $eventType = $_GET['event_type'] ?? 'all';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    
    $result = getProductOperationsLog($searchTerm, $eventType, $page, $limit);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'entries' => $result['entries'],
        'pagination' => $result['pagination']
    ]);
    exit;
}
?>

<div class="tab-pane fade show active" id="product-log">

    
    <!-- Filter Form -->
    <form id="log-filter-form" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="log-search-term" class="form-label">Sök</label>
                <input type="text" class="form-control" id="log-search-term" name="search" 
                    placeholder="Sök efter beskrivning, användare, produkttitel, författare...">
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

    <!-- Log Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="product-log-table">
            <thead class="table-light">
                <tr>
                    <th>Tidpunkt</th>
                    <th>Användare</th>
                    <th>Operationstyp</th>
                    <th>Produkt</th>
                    <th>Beskrivning</th>
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
    
    <!-- Pagination controls -->
    <div class="mt-3" id="log-pagination-controls">
        <div class="row align-items-center">
            <!-- Page info -->
            <div class="col-md-6 mb-2 mb-md-0">
                <div id="log-pagination-info">
                    Visar <span id="log-showing-start">0</span> till 
                    <span id="log-showing-end">0</span> av 
                    <span id="log-total-items">0</span> poster
                </div>
            </div>
            
            <!-- Page navigation -->
            <div class="col-md-6 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="log-pagination-links">
                    <!-- Pagination links will be inserted here by JS -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Product log tab initialized');
    
    // Load initial log data
    loadProductLog();
    
    // Filter form submission
    $('#log-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadProductLog();
    });
    
    // Event type filter change
    $('#event-type-filter').on('change', function() {
        loadProductLog();
    });
    
    // Page size change
    $('#log-page-size').on('change', function() {
        loadProductLog();
    });
});

/**
 * Load product operations log
 */
function loadProductLog(page = 1) {
    const searchTerm = $('#log-search-term').val();
    const eventType = $('#event-type-filter').val();
    const limit = $('#log-page-size').val();
    
    console.log('Loading product log:', { searchTerm, eventType, page, limit });
    
    // Show loading
    $('#log-table-body').html(`
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    // Make AJAX request
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
        success: function(data) {
            console.log('Log data received:', data);
            
            if (data.success) {
                renderLogEntries(data.entries);
                updateLogPagination(data.pagination);
            } else {
                $('#log-table-body').html('<tr><td colspan="5" class="text-center text-danger">Ett fel inträffade</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            $('#log-table-body').html('<tr><td colspan="5" class="text-center text-danger">Ett fel inträffade vid hämtning av data</td></tr>');
        }
    });
}

/**
 * Render log entries in the table
 */
function renderLogEntries(entries) {
    if (!entries || entries.length === 0) {
        $('#log-table-body').html('<tr><td colspan="5" class="text-center">Inga loggposter hittades</td></tr>');
        return;
    }
    
    let html = '';
    
    entries.forEach(entry => {
        // Format event type badge
        let eventTypeBadge = '';
        switch (entry.event_type) {
            case 'create':
                eventTypeBadge = '<span class="badge bg-success">Skapa</span>';
                break;
            case 'update':
                eventTypeBadge = '<span class="badge bg-warning">Uppdatera</span>';
                break;
            case 'sell':
                eventTypeBadge = '<span class="badge bg-danger">Sälj</span>';
                break;
            case 'return':
                eventTypeBadge = '<span class="badge bg-info">Återställ</span>';
                break;
            case 'delete':
                eventTypeBadge = '<span class="badge bg-dark">Ta bort</span>';
                break;
            case 'create_author':
                eventTypeBadge = '<span class="badge bg-success"><i class="fas fa-user-plus"></i> Författare</span>';
                break;
            case 'update_author':
                eventTypeBadge = '<span class="badge bg-warning"><i class="fas fa-user-edit"></i> Författare</span>';
                break;
            case 'delete_author':
                eventTypeBadge = '<span class="badge bg-dark"><i class="fas fa-user-minus"></i> Författare</span>';
                break;
            case 'batch_update_status':
                eventTypeBadge = '<span class="badge bg-primary"><i class="fas fa-tasks"></i> Batch Status</span>';
                break;
            case 'batch_update_price':
                eventTypeBadge = '<span class="badge bg-primary"><i class="fas fa-tags"></i> Batch Pris</span>';
                break;
            case 'batch_move_shelf':
                eventTypeBadge = '<span class="badge bg-primary"><i class="fas fa-arrows-alt"></i> Batch Hylla</span>';
                break;
            case 'batch_set_special_price':
                eventTypeBadge = '<span class="badge bg-danger"><i class="fas fa-percentage"></i> Batch Rea</span>';
                break;
            case 'batch_set_rare':
                eventTypeBadge = '<span class="badge bg-warning"><i class="fas fa-gem"></i> Batch Raritet</span>';
                break;
            case 'batch_set_recommended':
                eventTypeBadge = '<span class="badge bg-info"><i class="fas fa-star"></i> Batch Rekommenderat</span>';
                break;
            case 'batch_delete':
                eventTypeBadge = '<span class="badge bg-danger"><i class="fas fa-trash-alt"></i> Batch Ta bort</span>';
                break;
            default:
                eventTypeBadge = '<span class="badge bg-secondary">' + (entry.event_type || '') + '</span>';
        }
        
        // Product link
        let productCell = '';
        if (entry.product_id && entry.product_title) {
            productCell = `<a href="${BASE_URL}/admin/adminsingleproduct.php?id=${entry.product_id}" class="text-decoration-none">${entry.product_title}</a>`;
        } else if (entry.product_title) {
            productCell = entry.product_title;
        } else {
            productCell = '<span class="text-muted">-</span>';
        }
        
        html += `
        <tr>
            <td>${formatDateTime(entry.event_timestamp)}</td>
            <td>${entry.user_username || '<span class="text-muted">System</span>'}</td>
            <td>${eventTypeBadge}</td>
            <td>${productCell}</td>
            <td>${entry.event_description || ''}</td>
        </tr>`;
    });
    
    $('#log-table-body').html(html);
}

/**
 * Update pagination controls for log
 */
function updateLogPagination(pagination) {
    // Update page info
    $('#log-showing-start').text(pagination.firstRecord || 0);
    $('#log-showing-end').text(pagination.lastRecord || 0);
    $('#log-total-items').text(pagination.totalItems || 0);
    
    // Generate pagination links
    if (pagination.totalPages > 0) {
        let html = '';
        
        // Previous page
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link log-page-link" href="#" data-page="${pagination.currentPage - 1}" ${pagination.currentPage <= 1 ? 'tabindex="-1"' : ''}>
                    <span>&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        // First page
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link log-page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                    <a class="page-link log-page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page
        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link log-page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a></li>`;
        }
        
        // Next page
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link log-page-link" href="#" data-page="${pagination.currentPage + 1}" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1"' : ''}>
                    <span>&raquo;</span>
                </a>
            </li>
        `;
        
        $('#log-pagination-links').html(html);
        
        // Attach click handlers
        $('.log-page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page)) {
                loadProductLog(page);
            }
        });
    } else {
        $('#log-pagination-links').html('');
    }
}

/**
 * Format datetime for display
 */
function formatDateTime(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp);
    return date.toLocaleString('sv-SE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}
</script>