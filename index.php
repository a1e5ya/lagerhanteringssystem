<?php
/**
 * Home Page
 * 
 * Contains:
 * - Feature items display
 * - Search functionality
 * - Language switching
 * 
 * @package    KarisAntikvariat
 * @subpackage Frontend
 * @author     Axxell
 * @version    3.0
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/ui.php';
require_once 'includes/Formatter.php';

// Clean URL for default view
if (empty($_GET['search']) && 
    (empty($_GET['category']) || $_GET['category'] === 'all') &&
    isset($_GET['page']) && 
    isset($_GET['limit'])) {
    // Redirect to clean URL
    header('Location: index.php');
    exit;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Load language strings using the existing function from ui.php
$lang_strings = loadLanguageStrings($language);

// Get categories for search dropdown
try {
    $categories = getCategories();
} catch (Exception $e) {
    error_log('Error fetching categories: ' . $e->getMessage());
    $categories = [];
}

// Create formatter instance
$formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');

/**
 * Get featured products for the homepage
 * 
 * @param PDO $pdo Database connection
 * @param int $limit Number of featured products to retrieve
 * @return array Featured products
 */
function getFeaturedProducts(PDO $pdo, int $limit = 4): array 
{
    try {
        // Get recommended or special price products
        $sql = "SELECT 
                    p.prod_id, 
                    p.title, 
                    p.price, 
                    p.special_price,
                    p.recommended,
                    p.image,
                    a.author_name,
                    c.category_sv_name AS category_name
                FROM product p
                LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN author a ON pa.author_id = a.author_id
                JOIN category c ON p.category_id = c.category_id
                WHERE p.status = 1 AND (p.special_price = 1 OR p.recommended = 1)
                GROUP BY p.prod_id
                ORDER BY p.special_price DESC, RAND()
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log('Error fetching featured products: ' . $e->getMessage());
        return [];
    }
}

// Get special products for the featured section
$specialProducts = getFeaturedProducts($pdo, 4);

// Page title
$pageTitle = "Karis Antikvariat";

// Include header
include 'templates/header.php';
?>

