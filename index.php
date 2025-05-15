<?php
/**
 * Home Page
 * 
 * Contains:
 * - Feature items display
 * - Search functionality
 * - Language switching
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/ui.php';
require_once 'admin/search.php';

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

// Load language strings
$strings = loadLanguageStrings($language);

// Get categories for search dropdown
try {
    $categories = getCategories($pdo);
} catch (Exception $e) {
    error_log('Error fetching categories: ' . $e->getMessage());
    $categories = [];
}

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

// Get special products
 $specialProducts = getFeaturedProducts($pdo, 4);

// Prepare search parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Always 10 for homepage

// Prepare search parameters for products
$searchParams = [
    'page' => $page,
    'limit' => $limit,
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? 'all'
];

// Fetch search results
try {
    $searchResults = searchProducts($searchParams);
} catch (Exception $e) {
    error_log('Search error: ' . $e->getMessage());
    $searchResults = [];
}

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
                <h1><?php echo $strings['welcome']; ?></h1>
                <p class="lead"><?php echo $strings['subtitle']; ?></p>
                <a href="#browse" class="btn btn-primary btn-lg mt-3 border-light"><?php echo $strings['browse_button']; ?></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="container my-4">


    <!-- Browse Section -->
    <section id="browse" class="my-5">
        <h2 class="mb-4"><?php echo $strings['browse_heading']; ?></h2>
        
        <div class="search-bar mb-4">
            <form method="get" action="" id="search-form">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <input type="text" class="form-control" id="public-search" name="search" 
                            placeholder="<?php echo $strings['search_placeholder']; ?>" 
                            value="<?= isset($_GET['search']) ? safeEcho($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <select class="form-select" id="public-category" name="category">
                            <option value="all"><?php echo $strings['all_categories']; ?></option>
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
                            <?php echo $strings['search_button']; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="public-inventory-table">
                <thead class="table-light">
                    <tr>
                        <th><?php echo $strings['title']; ?></th>
                        <th><?php echo $strings['author_artist']; ?></th>
                        <th><?php echo $strings['category']; ?></th>
                        <th><?php echo $strings['genre']; ?></th>
                        <th><?php echo $strings['condition']; ?></th>
                        <th><?php echo $strings['price']; ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="public-inventory-body">
                    <?= renderIndexProducts($searchResults) ?>
                </tbody>
            </table>
        </div>
    </section>

        <!-- Special Products Section -->
        <section class="my-5">
        <h2 class="mb-4"><?php echo $strings['on_sale']; ?></h2>
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
                            <?php echo number_format($product->price, 2, ',', ' ') . ' €'; ?>
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
<script src="assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('public-search');
    const categorySelect = document.getElementById('public-category');
    
    // Skip if elements don't exist
    if (!searchForm || !searchInput || !categorySelect) return;
    
    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performPublicSearch(1); // Start with page 1 on new search
    });
    
    // Add change event listener to category dropdown
    categorySelect.addEventListener('change', function() {
        performPublicSearch(1); // Start with page 1 on category change
    });
    
    // Initial load of products
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 1;
    
    if (!searchInput.value && categorySelect.value === 'all') {
        // If no search filters, load default products with pagination
        performPublicSearch(currentPage);
    } else {
        // If there are search filters, use them
        performPublicSearch(currentPage);
    }
});
</script>