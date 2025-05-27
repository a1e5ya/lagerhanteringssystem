<?php
/**
 * Newsletter Subscribers Management for Admin
 * 
 * Contains:
 * - Newsletter subscribers list and management
 * - Filter and search functionality
 * - Subscriber status management
 * - Export functionality
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    1.0
 */

// Include initialization file
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has admin permissions
checkAuth(1); // Only Admin (1) role required for newsletter management

/**
 * Get newsletter subscribers with filtering
 * 
 * @param string $searchTerm Search term for filtering
 * @param string $status Status filter (active/inactive/all)
 * @param string $language Language preference filter
 * @param int $page Page number
 * @param int $limit Items per page
 * @return array Subscribers and pagination data
 */
function getNewsletterSubscribers($searchTerm = '', $status = 'all', $language = 'all', $page = 1, $limit = 20) {
    global $pdo;
    
    try {
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        // Status filter
        if ($status !== 'all') {
            if ($status === 'active') {
                $whereConditions[] = "subscriber_is_active = 1";
            } elseif ($status === 'inactive') {
                $whereConditions[] = "subscriber_is_active = 0";
            }
        }
        
        // Language filter
        if ($language !== 'all') {
            $whereConditions[] = "subscriber_language_pref = :language";
            $params[':language'] = $language;
        }
        
        // Search term filter
        if (!empty($searchTerm)) {
            $whereConditions[] = "(subscriber_email LIKE :search OR subscriber_name LIKE :search2)";
            $params[':search'] = "%$searchTerm%";
            $params[':search2'] = "%$searchTerm%";
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count total records
        $countSql = "SELECT COUNT(*) as total FROM newsletter_subscriber $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calculate pagination
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        
        // Get subscribers
        $sql = "
            SELECT 
                subscriber_id,
                subscriber_email,
                subscriber_name,
                subscribed_date,
                subscriber_is_active,
                subscriber_language_pref
            FROM newsletter_subscriber
            $whereClause
            ORDER BY subscribed_date DESC
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
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'subscribers' => $subscribers,
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
        error_log("Error getting newsletter subscribers: " . $e->getMessage());
        return [
            'subscribers' => [],
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

/**
 * Update subscriber status
 * 
 * @param int $subscriberId Subscriber ID
 * @param int $status New status (1=active, 0=inactive)
 * @return array Result array
 */
function updateSubscriberStatus($subscriberId, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE newsletter_subscriber SET subscriber_is_active = :status WHERE subscriber_id = :id");
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':id', $subscriberId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Log the action
        $currentUser = getSessionUser();
        $action = $status ? 'activated' : 'deactivated';
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (:user_id, 'update_subscriber', :description)
        ");
        $logStmt->execute([
            ':user_id' => $currentUser['user_id'],
            ':description' => "Newsletter subscriber $action (ID: $subscriberId)"
        ]);
        
        return [
            'success' => true,
            'message' => $status ? 'Prenumerant aktiverad' : 'Prenumerant inaktiverad'
        ];
        
    } catch (PDOException $e) {
        error_log("Error updating subscriber status: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte uppdatera prenumerantens status'
        ];
    }
}

/**
 * Delete subscriber
 * 
 * @param int $subscriberId Subscriber ID
 * @return array Result array
 */
function deleteSubscriber($subscriberId) {
    global $pdo;
    
    try {
        // Get subscriber info for logging
        $getStmt = $pdo->prepare("SELECT subscriber_email FROM newsletter_subscriber WHERE subscriber_id = :id");
        $getStmt->execute([':id' => $subscriberId]);
        $subscriber = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$subscriber) {
            return ['success' => false, 'message' => 'Prenumerant hittades inte'];
        }
        
        // Delete subscriber
        $stmt = $pdo->prepare("DELETE FROM newsletter_subscriber WHERE subscriber_id = :id");
        $stmt->execute([':id' => $subscriberId]);
        
        // Log the action
        $currentUser = getSessionUser();
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (:user_id, 'delete_subscriber', :description)
        ");
        $logStmt->execute([
            ':user_id' => $currentUser['user_id'],
            ':description' => "Newsletter subscriber deleted: {$subscriber['subscriber_email']}"
        ]);
        
        return [
            'success' => true,
            'message' => 'Prenumerant har tagits bort'
        ];
        
    } catch (PDOException $e) {
        error_log("Error deleting subscriber: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte ta bort prenumeranten'
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    // Handle statistics request
    if (isset($_GET['get_stats'])) {
        try {
            // Get total subscribers
            $totalStmt = $pdo->prepare("SELECT COUNT(*) as total FROM newsletter_subscriber");
            $totalStmt->execute();
            $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get active subscribers
            $activeStmt = $pdo->prepare("SELECT COUNT(*) as active FROM newsletter_subscriber WHERE subscriber_is_active = 1");
            $activeStmt->execute();
            $active = $activeStmt->fetch(PDO::FETCH_ASSOC)['active'];
            
            // Get inactive subscribers
            $inactive = $total - $active;
            
            // Get this month's subscribers
            $monthlyStmt = $pdo->prepare("
                SELECT COUNT(*) as monthly 
                FROM newsletter_subscriber 
                WHERE YEAR(subscribed_date) = YEAR(CURDATE()) 
                AND MONTH(subscribed_date) = MONTH(CURDATE())
            ");
            $monthlyStmt->execute();
            $monthly = $monthlyStmt->fetch(PDO::FETCH_ASSOC)['monthly'];
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total' => $total,
                    'active' => $active,
                    'inactive' => $inactive,
                    'monthly' => $monthly
                ]
            ]);
            exit;
            
        } catch (PDOException $e) {
            error_log("Error getting subscriber stats: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error loading statistics']);
            exit;
        }
    }
    
    // Handle regular subscriber list request
    $searchTerm = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? 'all';
    $language = $_GET['language'] ?? 'all';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    
    $result = getNewsletterSubscribers($searchTerm, $status, $language, $page, $limit);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'subscribers' => $result['subscribers'],
        'pagination' => $result['pagination']
    ]);
    exit;
}

// Handle export request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['export'])) {
    $searchTerm = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? 'all';
    $language = $_GET['language'] ?? 'all';
    
    // Get all matching subscribers (no pagination for export)
    $result = getNewsletterSubscribers($searchTerm, $status, $language, 1, 10000);
    $subscribers = $result['subscribers'];
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
    
    // Create CSV content
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers
    fputcsv($output, ['E-post', 'Namn', 'Prenumerationsdatum', 'Språk', 'Status'], ';');
    
    // Add data
    foreach ($subscribers as $subscriber) {
        fputcsv($output, [
            $subscriber['subscriber_email'],
            $subscriber['subscriber_name'] ?: '',
            $subscriber['subscribed_date'],
            $subscriber['subscriber_language_pref'] === 'fi' ? 'Finska' : 'Svenska',
            $subscriber['subscriber_is_active'] ? 'Aktiv' : 'Inaktiv'
        ], ';');
    }
    
    fclose($output);
    exit;
}

