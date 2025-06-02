<?php

// Add the viewport meta tag to the <head> of the HTML document to ensure responsive layout on mobile devices.
// Include necessary files
require_once 'init.php';

// Clean URL for default view (redirect to self with sale-specific parameters)
// We are explicitly setting the initial state for the sale page
if (empty($_GET['search']) && 
    (empty($_GET['category']) || $_GET['category'] === 'all') && // category check remains for the redirect purpose
    isset($_GET['page']) && 
    isset($_GET['limit']) &&
    !isset($_GET['sale_only'])) { 
    header('Location: sale.php?sale_only=true&limit=25'); // Redirect to ensure sale_only is always true and set a default limit
    exit;
}




// Determine current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Load language strings using the existing function from ui.php
$lang_strings = loadLanguageStrings($language);

// *** REMOVED: Get categories for search dropdown - no longer needed ***
// try {
//     $categories = getCategories();
// } catch (Exception $e) {
//     error_log('Error fetching categories: ' . $e->getMessage());
//     $categories = [];
// }

// Create formatter instance
$formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');

/**
 * Get products that are on sale (special_price = 1) - this function is not directly used for the AJAX table
 * but is a remnant of previous logic. It's okay to keep if other parts of the site call it,
 * but it doesn't affect the AJAX table anymore.
 * @param PDO $pdo Database connection
 * @param int $limit Number of products to retrieve
 * @return array Sale products
 */
function getSaleProducts(PDO $pdo, int $limit = 25): array 
{
    try {
        $sql = "SELECT 
                    p.prod_id, 
                    p.title, 
                    p.price, 
                    p.special_price,
                    p.recommended,
                    a.author_name
                FROM product p
                LEFT JOIN product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN author a ON pa.author_id = a.author_id
                WHERE p.status = 1 AND p.special_price = 1
                GROUP BY p.prod_id
                ORDER BY p.title ASC
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log('Error fetching sale products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Render a product card (this function remains the same as it's a display utility)
 * @param object $product The product object
 */
function renderProductCard(object $product): void {
    $imagePath = 'assets/images/' . $product->prod_id . '.jpg';
    $defaultImage = 'assets/images/default_antiqe_image.webp';
    $imageToShow = file_exists($imagePath) ? $imagePath : $defaultImage;
    ?>
    <div class="col">
        <a href="singleproduct.php?id=<?php echo $product->prod_id; ?>" class="card-link">
        <div class="card h-100">
            <img src="<?php echo $imageToShow; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo safeEcho($product->title); ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                <p class="card-text"><?php echo safeEcho($product->author_name); ?></p>
                <p class="card-text fw-bold text-success"><?php echo number_format($product->price, 2); ?> €</p>
                
            </div>
        </div>
</a>
    </div>
    <?php
}

// Include header
include 'templates/header.php';
?>

<div class="container my-4 flex-grow-1">
    <section id="browse" class="my-5">
        <h2 class="mb-4"><?php echo $lang_strings['sales_heading'] ?? 'Produkter på Rea'; ?></h2> 
        
        <div class="search-bar mb-4">
            <form method="get" action="" id="search-form">
                <div class="row">
                    <div class="col-md-10 mb-3 mb-md-0"> <input type="text" class="form-control" id="public-search" name="search" 
                               placeholder="<?php echo $lang_strings['search_sale_placeholder'] ?? 'Sök bland reaprodukter'; ?>" 
                               value="<?= isset($_GET['search']) ? safeEcho($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" id="public-search-btn">
                            <?php echo $lang_strings['search_button'] ?? 'Sök'; ?>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="sort" id="public-sort-column" value="<?= safeEcho($_GET['sort'] ?? '') ?>">
                <input type="hidden" name="order" id="public-sort-direction" value="<?= safeEcho($_GET['order'] ?? 'asc') ?>">
                <input type="hidden" name="sale_only" value="true"> 
            </form>
        </div>
        
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
            
            <div class="mt-3" id="pagination-controls">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <label class="me-2"><?php echo $lang_strings['show'] ?? 'Visa'; ?></label>
                            <select class="form-select form-select-sm" id="page-size-selector" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25" selected>25</option> <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="ms-2"><?php echo $lang_strings['items'] ?? 'objekt'; ?></span>
                        </div>
                    </div>
                    
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
                    
                    <div class="col-md-4 d-flex justify-content-md-end">
                        <ul class="pagination mb-0" id="pagination-links">
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>



<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("Initializing sale product search...");
    
    // Make table rows clickable
    makeRowsClickable();
    
    // Handle search form submission (for actual search button click)
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchInput = document.getElementById('public-search');
            const pageSizeSelector = document.getElementById('page-size-selector'); // Get current limit

            const searchTerm = searchInput ? searchInput.value.trim() : '';
            const limit = pageSizeSelector ? pageSizeSelector.value : 25;
            
            const sortColumn = document.getElementById('public-sort-column').value;
            const sortDirection = document.getElementById('public-sort-direction').value;

            // When searching or submitting, always go to page 1
            loadProducts(searchTerm, 'all', 1, limit, sortColumn, sortDirection); // Pass 'all' as category since it's no longer selectable
        });
    }
    
    // *** REMOVED: Handle category filter change - no longer needed ***
    // const categorySelect = document.getElementById('public-category');
    // if (categorySelect) {
    //     categorySelect.addEventListener('change', function() {
    //         const searchTerm = document.getElementById('public-search').value.trim();
    //         const category = this.value; 
    //         const limit = document.getElementById('page-size-selector').value || 25;
    //         const sort = document.getElementById('public-sort-column').value;
    //         const order = document.getElementById('public-sort-direction').value;
    //         loadProducts(searchTerm, category, 1, limit, sort, order);
    //     });
    // }
    
    // Handle page size change - DIRECTLY CALL loadProducts
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector) {
        pageSizeSelector.addEventListener('change', function() {
            const searchTerm = document.getElementById('public-search').value.trim();
            // Category is effectively always 'all' now
            const limit = this.value; // The newly selected limit
            const sort = document.getElementById('public-sort-column').value;
            const order = document.getElementById('public-sort-direction').value;
            loadProducts(searchTerm, 'all', 1, limit, sort, order); // Pass 'all' as category
        });
    }
    
    // Handle table header sorting
    const sortHeaders = document.querySelectorAll('th[data-sort]');
    sortHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortColumn = this.dataset.sort;
            
            const currentSortColumn = document.getElementById('public-sort-column').value;
            let currentSortDirection = document.getElementById('public-sort-direction').value;
            
            let newSortDirection = 'asc';
            if (sortColumn === currentSortColumn) {
                newSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            }
            
            document.getElementById('public-sort-column').value = sortColumn;
            document.getElementById('public-sort-direction').value = newSortDirection;
            
            sortHeaders.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            this.classList.add(`sort-${newSortDirection === 'asc' ? 'desc' : 'asc'}`);
            
            // Get current search parameters
            const searchInput = document.getElementById('public-search');
            const limit = pageSizeSelector ? pageSizeSelector.value : 25;
            
            const searchTerm = searchInput ? searchInput.value : '';
            
            loadProducts(searchTerm, 'all', 1, limit, sortColumn, newSortDirection); // Pass 'all' as category
        });
    });
    
    // Load initial products, always fetching sale items
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search') || '';
    const category = urlParams.get('category') || 'all'; // Keep this for initial URL parsing for robustness
    const page = parseInt(urlParams.get('page')) || 1;
    const limit = parseInt(urlParams.get('limit')) || 25; // Default to 25 for sale page
    const sort = urlParams.get('sort') || '';
    const order = urlParams.get('order') || 'asc';

    // Ensure `sale_only=true` (or rather `special_price=1`) is part of the initial load.
    loadProducts(searchTerm, category, page, limit, sort, order);
});

