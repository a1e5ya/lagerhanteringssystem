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

// Handle logout (POST request with CSRF protection)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Check CSRF token for logout
    checkCSRFToken();
    
    // Perform logout
    $result = logout();
    
    // Redirect to homepage
    header('Location: ' . url('index.php', ['message' => urlencode($result['message'])]));
    exit;
}




// Clean URL for default view
if (empty($_GET['search']) && 
    (empty($_GET['category']) || $_GET['category'] === 'all') &&
    isset($_GET['page']) && 
    isset($_GET['limit'])) {
    // Redirect to clean URL
    header('Location: ' . url('index.php'));
    exit;
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
 * Get product images for a specific product
 * 
 * @param int $productId Product ID
 * @param PDO $pdo Database connection
 * @return array Array of image objects
 */
function getProductImages($productId, $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT image_id, image_path, image_uploaded_at
            FROM image
            WHERE prod_id = ?
            ORDER BY image_uploaded_at ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error fetching product images: " . $e->getMessage());
        return [];
    }
}

/**
 * Get product image URL (first image or default)
 * 
 * @param int $productId Product ID
 * @param PDO $pdo Database connection
 * @return string Image URL (either actual image or default)
 */
function getProductImageUrl($productId, $pdo) {
    $images = getProductImages($productId, $pdo);
    
    if (!empty($images)) {
        return $images[0]->image_path;
    }
    
    // Return default image if no images found
    return 'assets/images/default_antiqe_image.webp';
}

/**
 * Get featured products for the homepage (UPDATED VERSION)
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
        // Build the main query with author information
        $sql = "SELECT DISTINCT
                    p.prod_id, 
                    p.title, 
                    p.price, 
                    p.special_price,
                    p.recommended,
                    p.category_id,
                    (SELECT GROUP_CONCAT(a.author_name SEPARATOR ', ') 
                     FROM product_author pa 
                     JOIN author a ON pa.author_id = a.author_id 
                     WHERE pa.product_id = p.prod_id) as author_name
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
        
        // Get category information for each product
        foreach ($products as $product) {
            // Get category for this product
            $catSql = "SELECT category_sv_name AS category_name 
                       FROM category c 
                       WHERE c.category_id = :category_id";
            $catStmt = $pdo->prepare($catSql);
            $catStmt->bindParam(':category_id', $product->category_id, PDO::PARAM_INT);
            $catStmt->execute();
            $category = $catStmt->fetch(PDO::FETCH_OBJ);
            
            if ($category) {
                $product->category_name = $category->category_name;
            } else {
                $product->category_name = '';
            }
            
            // Ensure author_name is not null
            if (empty($product->author_name)) {
                $product->author_name = '';
            }
        }
        
        return $products;
    } catch (PDOException $e) {
        error_log('Error fetching featured products: ' . $e->getMessage());
        return [];
    }
}

/**
 * Render a product card with single image only (UPDATED VERSION)
 * 
 * @param object $product The product object
 */