<!-- Hero Banner with Full Width Image -->
<div class="hero-container position-relative">
    <img src="assets/images/hero.webp" alt="Karis Antikvariat" class="hero-image w-100">
    <div class="container">
        <div class="hero-content position-absolute">
            <div class="hero-text-container p-5 rounded text-center">
                <h1><?php echo $lang_strings['welcome'] ?? 'Välkommen till Karis Antikvariat'; ?></h1>
                <p class="lead"><?php echo $lang_strings['subtitle'] ?? 'Din källa för nordisk litteratur, musik och samlarobjekt'; ?></p>
                <a href="#browse" class="btn btn-primary btn-lg mt-3 border-light"><?php echo $lang_strings['browse_button'] ?? 'Bläddra i vårt sortiment'; ?></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="container my-4">
    <!-- Browse Section -->
    <section id="browse" class="my-5">
        <h2 class="mb-4"><?php echo $lang_strings['browse_heading'] ?? 'Bläddra & Sök'; ?></h2>
        
        <!-- Search Form -->
        <div class="search-bar mb-4">
            <form method="get" action="" id="search-form">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <input type="text" class="form-control" id="public-search" name="search" 
                            placeholder="<?php echo $lang_strings['search_placeholder'] ?? 'Sök'; ?>" 
                            value="<?= isset($_GET['search']) ? safeEcho($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <select class="form-select" id="public-category" name="category">
                            <option value="all"><?php echo $lang_strings['all_categories'] ?? 'Alla kategorier'; ?></option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= safeEcho($category['category_id']) ?>" 
                            <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                                <?= safeEcho($category['category_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" id="public-search-btn">
                            <?php echo $lang_strings['search_button'] ?? 'Sök'; ?>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="sort" id="public-sort-column" value="<?= safeEcho($_GET['sort'] ?? '') ?>">
                <input type="hidden" name="order" id="public-sort-direction" value="<?= safeEcho($_GET['order'] ?? 'asc') ?>">
            </form>
        </div>
        
        <!-- Table with Pagination -->
        <div>
            <div class="table-responsive">
                <table class="table table-hover table-paginated-table" id="public-inventory-table">
                    <thead class="table-light">
                        <tr>
                            <th data-sort="title"><?php echo $lang_strings['title'] ?? 'Titel'; ?></th>
                            <th data-sort="author_name"><?php echo $lang_strings['author_artist'] ?? 'Författare/Artist'; ?></th>
                            <th data-sort="category_name"><?php echo $lang_strings['category'] ?? 'Kategori'; ?></th>
                            <th data-sort="genre_names"><?php echo $lang_strings['genre'] ?? 'Genre'; ?></th>
                            <th data-sort="condition_name"><?php echo $lang_strings['condition'] ?? 'Skick'; ?></th>
                            <th data-sort="price"><?php echo $lang_strings['price'] ?? 'Pris'; ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="table-paginated-content" id="public-inventory-body">
                        <tr>
                            <td colspan="7" class="text-center">
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
                <label class="me-2"><?php echo $lang_strings['show'] ?? 'Visa'; ?></label>
                <select class="form-select form-select-sm" id="page-size-selector" style="width: auto;">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ms-2"><?php echo $lang_strings['items'] ?? 'objekt'; ?></span>
            </div>
        </div>
        
        <!-- Page info -->
        <div class="col-md-4 text-center mb-2 mb-md-0">
            <div id="pagination-info">
                <?php echo $lang_strings['showing'] ?? 'Visar'; ?> <span id="showing-start">0</span> 
                <?php echo $lang_strings['to'] ?? 'till'; ?> 
                <span id="showing-end">0</span> 
                <?php echo $lang_strings['of'] ?? 'av'; ?> 
                <span id="total-items">0</span> 
                <?php echo $lang_strings['items'] ?? 'objekt'; ?>
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
    </section>

    <!-- Special Products Section -->
    <section class="my-5">
        <h2 class="mb-4"><?php echo $lang_strings['on_sale'] ?? 'På rea / Rekommenderas'; ?></h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="featured-items">
            <?php foreach ($specialProducts as $product): ?>
            <div class="col">
                <div class="card h-100">
                    <?php
                    // Image handling
                    $productImagePath = !empty($product->image) 
                        ? '/prog23/lagerhanteringssystem/' . str_replace('../', '', $product->image)
                        : 'assets/images/src-book.webp';
                    ?>
                    <img src="<?php echo safeEcho($productImagePath); ?>" 
                         class="card-img-top" 
                         style="height: 180px; object-fit: cover;" 
                         alt="<?php echo safeEcho($product->title); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                        <p class="card-text text-muted"><?php echo safeEcho($product->author_name); ?></p>
                        <p class="text-success fw-bold">
                            <?php echo $formatter->formatPrice($product->price); ?>
                            <?php if ($product->special_price): ?>
                                <span class="badge bg-danger ms-2">Rea</span>
                            <?php endif; ?>
                            <?php if ($product->recommended): ?>
                                <span class="badge bg-info ms-2">Rekommenderas</span>
                            <?php endif; ?>
                        </p>
                        <a href="singleproduct.php?id=<?php echo safeEcho($product->prod_id); ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script src="assets/js/pagination.js"></script> -->
<script src="assets/js/main.js"></script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("Initializing public search...");
    
    // Make table rows clickable
    makeRowsClickable();
    
// Handle search form submission
const searchForm = document.getElementById('search-form');
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const searchInput = document.getElementById('public-search');
        const categorySelect = document.getElementById('public-category');
        
        const searchTerm = searchInput ? searchInput.value.trim() : '';
        const category = categorySelect ? categorySelect.value : 'all';
        const limit = document.getElementById('page-size-selector').value || 10;
        
        // If search is empty and category is all, load random samples
        if (searchTerm === '' && (category === 'all' || category === '')) {
            console.log('Empty search and category - loading random samples');
            loadProducts('', 'all', 1, limit, '', 'asc', true); // The true parameter loads random samples
        } else {
            loadProducts(searchTerm, category, 1, limit);
        }
    });
}
    
    // Handle category filter change
    const categorySelect = document.getElementById('public-category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }
    
    // Handle page size change
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector) {
        pageSizeSelector.addEventListener('change', function() {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }
    
    // Handle table header sorting
    const sortHeaders = document.querySelectorAll('th[data-sort]');
    sortHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortColumn = this.dataset.sort;
            
            // Get current sort direction or default to asc
            const currentSortColumn = document.getElementById('public-sort-column').value;
            let currentSortDirection = document.getElementById('public-sort-direction').value;
            
            // Toggle sort direction if clicking the same column
            let newSortDirection = 'asc';
            if (sortColumn === currentSortColumn) {
                newSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            }
            
            // Update hidden sort inputs
            document.getElementById('public-sort-column').value = sortColumn;
            document.getElementById('public-sort-direction').value = newSortDirection;
            
            // Update visual indicators of sort
            sortHeaders.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            this.classList.add(`sort-${newSortDirection}`);
            
            // Get current search parameters
            const searchInput = document.getElementById('public-search');
            const categorySelect = document.getElementById('public-category');
            
            const searchTerm = searchInput ? searchInput.value : '';
            const category = categorySelect ? categorySelect.value : 'all';
            const limit = pageSizeSelector ? pageSizeSelector.value : 10;
            
            // Always use normal search for sorting
            loadProducts(searchTerm, category, 1, limit, sortColumn, newSortDirection);
        });
    });
    
