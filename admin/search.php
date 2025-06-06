<?php
/**
 * Search Products for Admin Interface
 * 
 * Provides comprehensive product search functionality for the admin interface
 * including AJAX endpoints, filtering capabilities, status management, and
 * real-time product operations. Features secure parameter handling, proper
 * authentication, and comprehensive event logging.
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    3.7
 * @since      3.6
 */

// Include initialization file (replaces multiple require statements)
require_once dirname(__DIR__) . '/init.php';

// Check if user is authenticated and has appropriate permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Create formatter instance for data presentation
$formatter = new Formatter();

// Get categories for dropdown with proper error handling
$categories = getCategories();

// Check if this is an AJAX request to update product status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_status') {
    // Validate CSRF token for security
    if (!checkCSRFToken()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Ogiltig säkerhetstoken'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Process status change
    handleStatusChange();
    exit; // Stop execution after handling the status change
}
?>
    
<div class="tab-pane fade show active" id="search">
    <!-- Search Form with CSRF Protection -->
    <form id="admin-search-form" class="mb-4">
        <?php echo getCSRFTokenField(); ?>
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search-term" class="form-label">Sök</label>
                <input type="text" class="form-control" id="search-term" name="search" 
                    placeholder="Sök efter titel, författare, noter..." 
                    value="<?= isset($_GET['search']) ? safeEcho($_GET['search']) : '' ?>"
                    maxlength="255">
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
        
        <!-- Hidden inputs for sorting and pagination state management -->
        <input type="hidden" id="admin-sort-column" name="sort" value="<?= isset($_GET['sort']) ? safeEcho($_GET['sort']) : '' ?>">
        <input type="hidden" id="admin-sort-direction" name="order" value="<?= isset($_GET['order']) ? safeEcho($_GET['order']) : 'asc' ?>">
        <input type="hidden" id="admin-current-page" name="page" value="<?= isset($_GET['page']) ? safeEcho($_GET['page']) : '1' ?>">
        <input type="hidden" id="admin-page-limit" name="limit" value="<?= isset($_GET['limit']) ? safeEcho($_GET['limit']) : '20' ?>">
    </form>

    <!-- Products Table with Responsive Design -->
    <div class="table-responsive">
        <table class="table table-hover" id="inventory-table">
            <thead class="table-light">
                <tr>
                    <th scope="col" data-sort="title">Titel</th>
                    <th scope="col" data-sort="author_name">Författare</th>
                    <th scope="col" data-sort="category_name">Kategori</th>
                    <th scope="col" data-sort="shelf_name">Hylla</th>
                    <th scope="col" data-sort="price">Pris</th>
                    <th scope="col" data-sort="status">Status</th>
                    <th scope="col">Märkning</th>
                    <th scope="col">Åtgärder</th>
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
    
    <!-- Pagination Controls with Accessibility Features -->
    <div class="mt-3" id="pagination-controls">
        <div class="row align-items-center">
            <!-- Page Size Selector -->
            <div class="col-md-4 mb-2 mb-md-0">
                <div class="d-flex align-items-center">
                    <label for="page-size-selector" class="me-2">Visa</label>
                    <select class="form-select form-select-sm" id="page-size-selector" style="width: auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-2">objekt</span>
                </div>
            </div>
            
            <!-- Page Information Display -->
            <div class="col-md-4 text-center mb-2 mb-md-0">
                <div id="pagination-info">
                    Visar <span id="showing-start">0</span> till 
                    <span id="showing-end">0</span> av 
                    <span id="total-items">0</span> objekt
                </div>
            </div>
            
            <!-- Page Navigation Links -->
            <div class="col-md-4 d-flex justify-content-md-end">
                <ul class="pagination mb-0" id="pagination-links">
                    <!-- Pagination links will be inserted here by JavaScript -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX Admin Search with Enhanced Security -->
<script>
/**
 * Admin Search Interface JavaScript
 * 
 * Handles AJAX-powered product search, filtering, pagination,
 * and status management with proper error handling and security.
 */

/**
 * Get CSRF token for AJAX requests
 * 
 * @returns {string} CSRF token value
 */