function renderProductCard(object $product): void {
    global $pdo;
    
    // Get first product image only
    $productImages = getProductImages($product->prod_id, $pdo);
    $firstImage = !empty($productImages) ? $productImages[0]->image_path : asset('images', 'default_antiqe_image.webp');
    
    ?>
    <div class="col">
        <div class="card h-100">
            <!-- For mobile: horizontal layout -->
            <div class="d-block d-md-none">
                <div class="row g-0 h-100">
                    <div class="col-6">
                        <img src="<?php echo safeEcho($firstImage); ?>" 
                             class="card-img-top h-100 object-fit-cover" 
                             alt="<?php echo safeEcho($product->title); ?>" loading="lazy">
                    </div>
                    <div class="col-6">
                        <div class="card-body d-flex flex-column h-100">
                            <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                            <p class="card-text text-muted flex-grow-1">
                                <?php echo safeEcho($product->author_name ?? ''); ?>
                            </p>
                            <?php if (isset($product->price) && $product->price !== null && $product->price > 0): ?>
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
                <img src="<?php echo safeEcho($firstImage); ?>" 
                     class="card-img-top" style="height: 180px; object-fit: cover;" 
                     alt="<?php echo safeEcho($product->title); ?>" loading="lazy">
                
                <div class="card-body">
                    <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                    <p class="card-text text-muted">
                        <?php echo safeEcho($product->author_name ?? ''); ?>
                    </p>
                    <?php if (isset($product->price) && $product->price !== null && $product->price > 0): ?>
                    <p class="text-success fw-bold"><?php echo number_format((float)$product->price, 2); ?> €</p>
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

/**
 * Get fallback products when no special/recommended products exist
 * 
 * @param PDO $pdo Database connection
 * @param int $limit Number of products to retrieve
 * @return array Random products
 */
function getFallbackProducts(PDO $pdo, int $limit = 4): array {
    try {
        $sql = "SELECT DISTINCT
                    p.prod_id, 
                    p.title, 
                    p.price, 
                    p.special_price,
                    p.recommended,
                    p.category_id,
                    (SELECT GROUP_CONCAT(a.author_name SEPARATOR ', ') 
                     FROM product_author pa 
                     JOIN author a ON pa.author_id = a.author_id 
                     WHERE pa.product_id = p.prod_id) as author_name
                FROM product p
                WHERE p.status = 1 AND p.condition_id IN (1, 2)
                ORDER BY RAND()
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Get category information for each product
        foreach ($products as $product) {
            // Get category for this product
            $catSql = "SELECT category_sv_name AS category_name 
                       FROM category c 
                       WHERE c.category_id = :category_id";
            $catStmt = $pdo->prepare($catSql);
            $catStmt->bindParam(':category_id', $product->category_id, PDO::PARAM_INT);
            $catStmt->execute();
            $category = $catStmt->fetch(PDO::FETCH_OBJ);
            
            if ($category) {
                $product->category_name = $category->category_name;
            } else {
                $product->category_name = '';
            }
            
            // Ensure author_name is not null
            if (empty($product->author_name)) {
                $product->author_name = '';
            }
        }
        
        return $products;
    } catch (PDOException $e) {
        error_log('Error fetching fallback products: ' . $e->getMessage());
        return [];
    }
}

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

// Get special products (on sale)
$specialProducts = getFeaturedProducts($pdo, 4, true); // Only special_price = 1

// If no special products found, get fallback products
if (empty($specialProducts)) {
    $specialProducts = getFallbackProducts($pdo, 4);
}

// Get recommended products
$recommendedProducts = getFeaturedProducts($pdo, 4, false, true); // Only recommended = 1

// If no recommended products, get fallback products
if (empty($recommendedProducts)) {
    $recommendedProducts = getFallbackProducts($pdo, 4);
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
        <div class="text-center mt-5">
            <button class="btn btn-outline-danger fs-5 mt-5" onclick="window.location.href='sale.php';"><?php echo $lang_strings['go_sale'] ?? 'Rea - Klicka här!'; ?></button>
        </div>
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


<script>
    // Translations for JavaScript
const translations = {
    limitNotification: '<?php echo addslashes($lang_strings['limit_notification'] ?? 'Visar de första 1000 produkterna av totalt {total}. Använd sök eller filter för att se fler produkter.'); ?>',
    limitNotificationShort: '<?php echo addslashes($lang_strings['limit_notification_short'] ?? 'Visar de första {limit} produkterna av totalt {total}.'); ?>',
    useFiltersText: '<?php echo addslashes($lang_strings['use_filters_text'] ?? 'Använd sök eller filter för att se fler produkter.'); ?>'
};
document.addEventListener('DOMContentLoaded', function () {
    // Make table rows clickable
    makeRowsClickable();

    // Handle search form submission
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Regular search - not random samples
            performPublicSearch(1, false);
        });
    }

    // Handle category filter change
    const categorySelect = document.getElementById('public-category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }

    // Handle page size selector
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector) {
        pageSizeSelector.addEventListener('change', function() {
            const searchTerm = document.getElementById('public-search').value;
            const category = document.getElementById('public-category').value;
            const limit = this.value;
            const sort = document.getElementById('public-sort-column').value;
            const order = document.getElementById('public-sort-direction').value;
            
            const paginationLinks = document.getElementById('pagination-links');
            const isRandomSamples = paginationLinks && paginationLinks.getAttribute('data-random-samples') === 'true';
            
            loadProducts(searchTerm, category, 1, limit, sort, order, isRandomSamples);
        });
    }

    // Handle table header sorting
    const sortHeaders = document.querySelectorAll('th[data-sort]');
    sortHeaders.forEach(header => {
        header.addEventListener('click', function () {
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

            // Update visual indicators
            sortHeaders.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            this.classList.add(`sort-${newSortDirection === 'asc' ? 'desc' : 'asc'}`);

            // Get current search parameters
            const searchTerm = document.getElementById('public-search').value;
            const category = document.getElementById('public-category').value;
            const limit = document.getElementById('page-size-selector').value;
            
            // IMPORTANT: When sorting, always switch to showing all products (not random samples)
            const isRandomSamples = false;

            // Call loadProducts with ALL parameters including sort
            loadProducts(searchTerm, category, 1, limit, sortColumn, newSortDirection, isRandomSamples);
        });
    });

// Load initial products - only if there are URL parameters, otherwise use clean initial load
const urlParams = new URLSearchParams(window.location.search);

// Check if we have any meaningful parameters that should trigger a search
const searchTerm = urlParams.get('search') || '';
const category = urlParams.get('category') || 'all';
const sort = urlParams.get('sort') || '';
const order = urlParams.get('order') || 'asc';
const limit = urlParams.get('limit') || 10;
const page = urlParams.get('page') || 1;

// Only load products via AJAX if we have actual parameters in URL
const hasUrlParameters = urlParams.has('search') || 
                         urlParams.has('category') || 
                         urlParams.has('sort') || 
                         urlParams.has('page') || 
                         urlParams.has('limit') ||
                         urlParams.has('random_samples');

if (hasUrlParameters) {
    // Determine if random samples should be loaded
    const isRandomSamples = !searchTerm && category === 'all' && !sort && urlParams.has('random_samples');
    
    // Load products with the current parameters
    loadProducts(searchTerm, category, page, limit, sort, order, isRandomSamples);
} else {
    // Clean initial load - load random samples without updating URL
    loadProductsInitial();
}
});