/**
 * Load products via AJAX, always filtered by special_price = 1
 * @param {string} searchTerm - Search term
 * @param {string} category - Category (now always 'all' for this page)
 * @param {number} page - Page number
 * @param {number} limit - Items per page
 * @param {string} sort - Sort column
 * @param {string} order - Sort direction
 */
function loadProducts(searchTerm = '', category = 'all', page = 1, limit = 25, sort = '', order = 'asc') { // category parameter is now effectively ignored and hardcoded to 'all' for internal logic
    console.log('Loading sale products with parameters:', { searchTerm, category, page, limit, sort, order });
    
    // Get the value of the hidden input `sale_only`
    const saleOnlyInput = document.querySelector('input[name="sale_only"]');
    const isSaleActive = saleOnlyInput && saleOnlyInput.value === 'true'; 

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
    const url = new URL(window.location.href);
    url.searchParams.set('search', searchTerm);
    url.searchParams.set('category', 'all'); // Always set category to 'all' in URL
    url.searchParams.set('page', page);
    url.searchParams.set('limit', limit);
    
    if (isSaleActive) {
        url.searchParams.set('special_price', '1');
        url.searchParams.delete('sale_only'); 
    } else {
        url.searchParams.delete('special_price');
        url.searchParams.delete('sale_only'); 
    }

    if (sort) {
        url.searchParams.set('sort', sort);
        url.searchParams.set('order', order);
    } else {
        url.searchParams.delete('sort');
        url.searchParams.delete('order');
    }
    window.history.pushState({}, '', url);
    
    // Set request parameters for the fetch call
    const requestParams = {
        search: searchTerm,
        category: 'all', // Always send 'all' as category to the API
        page: page,
        limit: limit,
        sort: sort,
        order: order
    };

    if (isSaleActive) {
        requestParams.special_price = 1; 
    } else {
        delete requestParams.special_price;
    }
    
    // Make AJAX request to the api endpoint
    console.log('Sending requestParams:', requestParams); 
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
            
            // Scroll to browse section if this was a search/pagination action
            document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
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
 * Update pagination information (this function remains the same)
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
                    const pageSizeSelector = document.getElementById('page-size-selector');
                    
                    const searchTerm = searchInput ? searchInput.value : '';
                    const limit = pageSizeSelector ? pageSizeSelector.value : 25; 
                    
                    // Get sort parameters
                    const sortColumn = document.getElementById('public-sort-column').value;
                    const sortDirection = document.getElementById('public-sort-direction').value;
                    
                    loadProducts(searchTerm, 'all', page, limit, sortColumn, sortDirection); // Pass 'all' as category
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
 * Get categories for dropdown (this function is no longer called for the AJAX table, but keeping for completeness)
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