// Load initial products
const urlParams = new URLSearchParams(window.location.search);
const searchTerm = urlParams.get('search') || '';
const category = urlParams.get('category') || 'all';
const page = parseInt(urlParams.get('page')) || 1;
const limit = parseInt(urlParams.get('limit')) || 10;
const sort = urlParams.get('sort') || '';
const order = urlParams.get('order') || 'asc';

if (searchTerm || category !== 'all' || page > 1) {
    loadProducts(searchTerm, category, page, limit, sort, order);
} else {
    // Load random samples on initial page load
    loadProducts('', 'all', 1, 10, '', 'asc', true);
}
});


/**
 * Load products via AJAX
 * 
 * @param {string} searchTerm - Search term
 * @param {string} category - Category ID or 'all'
 * @param {number} page - Page number
 * @param {number} limit - Items per page
 * @param {string} sort - Sort column
 * @param {string} order - Sort direction
 * @param {boolean} randomSamples - Whether to load random samples
 */
function loadProducts(searchTerm = '', category = 'all', page = 1, limit = 10, sort = '', order = 'asc', randomSamples = false) {
    console.log('Loading products with parameters:', { searchTerm, category, page, limit, sort, order, randomSamples });
    
    // Show loading indicator
    const tableBody = document.getElementById('public-inventory-body');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Laddar...</span>
                    </div>
                </td>
            </tr>
        `;
    }
    
    // Update URL without reloading (for bookmark/history purposes)
    // Only update URL for explicit searches, not for random samples
    if (!randomSamples) {
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('category', category);
        url.searchParams.set('page', page);
        url.searchParams.set('limit', limit);
        if (sort) {
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);
        } else {
            url.searchParams.delete('sort');
            url.searchParams.delete('order');
        }
        window.history.pushState({}, '', url);
    }
    
    // Set request parameters
    const requestParams = {
        search: searchTerm,
        category: category !== 'all' ? category : '',
        page: page,
        limit: limit,
        sort: sort,
        order: order
    };
    
    // Explicitly set random_samples parameter if requested
    if (randomSamples) {
        requestParams.random_samples = 'true';
    }
    
    // Make AJAX request to the api endpoint
    fetch('api/get_public_products.php?' + new URLSearchParams(requestParams))
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('API response data:', data);
        
        if (data.success) {
            // Update table with products
            if (data.html && tableBody) {
                tableBody.innerHTML = data.html;
makeRowsClickable();

            }
            
            // Update pagination info
            if (data.pagination) {
                updatePaginationInfo(data.pagination);
            }
            
            // Scroll to browse section if this was a search (not random samples)
            if ((searchTerm || category !== 'all') && !randomSamples) {
                document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
            }
        } else {
            // Show error message
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`;
            }
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        if (tableBody) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Ett fel inträffade vid hämtning av data</td></tr>';
        }
    });
}