/**
 * Perform public search
 * 
 * @param {number} page - Page number
 * @param {boolean} randomSamples - Whether to load random samples
 */
function performPublicSearch(page = 1, randomSamples = false) {
    const searchTerm = document.getElementById('public-search').value;
    const category = document.getElementById('public-category').value;
    const limit = document.getElementById('page-size-selector').value;
    const sort = document.getElementById('public-sort-column').value;
    const order = document.getElementById('public-sort-direction').value;
    
    loadProducts(searchTerm, category, page, limit, sort, order, randomSamples);
}

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

    // Update URL for bookmarking/history
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

    // Add random_samples to the URL if needed
    if (randomSamples) {
        url.searchParams.set('random_samples', 'true');
    } else {
        url.searchParams.delete('random_samples');
    }

    window.history.pushState({}, '', url);

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

    // Fetch products
    fetch('api/get_public_products.php?' + new URLSearchParams(requestParams))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update table with products
                if (data.html && tableBody) {
                    tableBody.innerHTML = data.html;
                    makeRowsClickable(); // Re-initialize clickable rows
                }

                // Update pagination info
                if (data.pagination) {
                    updatePaginationInfo(data.pagination, randomSamples);
                }

                // Restore sort indicators
                const sortHeaders = document.querySelectorAll('th[data-sort]');
                sortHeaders.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));

                if (sort) {
                const activeHeader = document.querySelector(`th[data-sort="${sort}"]`);
                if (activeHeader) {
                    activeHeader.classList.add(`sort-${order === 'asc' ? 'desc' : 'asc'}`);
                }
            }
            } else {
                // Show error message
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`;
                }
            }
        });
}

/**
 * Update pagination information with 1000 limit support and translations
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

    // Check if limit was applied and show notification
    const paginationInfo = document.getElementById('pagination-info');
    if (paginationInfo && pagination.limitApplied) {
        // Create or update the limit notification
        let limitNotification = document.getElementById('limit-notification');
        if (!limitNotification) {
            limitNotification = document.createElement('div');
            limitNotification.id = 'limit-notification';
            limitNotification.className = 'mt-2 mb-0 p-2 border rounded';
            paginationInfo.parentNode.insertBefore(limitNotification, paginationInfo.nextSibling);
        }
        
        // Create the notification text using translations
        const limitText = translations.limitNotificationShort
            .replace('{limit}', pagination.totalItems.toLocaleString())
            .replace('{total}', pagination.actualTotalItems.toLocaleString());
        
        limitNotification.innerHTML = `
            <small>
                ${limitText} ${translations.useFiltersText}
            </small>
        `;
    } else {
        // Remove notification if it exists and limit is not applied
        const limitNotification = document.getElementById('limit-notification');
        if (limitNotification) {
            limitNotification.remove();
        }
    }

    // Rest of the pagination function remains the same...
    const paginationLinks = document.getElementById('pagination-links');
    if (paginationLinks) {
        let html = '';

        // Get current sort and order
        const sort = document.getElementById('public-sort-column').value;
        const order = document.getElementById('public-sort-direction').value;
        const limit = document.getElementById('page-size-selector').value;

        // Previous page button
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage - 1}" data-limit="${limit}" data-sort="${sort}" data-order="${order}" aria-label="Previous" ${pagination.currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
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
                    <a class="page-link" href="#" data-page="1" data-limit="${limit}" data-sort="${sort}" data-order="${order}">1</a>
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
                    <a class="page-link" href="#" data-page="${i}" data-limit="${limit}" data-sort="${sort}" data-order="${order}">${i}</a>
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
                    <a class="page-link" href="#" data-page="${pagination.totalPages}" data-limit="${limit}" data-sort="${sort}" data-order="${order}">${pagination.totalPages}</a>
                </li>
            `;
        }

        // Next page button
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage + 1}" data-limit="${limit}" data-sort="${sort}" data-order="${order}" aria-label="Next" ${pagination.currentPage >= pagination.totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
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
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const page = parseInt(this.dataset.page, 10);
                const limit = parseInt(this.dataset.limit, 10);
                const sort = this.dataset.sort || '';
                const order = this.dataset.order || 'asc';

                if (!isNaN(page)) {
                    // Check if we're in random samples mode
                    const isRandomSamples = paginationLinks.getAttribute('data-random-samples') === 'true';

                    // Call loadProducts with the current sort and order
                    loadProducts(
                        document.getElementById('public-search').value,
                        document.getElementById('public-category').value,
                        page,
                        limit,
                        sort,
                        order,
                        isRandomSamples
                    );
                }
            });
        });
    }

    // Update page size selector to show current value
    const pageSizeSelector = document.getElementById('page-size-selector');
    if (pageSizeSelector && pagination.itemsPerPage) {
        pageSizeSelector.value = pagination.itemsPerPage;
    }
}

