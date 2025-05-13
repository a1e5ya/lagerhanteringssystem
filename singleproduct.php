<?php
/**
 * Single Product Page
 * 
 * Contains:
 * - Detailed product information
 * - Related products
 * 
 * Functions:
 * - getProductById()
 * - getRelatedProducts()
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/ui.php';

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

// Get product ID from URL parameter
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if product ID is valid
if ($productId <= 0) {
    // Redirect to home page if invalid product ID
    header("Location: index.php");
    exit;
}

// Function to get product by ID
function getProductById($productId) {
    global $pdo;
    
    try {
        $sql = "SELECT
                p.prod_id,
                p.title,
                p.price,
                p.notes,
                p.year,
                p.publisher,
                p.language,
                p.special_price,
                p.rare,
                p.date_added,
                s.status_name,
                s.status_id,
                cat.category_name,
                cat.category_id,
                sh.shelf_name,
                sh.shelf_id,
                con.condition_name,
                con.condition_code,
                GROUP_CONCAT(DISTINCT a.first_name SEPARATOR '|') AS first_names,
                GROUP_CONCAT(DISTINCT a.last_name SEPARATOR '|') AS last_names,
                GROUP_CONCAT(DISTINCT a.author_id SEPARATOR '|') AS author_ids,
                GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR '|') AS genre_names,
                GROUP_CONCAT(DISTINCT g.genre_id SEPARATOR '|') AS genre_ids
            FROM
                product p
            LEFT JOIN
                product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN
                author a ON pa.author_id = a.author_id
            LEFT JOIN
                product_genre pg ON p.prod_id = pg.product_id
            LEFT JOIN
                genre g ON pg.genre_id = g.genre_id
            JOIN
                category cat ON p.category_id = cat.category_id
            JOIN
                shelf sh ON p.shelf_id = sh.shelf_id
            JOIN
                `condition` con ON p.condition_id = con.condition_id
            JOIN
                `status` s ON p.status = s.status_id
            WHERE
                p.prod_id = :productId
            GROUP BY
                p.prod_id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($product) {
            // Process concatenated fields
            $product->first_names_array = !empty($product->first_names) ? explode('|', $product->first_names) : [];
            $product->last_names_array = !empty($product->last_names) ? explode('|', $product->last_names) : [];
            $product->author_ids_array = !empty($product->author_ids) ? explode('|', $product->author_ids) : [];
            $product->genre_names_array = !empty($product->genre_names) ? explode('|', $product->genre_names) : [];
            $product->genre_ids_array = !empty($product->genre_ids) ? explode('|', $product->genre_ids) : [];
            
            // Create full author names
            $authorNames = [];
            for ($i = 0; $i < count($product->first_names_array); $i++) {
                $firstName = $product->first_names_array[$i] ?? '';
                $lastName = $product->last_names_array[$i] ?? '';
                if (!empty($firstName) || !empty($lastName)) {
                    $authorNames[] = trim($firstName . ' ' . $lastName);
                }
            }
            $product->author_names = !empty($authorNames) ? implode(', ', $authorNames) : '';
        }
        
        return $product;
    } catch (PDOException $e) {
        error_log("Databasfel vid hämtning av produkt: " . $e->getMessage());
        return null;
    }
}

// Function to get related products based on category and genre
function getRelatedProducts($productId, $categoryId, $genreIds, $limit = 4) {
    global $pdo;
    
    try {
        // Convert genre IDs array to a comma-separated string for SQL IN clause
        $genreIdList = implode(',', array_map('intval', $genreIds));
        
        $sql = "SELECT DISTINCT
                    p.prod_id,
                    p.title,
                    p.price,
                    GROUP_CONCAT(DISTINCT a.first_name SEPARATOR ' ') AS first_names,
                    GROUP_CONCAT(DISTINCT a.last_name SEPARATOR ' ') AS last_names
                FROM
                    product p
                LEFT JOIN
                    product_author pa ON p.prod_id = pa.product_id
                LEFT JOIN
                    author a ON pa.author_id = a.author_id
                JOIN
                    product_genre pg ON p.prod_id = pg.product_id
                WHERE
                    p.prod_id != :productId
                    AND p.status = 1 -- Only available products
                    AND (
                        p.category_id = :categoryId
                        OR pg.genre_id IN ($genreIdList)
                    )
                GROUP BY
                    p.prod_id
                LIMIT :limit";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Databasfel vid hämtning av relaterade produkter: " . $e->getMessage());
        return [];
    }
}

// Get product data
$product = getProductById($productId);

// If product not found, redirect to home page
if (!$product) {
    header("Location: index.php");
    exit;
}

// Get related products
$relatedProducts = [];
if (!empty($product->genre_ids_array)) {
    $relatedProducts = getRelatedProducts($productId, $product->category_id, $product->genre_ids_array);
}

// Format the price with two decimal places
$formattedPrice = number_format($product->price, 2, ',', ' ') . ' €';

// Format the date
$formattedDate = date('d.m.Y', strtotime($product->date_added));

// Determine status class
$statusClass = '';
switch ($product->status_id) {
    case 1: // Available
        $statusClass = 'bg-success';
        break;
    case 2: // Sold
        $statusClass = 'bg-secondary';
        break;
    case 3: // Reserved
        $statusClass = 'bg-warning';
        break;
    case 4: // Damaged
        $statusClass = 'bg-danger';
        break;
}

// Update page title with product name
$pageTitle = safeEcho($product->title) . " - Karis Antikvariat";

// Include header
include 'templates/header.php';
?>

<!-- Main Content Container -->
<div class="container my-5 pb-5">
    <div class="row">

        
        <!-- Three-column layout: Image, Details, Price/Status -->
        <div class="row mb-5">
            <!-- Column 1: Item Image -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="item-image-container">
                    <?php
                    // Check if product image exists, otherwise use default based on category
                    $imagePath = 'uploads/products/' . $product->prod_id . '.jpg';
                    $defaultImage = 'assets/images/src-book.webp'; // Default image
                    
                    if ($product->category_id == 5) { // CD
                        $defaultImage = 'assets/images/src-cd.webp';
                    } elseif ($product->category_id == 6) { // Vinyl
                        $defaultImage = 'assets/images/src-vinyl.webp';
                    } elseif ($product->category_id == 7) { // DVD
                        $defaultImage = 'assets/images/src-dvd.webp';
                    } elseif ($product->category_id == 8) { // Comics/Magazines
                        $defaultImage = 'assets/images/src-magazine.webp';
                    }
                    
                    $imageToShow = file_exists($imagePath) ? $imagePath : $defaultImage;
                    ?>
                    <img src="<?php echo $imageToShow; ?>" alt="<?php echo safeEcho($product->title); ?>" class="img-fluid rounded shadow" id="item-image">
                </div>
            </div>
            
            <!-- Column 2: Item Details -->
            <div class="col-md-4 item-details">
                <div>
                    <h1 class="mb-2" id="item-title"><?php echo safeEcho($product->title); ?></h1>
                    <h4 class="text-muted mb-3" id="item-author"><?php echo safeEcho($product->author_names); ?></h4>
                </div>
                
                <ul class="list-unstyled">
                    <li><strong><?php echo $strings['category']; ?>:</strong> <span id="item-category"><?php echo safeEcho($product->category_name); ?></span></li>
                    <li><strong><?php echo $strings['genre']; ?>:</strong> <span id="item-genre"><?php echo safeEcho(implode(', ', $product->genre_names_array)); ?></span></li>
                    <li><strong><?php echo $strings['condition']; ?>:</strong> <span id="item-condition"><?php echo safeEcho($product->condition_name); ?></span></li>
                    <li><strong><?php echo $strings['shelf']; ?>:</strong> <span id="item-shelf"><?php echo safeEcho($product->shelf_name); ?></span></li>
                    
                    <?php if (!empty($product->language)): ?>
                    <li><strong><?php echo $strings['language']; ?>:</strong> <span id="item-language"><?php echo safeEcho($product->language); ?></span></li>
                    <?php endif; ?>
                    
                    <?php if (!empty($product->year)): ?>
                    <li><strong><?php echo $strings['year']; ?>:</strong> <span id="item-year"><?php echo safeEcho($product->year); ?></span></li>
                    <?php endif; ?>
                    
                    <?php if (!empty($product->publisher)): ?>
                    <li><strong><?php echo $strings['publisher']; ?>:</strong> <span id="item-publisher"><?php echo safeEcho($product->publisher); ?></span></li>
                    <?php endif; ?>
                    
                    <li><strong><?php echo $strings['date_added']; ?>:</strong> <span id="item-date"><?php echo $formattedDate; ?></span></li>
                </ul>
                
                <?php if (!empty($product->notes)): ?>
                <div class="item-description">
                    <h5><?php echo $strings['description']; ?></h5>
                    <p id="item-notes"><?php echo nl2br(safeEcho($product->notes)); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Column 3: Price, Status and Contact Info -->
            <div class="col-md-4 item-price-status">

                <div class="price-container">
                    <span class="badge <?php echo $statusClass; ?> fs-6" id="item-status"><?php echo safeEcho($product->status_name); ?></span>
                    
                    <?php if ($product->special_price): ?>
                        <span class="badge bg-danger fs-6 ms-2"><?php echo $strings['special_price']; ?></span>
                    <?php endif; ?>
                    
                    <?php if ($product->rare): ?>
                        <span class="badge bg-warning text-dark fs-6 mt-2 d-block"><?php echo $strings['rare_item']; ?></span>
                    <?php endif; ?>
                    
                    <h3 class="text-success mt-3" id="item-price"><?php echo $formattedPrice; ?></h3>
                </div>
                
                <!-- Contact Info -->
                <div class="mt-4">
                    <h5><?php echo $strings['interested_product']; ?></h5>
                    
                    <?php if ($product->status_id == 1): // If available ?>
                    <p><?php echo $strings['contact_to_reserve']; ?></p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>+358 40 871 9706</li>
                        <li><i class="fas fa-envelope me-2"></i>karisantikvariat@gmail.com</li>
                    </ul>
                    <?php elseif ($product->status_id == 2): // If sold ?>
                    <p><?php echo $strings['product_sold']; ?></p>
                    <?php elseif ($product->status_id == 3): // If reserved ?>
                    <p><?php echo $strings['product_reserved']; ?></p>
                    <?php else: // Other status ?>
                    <p><?php echo $strings['product_unavailable']; ?></p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        
<!-- Related Items Section -->
<?php if (!empty($relatedProducts)): ?>
<div class="col-12">
    <section class="my-5">
        <h2 class="mb-4"><?php echo $strings['related_items']; ?></h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="related-items">
            <?php foreach ($relatedProducts as $relatedProduct): ?>
            <div class="col">
                <div class="card h-100">
                    <!-- For mobile: horizontal layout -->
                    <div class="d-block d-md-none">
                        <div class="row g-0 h-100">
                            <div class="col-6">
                                <?php
                                // Check if related product image exists
                                $relatedImagePath = 'uploads/products/' . $relatedProduct->prod_id . '.jpg';
                                $relatedDefaultImage = 'assets/images/src-book.webp'; // Default related image
                                $relatedImageToShow = file_exists($relatedImagePath) ? $relatedImagePath : $relatedDefaultImage;
                                ?>
                                <img src="<?php echo $relatedImageToShow; ?>" class="card-img-top h-100 object-fit-cover" alt="<?php echo safeEcho($relatedProduct->title); ?>">
                            </div>
                            <div class="col-6">
                                <div class="card-body d-flex flex-column h-100">
                                    <h5 class="card-title"><?php echo safeEcho($relatedProduct->title); ?></h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo safeEcho(trim($relatedProduct->first_names . ' ' . $relatedProduct->last_names)); ?>
                                    </p>
                                    <p class="text-success fw-bold mb-2"><?php echo number_format($relatedProduct->price, 2, ',', ' ') . ' €'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- For laptop/desktop: vertical layout -->
                    <div class="d-none d-md-block">
                        <?php
                        // Check if related product image exists
                        $relatedImagePath = 'uploads/products/' . $relatedProduct->prod_id . '.jpg';
                        $relatedDefaultImage = 'assets/images/src-book.webp'; // Default related image
                        $relatedImageToShow = file_exists($relatedImagePath) ? $relatedImagePath : $relatedDefaultImage;
                        ?>
                        <img src="<?php echo $relatedImageToShow; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo safeEcho($relatedProduct->title); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo safeEcho($relatedProduct->title); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo safeEcho(trim($relatedProduct->first_names . ' ' . $relatedProduct->last_names)); ?>
                            </p>
                            <p class="text-success fw-bold"><?php echo number_format($relatedProduct->price, 2, ',', ' ') . ' €'; ?></p>
                        </div>
                    </div>
                    
                    <a href="singleproduct.php?id=<?php echo safeEcho($relatedProduct->prod_id); ?>" class="stretched-link"></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php endif; ?>
    </div>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript functionality needed for the single product page
        console.log('Single product page loaded');
    });
</script>