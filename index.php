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

$categories = getCategories($pdo);
$specialProducts = getSpecialPriceProducts($pdo, 3); // 3 produkter på rea

// Check if language change is requested
if (isset($_GET['lang'])) {
    changeLanguage($_GET['lang']);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Load language strings
$strings = loadLanguageStrings($language);

// Get categories for the search dropdown
$categories = getCategories($pdo);

// Handle search form
$searchResults = [];
if (isset($_GET['search']) || (isset($_GET['category']) && $_GET['category'] !== 'all')) {
    $searchResults = searchProducts([
        'search' => $_GET['search'] ?? '',
        'category' => $_GET['category'] ?? 'all'
    ]);
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
    <!-- Homepage Content -->
    <div id="homepage" class="mb-5">
        <!-- About Section -->
        <section id="about" class="my-5">
            <div class="row">
                <div class="col-lg-6">
                    <h2><?php echo $strings['about_heading']; ?></h2>
                    <p><?php echo $strings['about_p1']; ?></p>
                    <p><?php echo $strings['about_p2']; ?></p>
                    <p><?php echo $strings['about_p3']; ?></p>
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
                            <span class="visually-hidden"><?php echo $strings['previous']; ?></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"><?php echo $strings['next']; ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Browse Section -->
        <section id="browse" class="my-5">
            <h2 class="mb-4"><?php echo $strings['browse_heading']; ?></h2>
            
            <div class="search-bar mb-4">
                <form method="get" action="" id="search-form">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="text" class="form-control" id="public-search" name="search" 
                                placeholder="<?php echo $strings['search_placeholder']; ?>" 
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <select class="form-select" id="public-category" name="category">
                                <option value="all"><?php echo $strings['all_categories']; ?></option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['category_id']) ?>" 
                                <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100" id="public-search-btn"><?php echo $strings['search_button']; ?></button>
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
<!--Anropar fuktionen renderIndexProducts -->            
            <?= renderIndexProducts($searchResults) ?>
        </tbody>
    </table>
</div>
        </section>

        <section class="my-5">
    <h2 class="mb-4"><?php echo $strings['on_sale']; ?></h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="featured-items">
        <?php foreach ($specialProducts as $product): ?>
        <div class="col">
            <div class="card h-100">
                <!-- For mobile: horizontal layout -->
                <div class="d-block d-md-none">
                    <div class="row g-0 h-100">
                        <div class="col-6">
                            <?php
                            // Check if product image exists
                            $productImagePath = 'uploads/products/' . $product->prod_id . '.jpg';
                            $productDefaultImage = 'assets/images/src-book.webp'; // Default image
                            $productImageToShow = file_exists($productImagePath) ? $productImagePath : $productDefaultImage;
                            ?>
                            <img src="<?php echo $productImageToShow; ?>" class="card-img-top h-100 object-fit-cover" alt="<?php echo htmlspecialchars($product->title); ?>">
                        </div>
                        <div class="col-6">
                            <div class="card-body d-flex flex-column h-100">
                                <h5 class="card-title"><?php echo htmlspecialchars($product->title); ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product->author_name); ?></p>
                                <p class="text-success fw-bold mb-2"><?php echo number_format($product->price, 2, ',', ' ') . ' €'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- For laptop/desktop: vertical layout -->
                <div class="d-none d-md-block">
                    <?php
                    // Check if product image exists
                    $productImagePath = 'uploads/products/' . $product->prod_id . '.jpg';
                    $productDefaultImage = 'assets/images/src-book.webp'; // Default image
                    $productImageToShow = file_exists($productImagePath) ? $productImagePath : $productDefaultImage;
                    ?>
                    <img src="<?php echo $productImageToShow; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($product->title); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product->title); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($product->author_name); ?></p>
                        <p class="text-success fw-bold"><?php echo number_format($product->price, 2, ',', ' ') . ' €'; ?></p>
                    </div>
                </div>
               
                <a href="singleproduct.php?id=<?php echo htmlspecialchars($product->prod_id); ?>" class="stretched-link"></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>


        
    </div>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('#public-inventory-body .clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function(event) {
                if (!event.target.closest('a')) {
                    window.location.href = this.dataset.href;
                }
            });
        });

        // Auto scroll to search results if a search has been performed
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search') || (urlParams.has('category') && urlParams.get('category') !== 'all')) {
            const browseSection = document.getElementById('browse');
            if (browseSection) {
                browseSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
</script>