document.addEventListener('click', function(event) {
    // Find the closest clickable row to the click target
    const row = event.target.closest('.clickable-row');
    if (row && row.dataset.href) {
        // Don't navigate if clicking on a control element
        if (!event.target.closest('a, button, input, select, .no-click')) {
            window.location.href = row.dataset.href;
        }
    }
});

/**
 * Load initial products without updating URL (for clean homepage)
 */
function loadProductsInitial() {
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

    // Set request parameters for random samples
    const requestParams = {
        search: '',
        category: '',
        page: 1,
        limit: 10,
        sort: '',
        order: 'asc',
        random_samples: 'true'
    };

    // Fetch products but DON'T update URL
    fetch('api/get_public_products.php?' + new URLSearchParams(requestParams))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update table with products
                if (data.html && tableBody) {
                    tableBody.innerHTML = data.html;
                    makeRowsClickable(); // Re-initialize clickable rows
                }

                // Update pagination info
                if (data.pagination) {
                    updatePaginationInfo(data.pagination, true); // true = random samples mode
                }
            } else {
                // Show error message
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`;
                }
            }
        })
        .catch(error => {
            console.error('Error loading initial products:', error);
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Ett fel inträffade vid laddning av produkter</td></tr>`;
            }
        });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle session messages from password reset and other operations
    <?php
    // Check for messages in session (from password reset redirects)
    if (isset($_SESSION['message'])) {
        $sessionMessage = $_SESSION['message'];
        unset($_SESSION['message']); // Clear message after displaying
        
        $messageType = $sessionMessage['success'] ? 'success' : 'error';
        ?>
        setTimeout(function() {
            if (window.messageSystem) {
                window.messageSystem.<?php echo $messageType; ?>('<?php echo addslashes($sessionMessage['message']); ?>');
            }
        }, 100);
        <?php
    }
    
    // Also handle legacy URL parameters for backward compatibility
    if (isset($_GET['success'])) {
        $message = urldecode($_GET['success']);
        ?>
        setTimeout(function() {
            if (window.messageSystem) {
                window.messageSystem.success('<?php echo addslashes($message); ?>');
            }
        }, 100);
        <?php
    }
    
    if (isset($_GET['error'])) {
        $message = urldecode($_GET['error']);
        ?>
        setTimeout(function() {
            if (window.messageSystem) {
                window.messageSystem.error('<?php echo addslashes($message); ?>');
            }
        }, 100);
        <?php
    }
    
    if (isset($_GET['warning'])) {
        $message = urldecode($_GET['warning']);
        ?>
        setTimeout(function() {
            if (window.messageSystem) {
                window.messageSystem.warning('<?php echo addslashes($message); ?>');
            }
        }, 100);
        <?php
    }
    
    if (isset($_GET['info'])) {
        $message = urldecode($_GET['info']);
        ?>
        setTimeout(function() {
            if (window.messageSystem) {
                window.messageSystem.info('<?php echo addslashes($message); ?>');
            }
        }, 100);
        <?php
    }
    ?>
});
</script>