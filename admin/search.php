<?php
/**
 * Search Products for Admin
 * 
 * Contains:
 * - Product search functionality for admin interface
 * - AJAX endpoints for search results
 * - Product status management
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    3.6
 */

define('BASE_PATH', dirname(__DIR__));

// Include necessary files if not already included
if (!function_exists('safeEcho')) {
    require_once BASE_PATH . '/includes/functions.php';
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/Paginator.php';
require_once BASE_PATH . '/includes/Formatter.php';

// Create formatter instance
$formatter = new Formatter();

// Get categories for dropdown
$categories = getCategories();

// Check if this is an AJAX request to update product status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_status') {
    // Process status change
    handleStatusChange();
    exit; // Stop execution after handling the status change
}
?>
    
<div class="tab-pane fade show active" id="search">
    <!-- Search Form -->
    <form id="admin-search-form" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search-term" class="form-label">Sök</label>
                <input type="text" class="form-control" id="search-term" name="search" 
                    placeholder="Sök efter titel, författare, noter..." 
                    value="<?= isset($_GET['search']) ? safeEcho($_GET['search']) : '' ?>">
            </div>
            <div class="col-md-4">
                <label for="category-filter" class="form-label">Kategori</label>
                <select class="form-select" id="category-filter" name="category">
                    <option value="all">Alla kategorier</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= safeEcho($category['category_id']) ?>" 
                            <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                        <?= safeEcho($category['category_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Sök
                </button>
            </div>
        </div>
        
        <!-- Hidden inputs for sorting and pagination -->
        <input type="hidden" id="admin-sort-column" name="sort" value="<?= isset($_GET['sort']) ? safeEcho($_GET['sort']) : '' ?>">
        <input type="hidden" id="admin-sort-direction" name="order" value="<?= isset($_GET['order']) ? safeEcho($_GET['order']) : 'asc' ?>">
        <input type="hidden" id="admin-current-page" name="page" value="<?= isset($_GET['page']) ? safeEcho($_GET['page']) : '1' ?>">
        <input type="hidden" id="admin-page-limit" name="limit" value="<?= isset($_GET['limit']) ? safeEcho($_GET['limit']) : '20' ?>">
    </form>

    <!-- Explicit message container -->
    <div id="message-container" style="display:none;"></div>

    <!-- Direct Implementation of Products Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="inventory-table">
            <thead class="table-light">
                <tr>
                    <th data-sort="title">Titel</th>
                    <th data-sort="author_name">Författare</th>
                    <th data-sort="category_name">Kategori</th>
                    <th data-sort="shelf_name">Hylla</th>
                    <th data-sort="price">Pris</th>
                    <th data-sort="status">Status</th>
                    <th>Märkning</th>
                    <th>Åtgärder</th>
                </tr>
            </thead>
            <tbody id="inventory-body">
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Laddar...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination controls -->
    <div class="mt-3" id="pagination-controls">
        <div class="row align-items-center">
            <!-- Page size selector -->
            <div class="col-md-4 mb-2 mb-md-0">
                <div class="d-flex align-items-center">
                    <label class="me-2">Visa</label>
                    <select class="form-select form-select-sm" id="page-size-selector" style="width: auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-2">objekt</span>
                </div>
            </div>
            
            <!-- Page info -->
            <div class="col-md-4 text-center mb-2 mb-md-0">
                <div id="pagination-info">
                    Visar <span id="showing-start">0</span> till 
                    <span id="showing-end">0</span> av 
                    <span id="total-items">0</span> objekt
                </div>
            </div>
            
            <!-- Page navigation -->
            <div class="col-md-4 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="pagination-links">
                    <!-- Pagination links will be inserted here by JS -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX admin search -->
<script>
// Functions expected by admin.js - adding for compatibility
function attachSearchEventHandlers() {
    console.log('Compatibility function called: attachSearchEventHandlers');
    const adminSearchForm = document.getElementById('admin-search-form');
    if (adminSearchForm) {
        // Handle form submit
        $(adminSearchForm).on('submit', function(e) {
            e.preventDefault();
            
            // Get search values
            const searchTerm = $('#search-term').val();
            const category = $('#category-filter').val();
            
            // Load products with search parameters
            loadProducts(searchTerm, category);
        });
        
        // Handle dropdown change
        $('#category-filter').on('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get search values
            const searchTerm = $('#search-term').val();
            const category = $(this).val();
            
            // Load products with updated category
            loadProducts(searchTerm, category);
            
            return false;
        });
    }
}

function attachActionListeners() {
    console.log('Compatibility function called: attachActionListeners');
    // Quick sell button click
    $('.quick-sell').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data('id');
        changeProductStatus(productId, 2); // 2 = Sold
    });
    
    // Quick return button click
    $('.quick-return').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data('id');
        changeProductStatus(productId, 1); // 1 = Available
    });
}

