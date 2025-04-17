<?php
/**
 * Single Product Page
 * 
 * Contains:
 * - Detailed product information
 * - Related products
 * 
 * Functions:
 * - renderSingleProduct()
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/ui.php';

// Get product ID from URL parameter
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if product ID is valid
if ($productId <= 0) {
    // Redirect to home page if invalid product ID
    header("Location: index.php");
    exit;
}

// Load language settings if multilingual support is enabled
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Page title (will be updated with product title)
$pageTitle = "Produkt - Karis Antikvariat";

// Include header
include 'templates/header.php';

// Get product data
// Note: This is a placeholder. Actual implementation would fetch data from database
// $product = getProductById($productId);
?>

<!-- Main Content Container -->
<div class="container my-5">
    <div class="row">
        <!-- Item Image -->
        <div class="col-md-6 mb-4">
            <div class="item-image-container">
                <img src="" alt="Objektbild" class="img-fluid rounded shadow" id="item-image">
            </div>
        </div>
        <!-- Item Details -->
        <div class="col-md-6 item-details">
            <div>
                <h1 class="mb-2" id="item-title">Titel på objekt</h1>
                <h4 class="text-muted mb-3" id="item-author">Författare</h4>
            </div>
            <h5>Produktinformation</h5>
            <ul class="list-unstyled">
                <li><strong>Kategori:</strong> <span id="item-category">-</span></li>
                <li><strong>Genre:</strong> <span id="item-genre">-</span></li>
                <li><strong>Skick:</strong> <span id="item-condition">-</span></li>
                <li><strong>Hyllplats:</strong> <span id="item-shelf">-</span></li>
                <li><strong>Tillagd datum:</strong> <span id="item-date">-</span></li>
            </ul>
            <div class="item-description">
                <h5>Beskrivning</h5>
                <p id="item-notes">Inga ytterligare anteckningar.</p>
            </div>
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="text-end">
                    <div class="price-container">
                        <span class="badge bg-success fs-6" id="item-status">Tillgänglig</span>
                        <h3 class="text-success mb-3" id="item-price">€0.00</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Items Section -->
        <div class="col-12">
            <section class="my-5">
                <h2 class="mb-4">Relaterade objekt</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="related-items">
                    <!-- Related items will be dynamically loaded -->
                    <?php
                    // Placeholder related items
                    // Will be replaced with actual database query
                    for ($i = 1; $i <= 4; $i++) {
                    ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="row g-0 h-100">
                                <div class="col-6">
                                    <img src="assets/images/src-book.webp" class="card-img-top h-100 object-fit-cover" alt="Related book">
                                </div>
                                <div class="col-6">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title">Relaterat Objekt <?php echo $i; ?></h5>
                                        <p class="card-text text-muted flex-grow-1">Författare</p>
                                        <p class="text-success fw-bold mb-2">€19.95</p>
                                        <a href="singleproduct.php?id=<?php echo $i; ?>" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>