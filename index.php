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

$categories = getCategories($pdo);
$specialProducts = getSpecialPriceProducts($pdo, 4); // 3 produkter på rea

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
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Always 10 for homepage

$searchResults = [];
if (isset($_GET['search']) || (isset($_GET['category']) && $_GET['category'] !== 'all')) {
    // User has performed a search, show search results
    $searchResults = searchProducts([
        'search' => $_GET['search'] ?? '',
        'category' => $_GET['category'] ?? 'all'
    ]);
} else {
    // No search performed, show rare or special items
    $searchResults = searchProducts([
        'special' => true  // Add this parameter to the existing function
    ]);
}

// Page title
$pageTitle = "Karis Antikvariat";

// For the homepage, we always want to show the non-logged in view
// Store the original login state
$originalLoggedIn = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false;
$originalUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$originalUsername = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : null;
$originalUserRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
$originalUserEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// Temporarily unset login information for the header display
unset($_SESSION['logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_username']);
unset($_SESSION['user_role']);
unset($_SESSION['user_email']);

// Include header
include 'templates/header.php';

// Restore the original login state after header is rendered
if ($originalLoggedIn) {
    $_SESSION['logged_in'] = $originalLoggedIn;
    $_SESSION['user_id'] = $originalUserId;
    $_SESSION['user_username'] = $originalUsername;
    $_SESSION['user_role'] = $originalUserRole;
    $_SESSION['user_email'] = $originalUserEmail;
}
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
        <!-- Do not include hidden page/limit fields unless explicitly needed -->
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
            
            <!-- Pagination container for AJAX pagination -->
            <div id="pagination-container" class="mt-4">
                <?php if (!empty($searchResults) && isset($searchResults[0]->pagination) && $searchResults[0]->pagination['totalPages'] > 1): 
                    $pagination = $searchResults[0]->pagination;
                ?>
                <nav aria-label="Search results pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous page button -->
                        <li class="page-item <?= ($pagination['currentPage'] <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link public-pagination-link" href="javascript:void(0);" data-page="<?= $pagination['currentPage'] - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php
                        // Calculate range of page numbers to display
                        $startPage = max(1, $pagination['currentPage'] - 2);
                        $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
                        
                        // Ensure we always show at least 5 pages if available
                        if ($endPage - $startPage + 1 < 5) {
                            if ($startPage == 1) {
                                $endPage = min($pagination['totalPages'], $startPage + 4);
                            } elseif ($endPage == $pagination['totalPages']) {
                                $startPage = max(1, $endPage - 4);
                            }
                        }
                        
                        // Display page numbers
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                        <li class="page-item <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                            <a class="page-link public-pagination-link" href="javascript:void(0);" data-page="<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <!-- Next page button -->
                        <li class="page-item <?= ($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                            <a class="page-link public-pagination-link" href="javascript:void(0);" data-page="<?= $pagination['currentPage'] + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </section>

        <section class="my-5">
            <h2 class="mb-4"><?php echo $strings['on_sale']; ?></h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="featured-items">
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
                                    <img src="<?php echo $productImageToShow; ?>" class="card-img-top h-100 object-fit-cover" alt="<?php echo safeEcho($product->title); ?>">
                                </div>
                                <div class="col-6">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                                        <p class="card-text text-muted flex-grow-1"><?php echo safeEcho($product->author_name); ?></p>
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
                            <img src="<?php echo $productImageToShow; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo safeEcho($product->title); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo safeEcho($product->title); ?></h5>
                                <p class="card-text text-muted"><?php echo safeEcho($product->author_name); ?></p>
                                <p class="text-success fw-bold"><?php echo number_format($product->price, 2, ',', ' ') . ' €'; ?></p>
                            </div>
                        </div>
                       
                        <a href="singleproduct.php?id=<?php echo safeEcho($product->prod_id); ?>" class="stretched-link"></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<?php
// Temporarily unset login info again for the footer
unset($_SESSION['logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_username']);
unset($_SESSION['user_role']);
unset($_SESSION['user_email']);

// Include footer
include 'templates/footer.php';

// Restore the original login state after footer is rendered
if ($originalLoggedIn) {
    $_SESSION['logged_in'] = $originalLoggedIn;
    $_SESSION['user_id'] = $originalUserId;
    $_SESSION['user_username'] = $originalUsername;
    $_SESSION['user_role'] = $originalUserRole;
    $_SESSION['user_email'] = $originalUserEmail;
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/ui-components.js"></script>
<script src="assets/js/ajax.js"></script>
<script src="assets/js/main.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle search form submission with AJAX
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            performPublicSearch(1); // Page 1 for new searches
        });

        // Handle pagination clicks
        $(document).on('click', '.public-pagination-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            $('#current-page').val(page);
            performPublicSearch(page);
        });

        // Perform search
        function performPublicSearch(page) {
            const searchTerm = $('#public-search').val();
            const category = $('#public-category').val();
            const limit = 10; // Fixed limit for public view
            
            // Update the hidden page input
            $('#current-page').val(page);
            
            // Show loading indicator
            $('#public-inventory-body').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
            
            // Perform AJAX request
            $.ajax({
                url: 'admin/search.php',
                data: {
                    ajax: 'public',
                    search: searchTerm,
                    category: category,
                    page: page,
                    limit: limit
                },
                type: 'GET',
                success: function(data) {
                    // Update results
                    $('#public-inventory-body').html(data);
                    
                    // Get pagination
                    $.ajax({
                        url: 'admin/search.php',
                        data: {
                            ajax: 'public_pagination',
                            search: searchTerm,
                            category: category,
                            page: page,
                            limit: limit
                        },
                        type: 'GET',
                        success: function(paginationData) {
                            $('#pagination-container').html(paginationData);
                            
                            // Update URL for bookmarking without page reload
                            const url = new URL(window.location);
                            url.searchParams.set('search', searchTerm);
                            url.searchParams.set('category', category);
                            url.searchParams.set('page', page);
                            url.searchParams.set('limit', limit);
                            window.history.pushState({}, '', url);
                            
                            
                            // Scroll to results
                            document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                },
                error: function() {
                    $('#public-inventory-body').html('<tr><td colspan="7" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>');
                }
            });
            
        }

        // Auto-execute search if there are search parameters in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search') || (urlParams.has('category') && urlParams.get('category') !== 'all')) {
            // Set form values from URL parameters
            $('#public-search').val(urlParams.get('search') || '');
            $('#public-category').val(urlParams.get('category') || 'all');
            $('#current-page').val(urlParams.get('page') || '1');
            
            
            // Scroll to results section
            const browseSection = document.getElementById('browse');
            if (browseSection) {
                browseSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });


</script>