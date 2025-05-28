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

// Include initialization file (replaces multiple require statements)
require_once 'init.php';

// Get product ID from URL parameter
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if product ID is valid
if ($productId <= 0) {
    // Redirect to home page if invalid product ID
    header("Location: " . url('index.php'));
    exit;
}

/**
 * Get all product images
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

function getProductById($productId) {
    global $pdo, $language;
    
    // Determine which field to use based on language
    $categoryNameField = ($language === 'fi') ? 'cat.category_fi_name' : 'cat.category_sv_name';
    $shelfNameField = ($language === 'fi') ? 'sh.shelf_fi_name' : 'sh.shelf_sv_name';
    $statusNameField = ($language === 'fi') ? 's.status_fi_name' : 's.status_sv_name';
    $conditionNameField = ($language === 'fi') ? 'con.condition_fi_name' : 'con.condition_sv_name' ;
    $languageNameField = ($language === 'fi') ? 'lang.language_fi_name' : 'lang.language_sv_name';
    
    try {
        $sql = "SELECT
        p.prod_id,
        p.title,
        p.status,
        p.shelf_id,
        p.category_id,
        p.price,
        p.condition_id,
        p.notes,
        p.internal_notes,
        p.year,
        p.publisher,
        p.special_price,
        p.rare,
        p.recommended,
        p.date_added,
        p.language_id,
        {$statusNameField} as status_name,
        s.status_id,
        {$categoryNameField} as category_name,
        IFNULL({$shelfNameField}, '') as shelf_name,
        IFNULL({$conditionNameField}, '') as condition_name,
        IFNULL(con.condition_code, '') as condition_code,
        IFNULL({$languageNameField}, '') as language_name
    FROM
        product p
    JOIN category cat ON p.category_id = cat.category_id
    JOIN `status` s ON p.status = s.status_id
    LEFT JOIN shelf sh ON p.shelf_id = sh.shelf_id
    LEFT JOIN `condition` con ON p.condition_id = con.condition_id
    LEFT JOIN `language` lang ON p.language_id = lang.language_id
    WHERE
        p.prod_id = :productId";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($product) {
            // Get authors for this product
            $authorSql = "SELECT a.author_id, a.author_name
                         FROM author a
                         JOIN product_author pa ON a.author_id = pa.author_id
                         WHERE pa.product_id = :productId";
            
            $authorStmt = $pdo->prepare($authorSql);
            $authorStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $authorStmt->execute();
            
            $authors = $authorStmt->fetchAll(PDO::FETCH_OBJ);
            $product->authors = $authors;
            
            // Create formatted author list
            $authorNames = array_map(function($author) {
                return $author->author_name;
            }, $authors);
            
            $product->author_names = !empty($authorNames) ? implode(', ', $authorNames) : '';
            
            // Get genres for this product
            $genreSql = "SELECT g.genre_id, " . 
                       ($language === 'fi' ? 'g.genre_fi_name' : 'g.genre_sv_name') . " as genre_name
                       FROM genre g
                       JOIN product_genre pg ON g.genre_id = pg.genre_id
                       WHERE pg.product_id = :productId";
            
            $genreStmt = $pdo->prepare($genreSql);
            $genreStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $genreStmt->execute();
            
            $genres = $genreStmt->fetchAll(PDO::FETCH_OBJ);
            $product->genres = $genres;
            
            // Create genre arrays for use in templates and related products
            $product->genre_ids_array = array_map(function($genre) {
                return $genre->genre_id;
            }, $genres);
            
            $product->genre_names_array = array_map(function($genre) {
                return $genre->genre_name;
            }, $genres);
        }
        
        return $product;
    } catch (PDOException $e) {
        error_log("Database error in getProductById: " . $e->getMessage());
        error_log("SQL query: " . $sql);
        return null;
    }
}

/**
 * Gets related products based on category and genre
 * 
 * @param int $productId Current product ID 
 * @param int $categoryId Category ID
 * @param array $genreIds Array of genre IDs
 * @param int $limit Maximum number of related products
 * @return array Related products
 */
function getRelatedProducts($productId, $categoryId, $genreIds, $limit = 4) {
    global $pdo, $language;
    
    try {
        // If no genre IDs, use an empty array
        if (empty($genreIds)) {
            $genreIds = [0]; // Use a dummy value to prevent SQL error
        }
        
        // Convert genre IDs array to comma-separated string
        $genreIdList = implode(',', array_map('intval', $genreIds));
        
        // Get language-specific field names
        $categoryNameField = ($language === 'fi') ? 'c.category_fi_name' : 'c.category_sv_name';
        
        $sql = "SELECT DISTINCT
                    p.prod_id,
                    p.title,
                    p.price,
                    p.special_price,
                    p.rare,
                    p.recommended,
                    {$categoryNameField} as category_name,
                    (SELECT GROUP_CONCAT(a.author_name SEPARATOR ', ') 
                     FROM product_author pa 
                     JOIN author a ON pa.author_id = a.author_id 
                     WHERE pa.product_id = p.prod_id) as author_names
                FROM
                    product p
                JOIN
                    category c ON p.category_id = c.category_id
                LEFT JOIN
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
        error_log("Database error in getRelatedProducts: " . $e->getMessage());
        return [];
    }
}