function makeRowsClickable() {
    console.log('Compatibility function called: makeRowsClickable');
    $('.clickable-row').on('click', function(e) {
        // Only navigate if not clicking on a button or link
        if (!$(e.target).closest('button, a, input, select').length) {
            window.location.href = $(this).data('href');
        }
    });
}

// Make sure document is ready before running this script
$(document).ready(function() {
    console.log('DOM loaded, initializing admin search');
    
    // Load all products initially
    loadAllProducts();

    // Set up event handlers
    attachSearchEventHandlers();
    
    // Page size selector event
    $('#page-size-selector').on('change', function() {
        const searchTerm = $('#search-term').val();
        const category = $('#category-filter').val();
        const pageSize = $(this).val();
        
        loadProducts(searchTerm, category, 1, pageSize);
    });
});

/**
 * Load all products initially with no filters
 */
function loadAllProducts() {
    console.log('Loading all products');
    loadProducts('', 'all', 1, 20);
}

/**
 * Load products with search parameters using jQuery AJAX
 * 
 * @param {string} searchTerm - Search term
 * @param {string} category - Category ID or 'all'
 * @param {number} page - Page number
 * @param {number} limit - Items per page
 */
function loadProducts(searchTerm = '', category = 'all', page = 1, limit = 20) {
    console.log('Loading products with parameters:', { searchTerm, category, page, limit });
    
    // Show loading indicator
    $('#inventory-body').html(`
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    // Update URL with search parameters (without causing page reload)
    const url = new URL(window.location.href);
    url.searchParams.set('search', searchTerm);
    url.searchParams.set('category', category);
    url.searchParams.set('page', page);
    url.searchParams.set('limit', limit);
    url.searchParams.set('tab', 'search');
    window.history.pushState({}, '', url);
    
    // Update hidden form fields
    $('#admin-current-page').val(page);
    $('#admin-page-limit').val(limit);
    
    // Use jQuery AJAX to make the request
    $.ajax({
        url: 'admin/get_products.php', // Use server-side proxy script
        type: 'GET',
        data: {
            search: searchTerm,
            category: category !== 'all' ? category : '',
            page: page,
            limit: limit,
            show_all_statuses: true,
            status: 'all'
        },
        dataType: 'json',
        success: function(data) {
            console.log('API response data:', data);
            
            if (data.success) {
                // Update table with products
                if (data.html) {
                    $('#inventory-body').html(data.html);
                } else if (data.items && data.items.length > 0) {
                    renderProducts(data.items);
                } else {
                    $('#inventory-body').html('<tr><td colspan="8" class="text-center">Inga produkter hittades</td></tr>');
                }
                
                // Update pagination
                if (data.pagination) {
                    updatePagination(data.pagination, searchTerm, category, limit);
                }
                
                // Initialize actionable elements
                attachActionListeners();
                makeRowsClickable();
            } else {
                // Show error message
                $('#inventory-body').html(`<tr><td colspan="8" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            $('#inventory-body').html('<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade vid hämtning av data</td></tr>');
        }
    });
}

/**
 * Render products in the table
 * 
 * @param {Array} products - Products array
 */
function renderProducts(products) {
    let html = '';
    
    products.forEach(product => {
        const statusClass = parseInt(product.status) === 1 ? 'text-success' : 'text-danger';
        
        html += `
        <tr class="clickable-row" data-href="admin/adminsingleproduct.php?id=${product.prod_id}">
            <td>${product.title || ''}</td>
            <td>${product.author_name || ''}</td>
            <td>${product.category_name || ''}</td>
            <td>${product.shelf_name || ''}</td>
            <td>${product.formatted_price || ''}</td>
            <td class="${statusClass}">${product.status_name || ''}</td>
            <td>
                ${product.special_price == 1 ? '<span class="badge bg-danger">Rea</span>' : ''}
                ${product.rare == 1 ? '<span class="badge bg-warning text-dark">Sällsynt</span>' : ''}
                ${product.recommended == 1 ? '<span class="badge bg-info">Rekommenderas</span>' : ''}
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${parseInt(product.status) === 1 ? 
                        `<button class="btn btn-outline-success quick-sell" data-id="${product.prod_id}" title="Markera som såld">
                            <i class="fas fa-shopping-cart"></i>
                        </button>` : 
                        `<button class="btn btn-outline-warning quick-return" data-id="${product.prod_id}" title="Återställ till tillgänglig">
                            <i class="fas fa-undo"></i>
                        </button>`
                    }
                    <a href="admin/adminsingleproduct.php?id=${product.prod_id}" class="btn btn-outline-primary" title="Redigera">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>`;
    });
    
    $('#inventory-body').html(html);
}

/**
 * Update pagination controls
 * 
 * @param {Object} pagination - Pagination data
 * @param {string} searchTerm - Current search term
 * @param {string} category - Current category
 * @param {number} limit - Items per page
 */