/**
 * Update pagination information
 * 
 * @param {object} pagination - Pagination data
 */
function updatePaginationInfo(pagination) {
    // Update showing range
    const showingStart = document.getElementById('showing-start');
    if (showingStart) {
        showingStart.textContent = pagination.firstRecord;
    }
    
    const showingEnd = document.getElementById('showing-end');
    if (showingEnd) {
        showingEnd.textContent = pagination.lastRecord;
    }
    
    const totalItems = document.getElementById('total-items');
    if (totalItems) {
        totalItems.textContent = pagination.totalItems;
    }
    
    // Update pagination links
    const paginationLinks = document.getElementById('pagination-links');
    if (paginationLinks) {
        let html = '';
        
        // Previous page button
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage - 1}" aria-label="Previous" ${pagination.currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        // Calculate range of page numbers to display
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        // First page link if not in range
        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
            `;
            
            // Add ellipsis if needed
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
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page link if not in range
        if (endPage < pagination.totalPages) {
            // Add ellipsis if needed
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
        
        // Next page button
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage + 1}" aria-label="Next" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        paginationLinks.innerHTML = html;
        
        // Attach event listeners to pagination links
        const links = paginationLinks.querySelectorAll('.page-link');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const page = parseInt(this.dataset.page, 10);
                if (!isNaN(page)) {
                    // Get current search parameters
                    const searchInput = document.getElementById('public-search');
                    const categorySelect = document.getElementById('public-category');
                    const pageSizeSelector = document.getElementById('page-size-selector');
                    
                    const searchTerm = searchInput ? searchInput.value : '';
                    const category = categorySelect ? categorySelect.value : 'all';
                    const limit = pageSizeSelector ? pageSizeSelector.value : 10;
                    
                    // Get sort parameters
                    const sortColumn = document.getElementById('public-sort-column').value;
                    const sortDirection = document.getElementById('public-sort-direction').value;
                    
                    // Always use normal search for pagination
                    loadProducts(searchTerm, category, page, limit, sortColumn, sortDirection);
                }
            });
        });
    }
    
    // Update page size selector if available
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector && pagination.pageSizeOptions) {
        let options = '';
        pagination.pageSizeOptions.forEach(size => {
            const selected = size == pagination.itemsPerPage ? 'selected' : '';
            options += `<option value="${size}" ${selected}>${size}</option>`;
        });
        pageSizeSelector.innerHTML = options;
    }
}

document.addEventListener('click', function(event) {
    // Find the closest clickable row to the click target
    const row = event.target.closest('.clickable-row');
    if (row && row.dataset.href) {
        // Don't navigate if clicking on a control element
        if (!event.target.closest('a, button, input, select, .no-click')) {
            console.log('Global handler navigating to:', row.dataset.href);
            window.location.href = row.dataset.href;
        }
    }
});
</script>

<?php
/**
 * Get categories for dropdown
 * 
 * @return array Array of categories
 */
function getCategories() {
    global $pdo;
    
    try {
        // Get language from session or default to Swedish
        $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
        
        // Determine field name based on language
        $nameField = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
        
        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT category_id, {$nameField} as category_name FROM category ORDER BY {$nameField} ASC");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching categories: ' . $e->getMessage());
        return [];
    }
}
?>