// Get the product
$product = getProductById($productId);

if (!$product) {
    // Redirect to home page if product not found
    header("Location: " . url('index.php'));
    exit;
}

// Fetch images for the product
$productImages = getProductImages($product->prod_id, $pdo);

// Get related products
$relatedProducts = [];
if (!empty($product->genre_ids_array)) {
    $relatedProducts = getRelatedProducts($productId, $product->category_id, $product->genre_ids_array);
}

// Format the price with two decimal places
$formattedPrice = $formatter->formatPrice($product->price);

// Format the date
$formattedDate = $formatter->formatDate($product->date_added);

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
<div class="container my-5 pb-5 flex-grow-1">
    <div class="row">
        
        <!-- Three-column layout: Image, Details, Price/Status -->
        <div class="row mb-5">
            <!-- Column 1: Item Image -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="item-image-container">
                    <?php if (!empty($productImages)): ?>
                        <?php if (count($productImages) === 1): ?>
                            <!-- Single image -->
                            <img src="<?php echo safeEcho($productImages[0]->image_path); ?>" 
                                 alt="<?php echo safeEcho($product->title); ?>" 
                                 class="img-fluid rounded shadow" id="item-image">
                        <?php else: ?>
                            <!-- Carousel for multiple images -->
                            <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <?php foreach ($productImages as $index => $image): ?>
                                        <button type="button" data-bs-target="#productImageCarousel" 
                                                data-bs-slide-to="<?php echo $index; ?>" 
                                                <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?>
                                                aria-label="Bild <?php echo $index + 1; ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="carousel-inner">
                                    <?php foreach ($productImages as $index => $image): ?>
                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                            <img src="<?php echo safeEcho($image->image_path); ?>" 
                                                 class="d-block w-100 rounded shadow" 
                                                 alt="<?php echo safeEcho($product->title); ?> - Bild <?php echo $index + 1; ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Föregående</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Nästa</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Default image -->
                        <img src="<?php echo asset('images', 'default_antiqe_image.webp'); ?>" 
                             alt="<?php echo safeEcho($product->title); ?>" 
                             class="img-fluid rounded shadow" id="item-image">
                    <?php endif; ?>
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
                    
                    <?php if (!empty($product->genre_names_array)): ?>
                    <li><strong><?php echo $strings['genre']; ?>:</strong> <span id="item-genre"><?php echo safeEcho(implode(', ', $product->genre_names_array)); ?></span></li>
                    <?php endif; ?>
                    
                    <li><strong><?php echo $strings['condition']; ?>:</strong> <span id="item-condition"><?php echo safeEcho($product->condition_name); ?></span></li>
                    <li><strong><?php echo $strings['shelf']; ?>:</strong> <span id="item-shelf"><?php echo safeEcho($product->shelf_name); ?></span></li>
                    
                    <?php if (!empty($product->language_name)): ?>
                    <li><strong><?php echo $strings['language']; ?>:</strong> <span id="item-language"><?php echo safeEcho($product->language_name); ?></span></li>
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
                    
                    <?php if ($product->recommended): ?>
                        <span class="badge bg-info fs-6 mt-2 d-block"><?php echo $strings['recommended'] ?? 'Rekommenderad'; ?></span>
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
                                // Get first image for related product
                                $relatedImages = getProductImages($relatedProduct->prod_id, $pdo);
                                $relatedImagePath = !empty($relatedImages) ? $relatedImages[0]->image_path : asset('images', 'default_antiqe_image.webp');
                                ?>
                                <img src="<?php echo safeEcho($relatedImagePath); ?>" class="card-img-top h-100 object-fit-cover" alt="<?php echo safeEcho($relatedProduct->title); ?>">
                            </div>
                            <div class="col-6">
                                <div class="card-body d-flex flex-column h-100">
                                    <h5 class="card-title"><?php echo safeEcho($relatedProduct->title); ?></h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo safeEcho($relatedProduct->author_names); ?>
                                    </p>
                                    <p class="text-success fw-bold mb-2"><?php echo $formatter->formatPrice($relatedProduct->price); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- For laptop/desktop: vertical layout -->
                    <div class="d-none d-md-block">
                        <?php
                        // Get first image for related product
                        $relatedImages = getProductImages($relatedProduct->prod_id, $pdo);
                        $relatedImagePath = !empty($relatedImages) ? $relatedImages[0]->image_path : asset('images', 'default_antiqe_image.webp');
                        ?>
                        <img src="<?php echo safeEcho($relatedImagePath); ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo safeEcho($relatedProduct->title); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo safeEcho($relatedProduct->title); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo safeEcho($relatedProduct->author_names); ?>
                            </p>
                            <p class="text-success fw-bold"><?php echo $formatter->formatPrice($relatedProduct->price); ?></p>
                        </div>
                    </div>
                    
                    <a href="<?php echo url('singleproduct.php', ['id' => $relatedProduct->prod_id]); ?>" class="stretched-link"></a>
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