function getCSRFToken() {
    const tokenField = document.querySelector('input[name="csrf_token"]');
    return tokenField ? tokenField.value : '';
}

/**
 * Attach search event handlers for form interactions
 * 
 * Sets up event listeners for search form submission and category filtering
 * with proper event handling and validation.
 */
function attachSearchEventHandlers() {
    const adminSearchForm = document.getElementById('admin-search-form');
    if (adminSearchForm) {
        // Handle form submit with validation
        $(adminSearchForm).on('submit', function(e) {
            e.preventDefault();
            
            // Get and validate search values
            const searchTerm = $('#search-term').val().trim();
            const category = $('#category-filter').val();
            
            // Validate search term length
            if (searchTerm.length > 255) {
                showMessage('Sökterm är för lång (max 255 tecken)', 'warning');
                return false;
            }
            
            // Load products with search parameters
            loadProducts(searchTerm, category);
        });
        
        // Handle dropdown change with debouncing
        $('#category-filter').on('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get search values
            const searchTerm = $('#search-term').val().trim();
            const category = $(this).val();
            
            // Load products with updated category
            loadProducts(searchTerm, category);
            
            return false;
        });
    }
}

/**
 * Attach action listeners for product operations
 * 
 * Sets up event handlers for quick sell/return buttons with
 * proper event delegation and confirmation dialogs.
 */
function attachActionListeners() {
    // Use event delegation for dynamically added elements
    $(document).off('click', '.quick-sell').on('click', '.quick-sell', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = parseInt($(this).data('id'), 10);
        if (isNaN(productId) || productId <= 0) {
            showMessage('Ogiltigt produkt-ID', 'danger');
            return false;
        }
        
        // Confirm action
        if (confirm('Är du säker på att du vill markera denna produkt som såld?')) {
            changeProductStatus(productId, 2); // 2 = Sold
        }
        
        return false;
    });
    
    // Quick return button click with confirmation
    $(document).off('click', '.quick-return').on('click', '.quick-return', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = parseInt($(this).data('id'), 10);
        if (isNaN(productId) || productId <= 0) {
            showMessage('Ogiltigt produkt-ID', 'danger');
            return false;
        }
        
        // Confirm action
        if (confirm('Är du säker på att du vill återställa denna produkt till tillgänglig?')) {
            changeProductStatus(productId, 1); // 1 = Available
        }
        
        return false;
    });
}

/**
 * Make table rows clickable for navigation
 * 
 * Enables row-level navigation while preventing conflicts with
 * interactive elements like buttons and links.
 */
function makeRowsClickable() {
    // Use event delegation for dynamically added rows
    $(document).off('click', '.clickable-row').on('click', '.clickable-row', function(e) {
        // Only navigate if not clicking on interactive elements
        if (!$(e.target).closest('button, a, input, select').length) {
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        }
    });
}

/**
 * Initialize admin search functionality
 * 
 * Sets up all event handlers and loads initial data when document is ready
 */
$(document).ready(function() {
    // Load all products initially
    loadAllProducts();

    // Set up event handlers
    attachSearchEventHandlers();
    attachActionListeners();
    makeRowsClickable();
    
    // Page size selector event with validation
    $('#page-size-selector').on('change', function() {
        const searchTerm = $('#search-term').val().trim();
        const category = $('#category-filter').val();
        const pageSize = parseInt($(this).val(), 10);
        
        // Validate page size
        if (isNaN(pageSize) || pageSize < 1 || pageSize > 200) {
            showMessage('Ogiltigt antal objekt per sida', 'warning');
            return;
        }
        
        loadProducts(searchTerm, category, 1, pageSize);
    });
});

/**
 * Load all products initially with no filters
 * 
 * Called on page load to display the default product listing
 */
function loadAllProducts() {
    loadProducts('', 'all', 1, 20);
}

/**
 * Load products with search parameters using secure AJAX
 * 
 * Fetches product data based on search criteria with proper validation,
 * error handling, and loading state management.
 * 
 * @param {string} searchTerm - Search term for filtering
 * @param {string} category - Category ID or 'all' for all categories
 * @param {number} page - Page number for pagination
 * @param {number} limit - Items per page limit
 */