// Handle POST requests (status updates, deletions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $subscriberId = (int)($_POST['subscriber_id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);
        
        $result = updateSubscriberStatus($subscriberId, $status);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
        
    } elseif ($action === 'delete_subscriber') {
        $subscriberId = (int)($_POST['subscriber_id'] ?? 0);
        
        $result = deleteSubscriber($subscriberId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
?>

<div class="tab-pane fade show active" id="newsletter-subscribers">

    
    <!-- Filter Form -->
    <form id="subscriber-filter-form" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="subscriber-search-term" class="form-label">Sök</label>
                <input type="text" class="form-control" id="subscriber-search-term" name="search" 
                    placeholder="Sök efter e-post eller namn...">
            </div>
            <div class="col-md-2">
                <label for="status-filter" class="form-label">Status</label>
                <select class="form-select" id="status-filter" name="status">
                    <option value="all">Alla</option>
                    <option value="active">Aktiva</option>
                    <option value="inactive">Inaktiva</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="language-filter" class="form-label">Språk</label>
                <select class="form-select" id="language-filter" name="language">
                    <option value="all">Alla språk</option>
                    <option value="sv">Svenska</option>
                    <option value="fi">Finska</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="subscriber-page-size" class="form-label">Visa</label>
                <select class="form-select" id="subscriber-page-size">
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



    <!-- Subscribers Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="subscribers-table">
            <thead class="table-light">
                <tr>
                    <th>E-post</th>
                    <th>Namn</th>
                    <th>Prenumerationsdatum</th>
                    <th>Språk</th>
                    <th>Status</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody id="subscribers-table-body">
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Laddar...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination controls -->
    <div class="mt-3" id="subscriber-pagination-controls">
        <div class="row align-items-center">
            <!-- Page info -->
            <div class="col-md-6 mb-2 mb-md-0">
                <div id="subscriber-pagination-info">
                    Visar <span id="subscriber-showing-start">0</span> till 
                    <span id="subscriber-showing-end">0</span> av 
                    <span id="subscriber-total-items">0</span> prenumeranter
                </div>
            </div>
            
            <!-- Page navigation -->
            <div class="col-md-6 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="subscriber-pagination-links">
                    <!-- Pagination links will be inserted here by JS -->
                </ul>
            </div>
        </div>
    </div>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <button class="btn btn-success btn-lg mt-4" id="export-subscribers">
                <i class="fas fa-download"></i> Exportera
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Newsletter subscribers tab initialized');
    
    // Load initial data
    loadSubscribers();
    loadSubscriberStats();
    
    // Filter form submission
    $('#subscriber-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadSubscribers();
    });
    
    // Filter changes
    $('#status-filter, #language-filter').on('change', function() {
        loadSubscribers();
    });
    
    // Page size change
    $('#subscriber-page-size').on('change', function() {
        loadSubscribers();
    });
    
    // Export button
    $('#export-subscribers').on('click', function() {
        exportSubscribers();
    });
});

