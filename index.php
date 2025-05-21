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

// Include initialization file (replaces multiple require statements)
require_once 'init.php';

// Clean URL for default view
if (empty($_GET['search']) && 
    (empty($_GET['category']) || $_GET['category'] === 'all') &&
    isset($_GET['page']) && 
    isset($_GET['limit'])) {
    // Redirect to clean URL
    header('Location: ' . url('index.php'));
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
 * @param bool $onlySpecial If true, only get special_price products
 * @param bool $onlyRecommended If true, only get recommended products
 * @return array Featured products
 */
function getFeaturedProducts(PDO $pdo, int $limit = 4, bool $onlySpecial = false, bool $onlyRecommended = false): array 
{
    try {
        // Simplify the query to avoid potential JOIN issues
        $sql = "SELECT 
                    p.prod_id, 
                    p.title, 
                    p.price, 
                    p.special_price,
                    p.recommended
                FROM product p
                WHERE p.status = 1 ";

        if ($onlySpecial) {
            $sql .= " AND p.special_price = 1 ";
        } elseif ($onlyRecommended) {
            $sql .= " AND p.recommended = 1 ";
        } else {
            $sql .= " AND (p.special_price = 1 OR p.recommended = 1) ";
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Fetch author information separately for each product
        foreach ($products as $product) {
            // Get author for this product
            $authorSql = "SELECT a.author_name 
                          FROM author a 
                          JOIN product_author pa ON a.author_id = pa.author_id 
                          WHERE pa.product_id = :prod_id 
                          LIMIT 1";
            $authorStmt = $pdo->prepare($authorSql);
            $authorStmt->bindParam(':prod_id', $product->prod_id, PDO::PARAM_INT);
            $authorStmt->execute();
            $author = $authorStmt->fetch(PDO::FETCH_OBJ);
            
            if ($author) {
                $product->author_name = $author->author_name;
            } else {
                $product->author_name = '';
            }
            
            // Get category for this product
            $catSql = "SELECT category_sv_name AS category_name 
                       FROM category c 
                       JOIN product p ON c.category_id = p.category_id 
                       WHERE p.prod_id = :prod_id";
            $catStmt = $pdo->prepare($catSql);
            $catStmt->bindParam(':prod_id', $product->prod_id, PDO::PARAM_INT);
            $catStmt->execute();
            $category = $catStmt->fetch(PDO::FETCH_OBJ);
            
            if ($category) {
                $product->category_name = $category->category_name;
            } else {
                $product->category_name = '';
            }
        }
        
        return $products;
    } catch (PDOException $e) {
        error_log('Error fetching featured products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Render a product card
 * 
 * @param object $product The product object
 */
function renderProductCard(object $product): void {
    // Always use default image to avoid 404 errors
    $defaultImage = 'assets/images/default_antiqe_image.webp';
    
    ?>
    <div class="col">
        <div class="card h-100">
            <!-- For mobile: horizontal layout -->
            <div class="d-block d-md-none">
                <div class="row g-0 h-100">
                    <div class="col-6">
                        <img src="<?php echo $defaultImage; ?>" class="card-img-top h-100 object-fit-cover" alt="<?php echo htmlspecialchars($product->title); ?>">
                    </div>
                    <div class="col-6">
                        <div class="card-body d-flex flex-column h-100">
                            <h5 class="card-title"><?php echo htmlspecialchars($product->title); ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?php echo htmlspecialchars($product->author_name ?? ''); ?>
                            </p>
                            <?php if (isset($product->price) && $product->price !== null): ?>
                            <p class="text-success fw-bold mb-2"><?php echo number_format((float)$product->price, 2); ?> €</p>
                            <?php else: ?>
                            <p class="text-secondary fw-bold mb-2">Pris på förfrågan</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- For laptop/desktop: vertical layout -->
            <div class="d-none d-md-block">
                <img src="<?php echo $defaultImage; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($product->title); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product->title); ?></h5>
                    <p class="card-text text-muted">
                        <?php echo htmlspecialchars($product->author_name ?? ''); ?>
                    </p>
                    <?php if (isset($product->price) && $product->price !== null): ?>
                    <?php else: ?>
                    <p class="text-secondary fw-bold">Pris på förfrågan</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <a href="singleproduct.php?id=<?php echo $product->prod_id; ?>" class="stretched-link"></a>
        </div>
    </div>
    <?php
}


// Get special products with dedicated query
$specialProducts = getFeaturedProducts($pdo, 4, true); // Only special_price = 1

// If no special products found, get any random products
if (empty($specialProducts)) {
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                p.price, 
                p.special_price,
                p.recommended
            FROM product p
            WHERE p.status = 1 
            ORDER BY RAND()
            LIMIT 4";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $specialProducts = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get author and category info for each product
    foreach ($specialProducts as $product) {
        // (Get author and category as shown above)
    }
}

// Try to get recommended products
$recommendedProducts = getFeaturedProducts($pdo, 4, false, true); // Only recommended = 1

// If no recommended products, get random high-quality products
if (empty($recommendedProducts)) {
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                p.price, 
                p.special_price,
                p.recommended
            FROM product p
            WHERE p.status = 1 AND p.condition_id IN (1, 2) 
            ORDER BY RAND()
            LIMIT 4";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $recommendedProducts = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get author and category info for each product
    foreach ($recommendedProducts as $product) {
        // (Get author and category as shown above)
    }
}

// Page title
$pageTitle = "Karis Antikvariat";

// Include header
include 'templates/header.php';
?>

<!-- Hero Banner with Full Width Image -->
<div class="hero-container position-relative">
<img src="<?php echo asset('images', 'hero.webp'); ?>" alt="Karis Antikvariat" class="hero-image w-100">
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

<!-- About Section -->
        <section id="about" class="my-5">
            <div class="row">
                <div class="col-lg-6">
                    <h2><?php echo $lang_strings['about_heading']; ?></h2>
                    <p><?php echo $lang_strings['about_p1']; ?></p>
                    <p><?php echo $lang_strings['about_p2']; ?></p>
                    <p><?php echo $lang_strings['about_p3']; ?></p>
                </div>
                <div class="col-lg-6">
                    <!-- Image Carousel -->
                    <div id="storeCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            <button type="button" data-bs-target="#storeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                        </div>
                        <div class="carousel-inner rounded">
                            <div class="carousel-item active">
                                <img src="assets/images/bild1.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 1">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild2.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 2">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild3.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 3">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/bild4.webp" class="d-block w-100" alt="Karis Antikvariat butiksbild 4">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#storeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"><?php echo $lang_strings['previous']; ?></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"><?php echo $lang_strings['next']; ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>


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
                            value="<?= isset($_GET['search']) ? (function_exists('safeEcho') ? safeEcho($_GET['search']) : htmlspecialchars($_GET['search'])) : '' ?>">
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
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option> <!-- Maximum value -->
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
        <h2 class="mb-4"><?php echo $lang_strings['on_sale'] ?? 'På rea'; ?>
        </h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="featured-items">
            <?php
            if (!empty($specialProducts)) {
                foreach ($specialProducts as $product) {
                    renderProductCard($product);
                }
            } else {
                echo "<p>Inga produkter på rea hittades.</p>";
            }
            ?>
        </div>
        <button class="btn btn-danger fs-5 mt-3" onclick="window.location.href='sale.php';"><?php echo $lang_strings['go_sale'] ?? 'Rea - Klicka här!'; ?></button>
    </section>

    <section class="my-5">
        <h2 class="mb-4"><?php echo $lang_strings['on_rec'] ?? 'Rekommenderat'; ?></h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="recommended-items">
            <?php
            if (!empty($recommendedProducts)) {
                foreach ($recommendedProducts as $product) {
                    renderProductCard($product);
                }
            } else {
                echo "<p>Inga rekommenderade produkter hittades.</p>";
            }
            ?>
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
<script src="<?php echo url('assets/js/main.js'); ?>"></script>



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
            
            // Regular search - not random samples
            performPublicSearch(1, false);
        });
    }
    
    // Handle category filter change
    const categorySelect = document.getElementById('public-category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }
    
    // Handle page size selector
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector) {
        pageSizeSelector.addEventListener('change', pageSizeSelectorHandler);
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
            
            // Check if we're in random samples mode
            const isRandomSamples = document.getElementById('pagination-links').getAttribute('data-random-samples') === 'true';
            
            // Use performPublicSearch with sort parameters and maintain random samples mode
            performPublicSearch(1, isRandomSamples);
        });
    });
    
    // Load initial products - always use random samples on initial load
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search') || '';
    const category = urlParams.get('category') || 'all';
    
    if (searchTerm || (category !== 'all' && category !== '')) {
        // If search or category filter is applied, use regular search
        performPublicSearch(1, false);
    } else {
        // Otherwise, use random samples
        performPublicSearch(1, true);
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
    
    // Update URL for bookmarking/history (only for explicit searches)
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
    
    // Add random_samples parameter if needed
    if (randomSamples) {
        requestParams.random_samples = 'true';
    }
    
    // FIXED: Always use api/get_public_products.php instead of admin/search.php
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
                makeRowsClickable(); // Re-initialize clickable rows
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
 * @param {boolean} randomSamples - Whether we're in random samples mode
 */
function updatePaginationInfo(pagination, randomSamples = false) {
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
        
        // Add random samples flag to data attributes
        if (randomSamples) {
            paginationLinks.setAttribute('data-random-samples', 'true');
        } else {
            paginationLinks.removeAttribute('data-random-samples');
        }
        
        // Attach event listeners to pagination links
        const links = paginationLinks.querySelectorAll('.page-link');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const page = parseInt(this.dataset.page, 10);
                if (!isNaN(page)) {
                    // Check if we're in random samples mode
                    const isRandomSamples = paginationLinks.getAttribute('data-random-samples') === 'true';
                    
                    // Use performPublicSearch with appropriate random samples flag
                    performPublicSearch(page, isRandomSamples);
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
        
        // Add random samples flag to data attributes
        if (randomSamples) {
            pageSizeSelector.setAttribute('data-random-samples', 'true');
        } else {
            pageSizeSelector.removeAttribute('data-random-samples');
        }
        
        // Reinitialize event listener
        pageSizeSelector.removeEventListener('change', pageSizeSelectorHandler);
        pageSizeSelector.addEventListener('change', pageSizeSelectorHandler);
    }
}

/**
 * Handler for page size selector changes
 */
function pageSizeSelectorHandler() {
    // Get current page
    const currentPage = 1; // Always go back to page 1 when changing page size
    
    // Check if we're in random samples mode
    const isRandomSamples = this.getAttribute('data-random-samples') === 'true';
    
    // Use performPublicSearch with appropriate random samples flag
    performPublicSearch(currentPage, isRandomSamples);
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