function loadProducts(searchTerm = '', category = 'all', page = 1, limit = 20) {
    // Validate and sanitize parameters
    searchTerm = String(searchTerm || '').trim();
    category = String(category || 'all');
    page = parseInt(page, 10) || 1;
    limit = parseInt(limit, 10) || 20;
    
    // Validate parameter ranges
    if (page < 1) page = 1;
    if (limit < 1 || limit > 200) limit = 20;
    if (searchTerm.length > 255) {
        showMessage('Sökterm är för lång', 'warning');
        return;
    }
    
    // Show loading indicator
    displayLoadingState();
    
    // Update URL with search parameters (without causing page reload)
    updateURLWithParams(searchTerm, category, page, limit);
    
    // Update hidden form fields
    $('#admin-current-page').val(page);
    $('#admin-page-limit').val(limit);
    
    // Use jQuery AJAX with comprehensive error handling
    $.ajax({
        url: BASE_URL + '/admin/get_products.php',
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
        timeout: 30000, // 30 second timeout
        beforeSend: function() {
            // Disable form during request
            $('#admin-search-form button').prop('disabled', true);
        },
        complete: function() {
            // Re-enable form after request
            $('#admin-search-form button').prop('disabled', false);
        },
        success: function(data) {
            if (data && data.success) {
                // Update table with products
                if (data.html) {
                    $('#inventory-body').html(data.html);
                } else if (data.items && data.items.length > 0) {
                    renderProducts(data.items);
                } else {
                    displayNoResults();
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
                displayErrorMessage(data.message || 'Ett fel inträffade vid hämtning av data');
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
            } else if (xhr.status === 500) {
                errorMessage = 'Serverfel - kontakta administratören';
            }
            
            displayErrorMessage(errorMessage);
        }
    });
}

/**
 * Display loading state in table body
 */
function displayLoadingState() {
    $('#inventory-body').html(`
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
}

/**
 * Display no results message
 */
function displayNoResults() {
    $('#inventory-body').html(`
        <tr>
            <td colspan="8" class="text-center text-muted">
                <i class="fas fa-info-circle"></i> Inga produkter hittades
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
    $('#inventory-body').html(`
        <tr>
            <td colspan="8" class="text-center text-danger">
                <i class="fas fa-exclamation-triangle"></i> ${$('<div>').text(message).html()}
            </td>
        </tr>
    `);
}

/**
 * Update URL with search parameters
 * 
 * @param {string} searchTerm - Search term
 * @param {string} category - Category filter
 * @param {number} page - Current page
 * @param {number} limit - Items per page
 */
function updateURLWithParams(searchTerm, category, page, limit) {
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('category', category);
        url.searchParams.set('page', page);
        url.searchParams.set('limit', limit);
        url.searchParams.set('tab', 'search');
        window.history.pushState({}, '', url);
    } catch (e) {
        // Silently fail if URL manipulation is not supported
    }
}

/**
 * Render products in the table with proper escaping
 * 
 * Safely renders product data with HTML escaping and proper formatting.
 * Creates interactive elements for product management operations.
 * 
 * @param {Array} products - Array of product objects
 */
function renderProducts(products) {
    if (!Array.isArray(products) || products.length === 0) {
        displayNoResults();
        return;
    }
    
    let html = '';
    
    products.forEach(product => {
        // Validate product data
        if (!product || !product.prod_id) {
            return; // Skip invalid products
        }
        
        // Safely escape all text content
        const title = $('<div>').text(product.title || '').html();
        const authorName = $('<div>').text(product.author_name || '').html();
        const categoryName = $('<div>').text(product.category_name || '').html();
        const shelfName = $('<div>').text(product.shelf_name || '').html();
        const formattedPrice = $('<div>').text(product.formatted_price || '').html();
        const statusName = $('<div>').text(product.status_name || '').html();
        
        // Determine status class
        const statusClass = parseInt(product.status) === 1 ? 'text-success' : 'text-danger';
        
        // Validate product ID
        const productId = parseInt(product.prod_id, 10);
        if (isNaN(productId) || productId <= 0) {
            return; // Skip products with invalid IDs
        }
        
        // Create badges for special markings
        let badges = '';
        if (product.special_price == 1) {
            badges += '<span class="badge bg-danger me-1">Rea</span>';
        }
        if (product.rare == 1) {
            badges += '<span class="badge bg-warning text-dark me-1">Sällsynt</span>';
        }
        if (product.recommended == 1) {
            badges += '<span class="badge bg-info me-1">Rekommenderas</span>';
        }
        
        // Create action buttons based on status
        const actionButtons = createActionButtons(productId, parseInt(product.status));
        
        html += `
        <tr class="clickable-row" data-href="${BASE_URL}/admin/adminsingleproduct.php?id=${productId}">
            <td>${title}</td>
            <td>${authorName}</td>
            <td>${categoryName}</td>
            <td>${shelfName}</td>
            <td>${formattedPrice}</td>
            <td class="${statusClass}">${statusName}</td>
            <td>${badges}</td>
            <td>${actionButtons}</td>
        </tr>`;
    });
    
    $('#inventory-body').html(html);
}

/**
 * Create action buttons for product operations
 * 
 * @param {number} productId - Product ID
 * @param {number} status - Current product status
 * @returns {string} HTML for action buttons
 */
function createActionButtons(productId, status) {
    let buttons = '<div class="btn-group btn-group-sm" role="group">';
    
    if (status === 1) {
        // Available - show sell button
        buttons += `
            <button class="btn btn-outline-success quick-sell" data-id="${productId}" 
                    title="Markera som såld" aria-label="Markera som såld">
                <i class="fas fa-shopping-cart"></i>
            </button>`;
    } else {
        // Sold - show return button
        buttons += `
            <button class="btn btn-outline-warning quick-return" data-id="${productId}" 
                    title="Återställ till tillgänglig" aria-label="Återställ till tillgänglig">
                <i class="fas fa-undo"></i>
            </button>`;
    }
    
    // Edit button (always available)
    buttons += `
        <a href="${BASE_URL}/admin/adminsingleproduct.php?id=${productId}" 
           class="btn btn-outline-primary" title="Redigera" aria-label="Redigera produkt">
            <i class="fas fa-edit"></i>
        </a>`;
    
    buttons += '</div>';
    return buttons;
}

/**
 * Update pagination controls with enhanced accessibility
 * 
 * Generates pagination links with proper ARIA labels and event handling.
 * Updates page information display and manages navigation state.
 * 
 * @param {Object} pagination - Pagination data object
 * @param {string} searchTerm - Current search term
 * @param {string} category - Current category filter
 * @param {number} limit - Items per page
 */
function updatePagination(pagination, searchTerm, category, limit) {
    // Update page information display
    $('#showing-start').text(pagination.firstRecord || 0);
    $('#showing-end').text(pagination.lastRecord || 0);
    $('#total-items').text(pagination.totalItems || 0);
    
    // Update page size selector
    $('#page-size-selector').val(pagination.itemsPerPage || limit);
    
    // Generate pagination links if we have pages
    if (pagination.totalPages > 0) {
        const html = generatePaginationHTML(pagination);
        $('#pagination-links').html(html);
        
        // Attach event listeners to pagination links
        attachPaginationListeners(searchTerm, category, limit);
    } else {
        // No pages to display
        $('#pagination-links').html('');
    }
}

/**
 * Generate pagination HTML with accessibility features
 * 
 * @param {Object} pagination - Pagination data
 * @returns {string} Pagination HTML
 */
function generatePaginationHTML(pagination) {
    let html = '';
    
    // Previous page button
    html += `
        <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link pagination-link" href="#" data-page="${pagination.currentPage - 1}" 
               aria-label="Föregående sida" ${pagination.currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Calculate page range for display
    const startPage = Math.max(1, pagination.currentPage - 2);
    const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
    
    // First page link
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link pagination-link" href="#" data-page="1" aria-label="Sida 1">1</a>
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
    
    // Page number links
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                <a class="page-link pagination-link" href="#" data-page="${i}" 
                   aria-label="Sida ${i}" ${i === pagination.currentPage ? 'aria-current="page"' : ''}>${i}</a>
            </li>
        `;
    }
    
    // Last page link
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
                <a class="page-link pagination-link" href="#" data-page="${pagination.totalPages}" 
                   aria-label="Sida ${pagination.totalPages}">${pagination.totalPages}</a>
            </li>
        `;
    }
    
    // Next page button
    html += `
        <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
            <a class="page-link pagination-link" href="#" data-page="${pagination.currentPage + 1}" 
               aria-label="Nästa sida" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    return html;
}