function updatePagination(pagination, searchTerm, category, limit) {
    // Update page info
    $('#showing-start').text(pagination.firstRecord || 0);
    $('#showing-end').text(pagination.lastRecord || 0);
    $('#total-items').text(pagination.totalItems || 0);
    
    // Update page size selector
    $('#page-size-selector').val(pagination.itemsPerPage || limit);
    
    // Generate pagination links if we have pages
    if (pagination.totalPages > 0) {
        let html = '';
        
        // Previous page button
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage - 1}" aria-label="Previous" ${pagination.currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        // First page
        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
            `;
            
            if (startPage > 2) {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page
        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
            
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.totalPages}">${pagination.totalPages}</a>
                </li>
            `;
        }
        
        // Next page
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage + 1}" aria-label="Next" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        $('#pagination-links').html(html);
        
        // Attach event listeners to pagination links
        $('#pagination-links .page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page)) {
                loadProducts(searchTerm, category, page, limit);
            }
        });
    } else {
        // No pages to display
        $('#pagination-links').html('');
    }
}

/**
 * Change product status via AJAX
 * 
 * @param {number} productId Product ID
 * @param {number} newStatus New status (1=Available, 2=Sold)
 */
function changeProductStatus(productId, newStatus) {
    console.log('Changing product status:', { productId, newStatus });
    
    // Show message
    showMessage('Uppdaterar status...', 'info');
    
    // Use jQuery AJAX
    $.ajax({
        url: 'admin/search.php',
        type: 'POST',
        data: {
            action: 'change_status',
            product_id: productId,
            status: newStatus
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                showMessage(data.message || 'Status uppdaterad', 'success');
                
                // Reload products with current parameters
                const searchTerm = $('#search-term').val();
                const category = $('#category-filter').val();
                const currentPage = parseInt($('.page-item.active .page-link').text()) || 1;
                const limit = $('#page-size-selector').val();
                
                loadProducts(searchTerm, category, currentPage, limit);
            } else {
                showMessage(data.message || 'Ett fel inträffade', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        }
    });
}

/**
 * Display message to user
 * 
 * @param {string} message Message text
 * @param {string} type Message type (success, danger, warning, info)
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

// Make functions globally available for compatibility with admin.js
window.attachSearchEventHandlers = attachSearchEventHandlers;
window.attachActionListeners = attachActionListeners;
window.makeRowsClickable = makeRowsClickable;
window.changeProductStatus = changeProductStatus;
window.loadProducts = loadProducts;
</script>

<?php
/**
 * Handle product status change via AJAX
 * 
 * @return void
 */
function handleStatusChange() {
    global $pdo;
    
    // Check required parameters
    if (!isset($_POST['product_id']) || !isset($_POST['status'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters'
        ]);
        return;
    }
    
    $productId = (int)$_POST['product_id'];
    $newStatus = (int)$_POST['status'];
    
    // Validate status value (should be 1 or 2)
    if ($newStatus !== 1 && $newStatus !== 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid status value'
        ]);
        return;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Update product status
        $stmt = $pdo->prepare("UPDATE product SET status = :status WHERE prod_id = :product_id");
        $stmt->bindParam(':status', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Log the event
        $eventType = ($newStatus === 1) ? 'return' : 'sell';
        $eventDescription = ($newStatus === 1) ? 
            'Produkt återställd till tillgänglig: ' : 
            'Produkt markerad som såld: ';
        
        // Get product title
        $stmtTitle = $pdo->prepare("SELECT title FROM product WHERE prod_id = :product_id");
        $stmtTitle->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmtTitle->execute();
        $title = $stmtTitle->fetchColumn();
        
        $eventDescription .= $title;
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $stmtLog = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description, product_id) VALUES (:user_id, :event_type, :event_description, :product_id)");
        $stmtLog->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtLog->bindParam(':event_type', $eventType, PDO::PARAM_STR);
        $stmtLog->bindParam(':event_description', $eventDescription, PDO::PARAM_STR);
        $stmtLog->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmtLog->execute();
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => ($newStatus === 1) ? 'Produkt återställd till tillgänglig.' : 'Produkt markerad som såld.'
        ]);
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Databasfel: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get all categories from the database
 * 
 * @return array List of categories
 */
function getCategories() {
    global $pdo;
    
    // Check if PDO is properly initialized
    if (!isset($pdo) || !$pdo) {
        // Try to establish the database connection if it's not available
        require_once BASE_PATH . '/config/config.php';
    }
    
    try {
        // Get language from session or set default
        $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
        
        // Determine which field to use based on language
        $nameField = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
        
        // Prepare SQL query to get categories with appropriate language
        $stmt = $pdo->prepare("SELECT category_id, {$nameField} as category_name FROM category ORDER BY {$nameField} ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting categories: " . $e->getMessage());
        return [];
    }
}
?>