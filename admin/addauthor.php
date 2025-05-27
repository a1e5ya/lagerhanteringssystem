<?php
/**
 * Add/Manage Authors for Admin
 * 
 * Contains:
 * - Author creation and management
 * - Search and filter functionality
 * - Author editing and deletion
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    2.0
 */

// Include initialization file
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has admin or editor permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

/**
 * Get authors with filtering and pagination
 * 
 * @param string $searchTerm Search term for filtering
 * @param string $sortBy Sort field (name/id/date)
 * @param string $sortOrder Sort order (asc/desc)
 * @param int $page Page number
 * @param int $limit Items per page
 * @return array Authors and pagination data
 */
function getAuthors($searchTerm = '', $sortBy = 'name', $sortOrder = 'asc', $page = 1, $limit = 20) {
    global $pdo;
    
    try {
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        // Search term filter
        if (!empty($searchTerm)) {
            $whereConditions[] = "author_name LIKE :search";
            $params[':search'] = "%$searchTerm%";
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Determine sort column
        $sortColumn = 'author_name'; // default
        switch ($sortBy) {
            case 'id':
                $sortColumn = 'author_id';
                break;
            case 'name':
                $sortColumn = 'author_name';
                break;
        }
        
        // Validate sort order
        $sortOrder = ($sortOrder === 'desc') ? 'DESC' : 'ASC';
        
        // Count total records
        $countSql = "SELECT COUNT(*) as total FROM author $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calculate pagination
        $totalPages = ceil($totalRecords / $limit);
        $offset = ($page - 1) * $limit;
        
        // Get authors
        $sql = "
            SELECT 
                author_id,
                author_name
            FROM author
            $whereClause
            ORDER BY $sortColumn $sortOrder
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
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'authors' => $authors,
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
        error_log("Error getting authors: " . $e->getMessage());
        return [
            'authors' => [],
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
 * Create new author
 * 
 * @param string $authorName Author name
 * @return array Result array
 */
function createAuthor($authorName) {
    global $pdo;
    
    $authorName = trim($authorName);
    
    if (empty($authorName)) {
        return [
            'success' => false,
            'message' => 'Vänligen fyll i namnet.'
        ];
    }
    
    try {
        // Check if author already exists
        $checkStmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = :name");
        $checkStmt->execute([':name' => $authorName]);
        
        if ($checkStmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'Författaren finns redan i databasen.'
            ];
        }
        
        // Insert new author
        $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (:name)");
        $stmt->execute([':name' => $authorName]);
        
        // Log the action
        $currentUser = getSessionUser();
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (:user_id, 'create_author', :description)
        ");
        $logStmt->execute([
            ':user_id' => $currentUser['user_id'],
            ':description' => "Author created: $authorName"
        ]);
        
        return [
            'success' => true,
            'message' => 'Författare tillagd i databasen!'
        ];
        
    } catch (PDOException $e) {
        error_log("Error creating author: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte lägga till författaren.'
        ];
    }
}

/**
 * Update author
 * 
 * @param int $authorId Author ID
 * @param string $authorName New author name
 * @return array Result array
 */
function updateAuthor($authorId, $authorName) {
    global $pdo;
    
    $authorName = trim($authorName);
    
    if (empty($authorName)) {
        return [
            'success' => false,
            'message' => 'Vänligen fyll i namnet.'
        ];
    }
    
    try {
        // Get original name for logging
        $getStmt = $pdo->prepare("SELECT author_name FROM author WHERE author_id = :id");
        $getStmt->execute([':id' => $authorId]);
        $originalName = $getStmt->fetchColumn();
        
        if (!$originalName) {
            return [
                'success' => false,
                'message' => 'Författaren hittades inte.'
            ];
        }
        
        // Check if new name already exists (excluding current author)
        $checkStmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = :name AND author_id != :id");
        $checkStmt->execute([':name' => $authorName, ':id' => $authorId]);
        
        if ($checkStmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'En författare med detta namn finns redan.'
            ];
        }
        
        // Update author
        $stmt = $pdo->prepare("UPDATE author SET author_name = :name WHERE author_id = :id");
        $stmt->execute([':name' => $authorName, ':id' => $authorId]);
        
        // Log the action
        $currentUser = getSessionUser();
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (:user_id, 'update_author', :description)
        ");
        $logStmt->execute([
            ':user_id' => $currentUser['user_id'],
            ':description' => "Author updated: '$originalName' to '$authorName'"
        ]);
        
        return [
            'success' => true,
            'message' => 'Författaren har uppdaterats.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error updating author: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte uppdatera författaren.'
        ];
    }
}

/**
 * Delete author
 * 
 * @param int $authorId Author ID
 * @return array Result array
 */
function deleteAuthor($authorId) {
    global $pdo;
    
    try {
        // Get author name for logging
        $getStmt = $pdo->prepare("SELECT author_name FROM author WHERE author_id = :id");
        $getStmt->execute([':id' => $authorId]);
        $authorName = $getStmt->fetchColumn();
        
        if (!$authorName) {
            return [
                'success' => false,
                'message' => 'Författaren hittades inte.'
            ];
        }
        
        // Check if author is used in any products
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM product_author WHERE author_id = :id");
        $checkStmt->execute([':id' => $authorId]);
        $productCount = $checkStmt->fetchColumn();
        
        if ($productCount > 0) {
            return [
                'success' => false,
                'message' => "Kan inte ta bort författaren eftersom den används i $productCount produkt(er)."
            ];
        }
        
        // Delete author
        $stmt = $pdo->prepare("DELETE FROM author WHERE author_id = :id");
        $stmt->execute([':id' => $authorId]);
        
        // Log the action
        $currentUser = getSessionUser();
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (:user_id, 'delete_author', :description)
        ");
        $logStmt->execute([
            ':user_id' => $currentUser['user_id'],
            ':description' => "Author deleted: $authorName"
        ]);
        
        return [
            'success' => true,
            'message' => 'Författaren har tagits bort.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error deleting author: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Databasfel: Kunde inte ta bort författaren.'
        ];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $searchTerm = $_GET['search'] ?? '';
    $sortBy = $_GET['sort_by'] ?? 'name';
    $sortOrder = $_GET['sort_order'] ?? 'asc';
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    
    $result = getAuthors($searchTerm, $sortBy, $sortOrder, $page, $limit);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'authors' => $result['authors'],
        'pagination' => $result['pagination']
    ]);
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_author') {
        $authorName = $_POST['author_name'] ?? '';
        $result = createAuthor($authorName);
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
        
    } elseif ($action === 'update_author') {
        $authorId = (int)($_POST['author_id'] ?? 0);
        $authorName = $_POST['author_name'] ?? '';
        $result = updateAuthor($authorId, $authorName);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
        
    } elseif ($action === 'delete_author') {
        $authorId = (int)($_POST['author_id'] ?? 0);
        $result = deleteAuthor($authorId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
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
                        <label for="author_name" class="form-label">Författarens namn</label>
                        <input type="text" class="form-control" id="author_name" name="author_name" 
                               placeholder="Ange författarens fullständiga namn..." required>
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
                    placeholder="Sök efter författarens namn...">
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
                <button type="submit" class="btn btn-outline-primary w-100">
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
                    <!-- Pagination links will be inserted here by JS -->
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-author-id">
                <div class="mb-3">
                    <label for="edit-author-name" class="form-label">Författarens namn</label>
                    <input type="text" class="form-control" id="edit-author-name" required>
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
$(document).ready(function() {
    console.log('Add Author tab initialized');
    
    // Load initial data
    loadAuthors();
    
    // Add author form submission
    $('#add-author-form').on('submit', function(e) {
        e.preventDefault();
        
        const authorName = $('#author_name').val().trim();
        if (!authorName) {
            showMessage('Vänligen fyll i författarens namn.', 'warning');
            return;
        }
        
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
            error: function() {
                showMessage('Ett fel inträffade vid tillägg av författare', 'danger');
            }
        });
    });
    
    // Filter form submission
    $('#author-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadAuthors();
    });
    
    // Filter changes
    $('#sort-by, #sort-order').on('change', function() {
        loadAuthors();
    });
    
    // Edit author modal save
    $('#save-author-edit').on('click', function() {
        const authorId = $('#edit-author-id').val();
        const authorName = $('#edit-author-name').val().trim();
        
        if (!authorName) {
            showMessage('Vänligen fyll i författarens namn.', 'warning');
            return;
        }
        
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
            error: function() {
                showMessage('Ett fel inträffade vid uppdatering av författare', 'danger');
            }
        });
    });
});