/**
 * Attach event listeners to pagination links
 * 
 * @param {string} searchTerm - Current search term
 * @param {string} category - Current category
 * @param {number} limit - Items per page
 */
function attachPaginationListeners(searchTerm, category, limit) {
    $('#pagination-links .pagination-link').off('click').on('click', function(e) {
        e.preventDefault();
        
        // Check if link is disabled
        if ($(this).closest('.page-item').hasClass('disabled')) {
            return false;
        }
        
        const page = parseInt($(this).data('page'), 10);
        if (!isNaN(page) && page > 0) {
            loadProducts(searchTerm, category, page, limit);
        }
        
        return false;
    });
}

/**
 * Change product status via secure AJAX
 * 
 * Updates product status with proper validation, CSRF protection,
 * and comprehensive error handling. Provides user feedback and
 * refreshes the display upon successful completion.
 * 
 * @param {number} productId - Product ID to update
 * @param {number} newStatus - New status value (1=Available, 2=Sold)
 */
function changeProductStatus(productId, newStatus) {
    // Validate parameters
    if (isNaN(productId) || productId <= 0) {
        showMessage('Ogiltigt produkt-ID', 'danger');
        return;
    }
    
    if (newStatus !== 1 && newStatus !== 2) {
        showMessage('Ogiltig status', 'danger');
        return;
    }
    
    // Show progress message
    showMessage('Uppdaterar status...', 'info');
    
    // Use jQuery AJAX with CSRF protection
    $.ajax({
        url: BASE_URL + '/admin/search.php',
        type: 'POST',
        data: {
            action: 'change_status',
            product_id: productId,
            status: newStatus,
            csrf_token: getCSRFToken()
        },
        dataType: 'json',
        timeout: 15000, // 15 second timeout
        success: function(data) {
            if (data && data.success) {
                showMessage(data.message || 'Status uppdaterad', 'success');
                
                // Reload products with current parameters
                const searchTerm = $('#search-term').val().trim();
                const category = $('#category-filter').val();
                const currentPage = parseInt($('.page-item.active .page-link').text()) || 1;
                const limit = parseInt($('#page-size-selector').val()) || 20;
                
                loadProducts(searchTerm, category, currentPage, limit);
            } else {
                showMessage(data.message || 'Ett fel inträffade vid statusuppdatering', 'danger');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid statusuppdatering';
            
            if (status === 'timeout') {
                errorMessage = 'Begäran tog för lång tid - försök igen';
            } else if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet för denna åtgärd';
            } else if (xhr.status === 422) {
                errorMessage = 'Ogiltiga data skickades';
            }
            
            showMessage(errorMessage, 'danger');
        }
    });
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
 * Handle product status change via secure AJAX
 * 
 * Processes product status updates with comprehensive validation,
 * transaction management, and event logging. Ensures data integrity
 * and provides detailed error handling.
 * 
 * @return void Outputs JSON response and exits
 */
function handleStatusChange() {
    global $pdo;
    
    // Set JSON response headers
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        // Validate required parameters
        if (!isset($_POST['product_id']) || !isset($_POST['status'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Saknade obligatoriska parametrar'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Sanitize and validate input parameters
        $productId = sanitizeInput($_POST['product_id'], 'int', null, ['min' => 1]);
        $newStatus = sanitizeInput($_POST['status'], 'int', null, ['min' => 1, 'max' => 2]);
        
        if (!$productId || !$newStatus) {
            echo json_encode([
                'success' => false,
                'message' => 'Ogiltiga parametervärden'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Validate status value (should be 1 or 2 only)
        if ($newStatus !== 1 && $newStatus !== 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Ogiltigt statusvärde'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Check if product exists and get current status
        $stmt = $pdo->prepare("SELECT prod_id, status, title FROM product WHERE prod_id = :product_id");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'Produkten kunde inte hittas'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Check if status actually needs to change
        if ((int)$product['status'] === $newStatus) {
            echo json_encode([
                'success' => true,
                'message' => 'Produktstatus är redan uppdaterad'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // Start transaction for data integrity
        $pdo->beginTransaction();
        
        // Update product status with prepared statement
        $updateStmt = $pdo->prepare("UPDATE product SET status = :status WHERE prod_id = :product_id");
        $updateStmt->bindValue(':status', $newStatus, PDO::PARAM_INT);
        $updateStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $updateResult = $updateStmt->execute();
        
        if (!$updateResult) {
            throw new PDOException('Misslyckades med att uppdatera produktstatus');
        }
        
        // Log the event for audit trail
        $eventType = ($newStatus === 1) ? 'return' : 'sell';
        $eventDescription = ($newStatus === 1) ? 
            'Produkt återställd till tillgänglig: ' : 
            'Produkt markerad som såld: ';
        
        $eventDescription .= sanitizeInput($product['title'], 'string', 255);
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description, product_id, event_timestamp) 
            VALUES (:user_id, :event_type, :event_description, :product_id, NOW())
        ");
        $logStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $logStmt->bindValue(':event_type', $eventType, PDO::PARAM_STR);
        $logStmt->bindValue(':event_description', $eventDescription, PDO::PARAM_STR);
        $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $logResult = $logStmt->execute();
        
        if (!$logResult) {
            throw new PDOException('Misslyckades med att logga händelsen');
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => ($newStatus === 1) ? 
                'Produkt återställd till tillgänglig.' : 
                'Produkt markerad som såld.'
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (PDOException $e) {
        // Rollback transaction on database error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log error for debugging (without exposing to user)
        error_log("Database error in handleStatusChange: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Ett databasfel inträffade. Försök igen senare.'
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // Handle any other exceptions
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log error for debugging
        error_log("General error in handleStatusChange: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Ett oväntat fel inträffade. Försök igen senare.'
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Get all categories from the database with language support
 * 
 * Retrieves category list with proper language selection based on
 * user session preferences. Includes comprehensive error handling
 * and fallback mechanisms.
 * 
 * @return array List of categories with ID and localized names
 */
function getCategories() {
    global $pdo;
    
    // Check if PDO is properly initialized
    if (!isset($pdo) || !$pdo) {
        // Try to establish the database connection if it's not available
        try {
            require_once BASE_PATH . '/config/config.php';
        } catch (Exception $e) {
            error_log("Failed to initialize database connection: " . $e->getMessage());
            return [];
        }
    }
    
    try {
        // Get language from session with validation and fallback
        $language = isset($_SESSION['language']) ? sanitizeInput($_SESSION['language'], 'string', 2) : 'sv';
        
        // Validate language code and set fallback
        $allowedLanguages = ['sv', 'fi'];
        if (!in_array($language, $allowedLanguages)) {
            $language = 'sv'; // Default fallback
        }
        
        // Determine which field to use based on language
        $nameField = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
        
        // Validate field name to prevent SQL injection
        if (!in_array($nameField, ['category_sv_name', 'category_fi_name'])) {
            $nameField = 'category_sv_name'; // Safe fallback
        }
        
        // Prepare SQL query to get categories with appropriate language
        $sql = "SELECT category_id, {$nameField} as category_name 
                FROM category 
                WHERE {$nameField} IS NOT NULL AND {$nameField} != ''
                ORDER BY {$nameField} ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Validate and sanitize results
        $validCategories = [];
        foreach ($categories as $category) {
            if (isset($category['category_id']) && isset($category['category_name'])) {
                $validCategories[] = [
                    'category_id' => (int)$category['category_id'],
                    'category_name' => sanitizeInput($category['category_name'], 'string', 255)
                ];
            }
        }
        
        return $validCategories;
        
    } catch (PDOException $e) {
        // Log database errors without exposing sensitive information
        error_log("Database error in getCategories: " . $e->getMessage());
        return [];
        
    } catch (Exception $e) {
        // Handle any other exceptions
        error_log("General error in getCategories: " . $e->getMessage());
        return [];
    }
}
?>