/**
 * Load newsletter subscribers
 */
function loadSubscribers(page = 1) {
    const searchTerm = $('#subscriber-search-term').val();
    const status = $('#status-filter').val();
    const language = $('#language-filter').val();
    const limit = $('#subscriber-page-size').val();
    
    console.log('Loading subscribers:', { searchTerm, status, language, page, limit });
    
    // Show loading
    $('#subscribers-table-body').html(`
        <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    // Make AJAX request
    $.ajax({
        url: BASE_URL + '/admin/subscribers.php',
        type: 'GET',
        data: {
            ajax: 1,
            search: searchTerm,
            status: status,
            language: language,
            page: page,
            limit: limit
        },
        dataType: 'json',
        success: function(data) {
            console.log('Subscribers data received:', data);
            
            if (data.success) {
                renderSubscribers(data.subscribers);
                updateSubscriberPagination(data.pagination);
                loadSubscriberStats(); // Refresh stats
            } else {
                $('#subscribers-table-body').html('<tr><td colspan="6" class="text-center text-danger">Ett fel inträffade</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            $('#subscribers-table-body').html('<tr><td colspan="6" class="text-center text-danger">Ett fel inträffade vid hämtning av data</td></tr>');
        }
    });
}

/**
 * Render subscribers in the table
 */
function renderSubscribers(subscribers) {
    if (!subscribers || subscribers.length === 0) {
        $('#subscribers-table-body').html('<tr><td colspan="6" class="text-center">Inga prenumeranter hittades</td></tr>');
        return;
    }
    
    let html = '';
    
    subscribers.forEach(subscriber => {
        // Format status
        const statusClass = subscriber.subscriber_is_active == 1 ? 'text-success' : 'text-danger';
        const statusText = subscriber.subscriber_is_active == 1 ? 'Aktiv' : 'Inaktiv';
        const statusBadge = subscriber.subscriber_is_active == 1 ? 
            '<span class="badge bg-success">Aktiv</span>' : 
            '<span class="badge bg-danger">Inaktiv</span>';
        
        // Format language
        const languageText = subscriber.subscriber_language_pref === 'fi' ? 'Finska' : 'Svenska';
        
        // Format date
        const subscribeDate = formatDateTime(subscriber.subscribed_date);
        
        // Action buttons
        const toggleAction = subscriber.subscriber_is_active == 1 ? 'deactivate' : 'activate';
        const toggleText = subscriber.subscriber_is_active == 1 ? 'Inaktivera' : 'Aktivera';
        const toggleClass = subscriber.subscriber_is_active == 1 ? 'btn-warning' : 'btn-success';
        const toggleIcon = subscriber.subscriber_is_active == 1 ? 'pause' : 'play';
        
        html += `
        <tr>
            <td>${subscriber.subscriber_email || ''}</td>
            <td>${subscriber.subscriber_name || '<span class="text-muted">-</span>'}</td>
            <td>${subscribeDate}</td>
            <td>${languageText}</td>
            <td>${statusBadge}</td>
            <td>
    <div class="d-flex justify-content-between">
        <button class="btn btn-outline-${toggleClass === 'btn-warning' ? 'warning' : 'success'} btn-sm toggle-status" 
                data-id="${subscriber.subscriber_id}" 
                data-action="${toggleAction}"
                title="${toggleText}">
            ${toggleText}
        </button>
        <button class="btn btn-outline-danger btn-sm delete-subscriber" 
                data-id="${subscriber.subscriber_id}"
                data-email="${subscriber.subscriber_email}"
                title="Ta bort">
            Ta bort
        </button>
    </div>
</td>
        </tr>`;
    });
    
    $('#subscribers-table-body').html(html);
    
    // Attach event handlers
    $('.toggle-status').on('click', function() {
        const subscriberId = $(this).data('id');
        const action = $(this).data('action');
        const newStatus = action === 'activate' ? 1 : 0;
        
        updateSubscriberStatus(subscriberId, newStatus);
    });
    
    $('.delete-subscriber').on('click', function() {
        const subscriberId = $(this).data('id');
        const email = $(this).data('email');
        
        if (confirm(`Är du säker på att du vill ta bort prenumeranten ${email}?`)) {
            deleteSubscriber(subscriberId);
        }
    });
}

/**
 * Update subscriber status
 */
function updateSubscriberStatus(subscriberId, status) {
    $.ajax({
        url: BASE_URL + '/admin/subscribers.php',
        type: 'POST',
        data: {
            action: 'update_status',
            subscriber_id: subscriberId,
            status: status
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                showMessage(data.message, 'success');
                loadSubscribers(); // Reload current page
            } else {
                showMessage(data.message, 'danger');
            }
        },
        error: function() {
            showMessage('Ett fel inträffade vid uppdatering av status', 'danger');
        }
    });
}

/**
 * Delete subscriber
 */
function deleteSubscriber(subscriberId) {
    $.ajax({
        url: BASE_URL + '/admin/subscribers.php',
        type: 'POST',
        data: {
            action: 'delete_subscriber',
            subscriber_id: subscriberId
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                showMessage(data.message, 'success');
                loadSubscribers(); // Reload current page
            } else {
                showMessage(data.message, 'danger');
            }
        },
        error: function() {
            showMessage('Ett fel inträffade vid borttagning av prenumerant', 'danger');
        }
    });
}

/**
 * Load subscriber statistics
 */
function loadSubscriberStats() {
    $.ajax({
        url: BASE_URL + '/admin/subscribers.php',
        type: 'GET',
        data: {
            ajax: 1,
            get_stats: 1
        },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.stats) {
                $('#total-subscribers').text(data.stats.total || 0);
                $('#active-subscribers').text(data.stats.active || 0);
                $('#inactive-subscribers').text(data.stats.inactive || 0);
                $('#monthly-subscribers').text(data.stats.monthly || 0);
            }
        },
        error: function() {
            console.error('Failed to load subscriber statistics');
        }
    });
}

/**
 * Export subscribers
 */
function exportSubscribers() {
    const searchTerm = $('#subscriber-search-term').val();
    const status = $('#status-filter').val();
    const language = $('#language-filter').val();
    
    // Create download link
    const params = new URLSearchParams({
        export: 1,
        search: searchTerm,
        status: status,
        language: language
    });
    
    window.open(`${BASE_URL}/admin/subscribers.php?${params.toString()}`, '_blank');
}

/**
 * Update pagination controls for subscribers
 */
function updateSubscriberPagination(pagination) {
    // Update page info
    $('#subscriber-showing-start').text(pagination.firstRecord || 0);
    $('#subscriber-showing-end').text(pagination.lastRecord || 0);
    $('#subscriber-total-items').text(pagination.totalItems || 0);
    
    // Generate pagination links
    if (pagination.totalPages > 0) {
        let html = '';
        
        // Previous page
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link subscriber-page-link" href="#" data-page="${pagination.currentPage - 1}" ${pagination.currentPage <= 1 ? 'tabindex="-1"' : ''}>
                    <span>&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        // First page
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link subscriber-page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                    <a class="page-link subscriber-page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page
        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link subscriber-page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a></li>`;
        }
        
        // Next page
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link subscriber-page-link" href="#" data-page="${pagination.currentPage + 1}" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1"' : ''}>
                    <span>&raquo;</span>
                </a>
            </li>
        `;
        
        $('#subscriber-pagination-links').html(html);
        
        // Attach click handlers
        $('.subscriber-page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page)) {
                loadSubscribers(page);
            }
        });
    } else {
        $('#subscriber-pagination-links').html('');
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
        minute: '2-digit'
    });
}

/**
 * Show message to user
 */
function showMessage(message, type = 'info') {
    console.log('Showing message:', { message, type });
    
    const messageContainer = $('#message-container');
    if (!messageContainer.length) return;
    
    // Clear previous messages
    messageContainer.html('');
    
    // Create message element
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Add to container and show
    messageContainer.html(alertHtml).show();
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        messageContainer.find('.alert').removeClass('show');
        setTimeout(function() {
            messageContainer.find('.alert').remove();
            
            // Hide container if empty
            if (messageContainer.children().length === 0) {
                messageContainer.hide();
            }
        }, 150);
    }, 5000);
}
</script>