/**
 * Load authors
 */
function loadAuthors(page = 1) {
    const searchTerm = $('#author-search-term').val();
    const sortBy = $('#sort-by').val();
    const sortOrder = $('#sort-order').val();
    const limit = 20; // Fixed limit for now
    
    console.log('Loading authors:', { searchTerm, sortBy, sortOrder, page, limit });
    
    // Show loading
    $('#authors-table-body').html(`
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    // Make AJAX request
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
        success: function(data) {
            console.log('Authors data received:', data);
            
            if (data.success) {
                renderAuthors(data.authors);
                updateAuthorPagination(data.pagination);
            } else {
                $('#authors-table-body').html('<tr><td colspan="3" class="text-center text-danger">Ett fel inträffade</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            $('#authors-table-body').html('<tr><td colspan="3" class="text-center text-danger">Ett fel inträffade vid hämtning av data</td></tr>');
        }
    });
}

/**
 * Render authors in the table
 */
function renderAuthors(authors) {
    if (!authors || authors.length === 0) {
        $('#authors-table-body').html('<tr><td colspan="3" class="text-center">Inga författare hittades</td></tr>');
        return;
    }
    
    let html = '';
    
    authors.forEach(author => {
        html += `
        <tr>
            <td>${author.author_id}</td>
            <td>${author.author_name}</td>
<td>
    <div class="d-flex justify-content-between">
        <button class="btn btn-outline-primary btn-sm edit-author" 
                data-id="${author.author_id}"
                data-name="${author.author_name}"
                title="Redigera">
            Redigera
        </button>
        <button class="btn btn-outline-danger btn-sm delete-author" 
                data-id="${author.author_id}"
                data-name="${author.author_name}"
                title="Ta bort">
            Ta bort
        </button>
    </div>
</td>
        </tr>`;
    });
    
    $('#authors-table-body').html(html);
    
    // Attach event handlers
    $('.edit-author').on('click', function() {
        const authorId = $(this).data('id');
        const authorName = $(this).data('name');
        
        $('#edit-author-id').val(authorId);
        $('#edit-author-name').val(authorName);
        $('#editAuthorModal').modal('show');
    });
    
    $('.delete-author').on('click', function() {
        const authorId = $(this).data('id');
        const authorName = $(this).data('name');
        
        if (confirm(`Är du säker på att du vill ta bort författaren "${authorName}"?`)) {
            deleteAuthor(authorId);
        }
    });
}

/**
 * Delete author
 */
function deleteAuthor(authorId) {
    $.ajax({
        url: BASE_URL + '/admin/addauthor.php',
        type: 'POST',
        data: {
            action: 'delete_author',
            author_id: authorId
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                showMessage(data.message, 'success');
                loadAuthors(); // Reload current page
            } else {
                showMessage(data.message, 'danger');
            }
        },
        error: function() {
            showMessage('Ett fel inträffade vid borttagning av författare', 'danger');
        }
    });
}

/**
 * Update pagination controls for authors
 */
function updateAuthorPagination(pagination) {
    // Update page info
    $('#author-showing-start').text(pagination.firstRecord || 0);
    $('#author-showing-end').text(pagination.lastRecord || 0);
    $('#author-total-items').text(pagination.totalItems || 0);
    
    // Generate pagination links
    if (pagination.totalPages > 0) {
        let html = '';
        
        // Previous page
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link author-page-link" href="#" data-page="${pagination.currentPage - 1}" ${pagination.currentPage <= 1 ? 'tabindex="-1"' : ''}>
                    <span>&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        // First page
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link author-page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                    <a class="page-link author-page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page
        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link author-page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a></li>`;
        }
        
        // Next page
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link author-page-link" href="#" data-page="${pagination.currentPage + 1}" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1"' : ''}>
                    <span>&raquo;</span>
                </a>
            </li>
        `;
        
        $('#author-pagination-links').html(html);
        
        // Attach click handlers
        $('.author-page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page)) {
                loadAuthors(page);
            }
        });
    } else {
        $('#author-pagination-links').html('');
    }
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