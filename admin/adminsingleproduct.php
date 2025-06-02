<?php
/**
 * Admin Single Product Page - Fixed Version
 * Building from the working debug version with proper product ID handling
 */

require_once dirname(__DIR__) . '/init.php';

// Step 1: IMMEDIATE AJAX handling 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    
    // Clean ALL output buffers immediately
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers immediately
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    ini_set('display_errors', 0);

    // NOTE: init.php is now already included above, so we don't need this line anymore:
    // require_once dirname(__DIR__) . '/init.php';

    // Check CSRF token for AJAX requests
    try {
        checkAuth(2);
        checkCSRFToken();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Authentication or CSRF token validation failed']);
        exit;
    }
    
    // Step 2: Add minimal required includes for database access
    try {
        
        checkAuth(2);
        
        // Step 3: Get product ID from POST data or URL
        $productId = 0;
        
        // First try to get from POST data (hidden field)
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            $productId = (int)$_POST['product_id'];
        }
        // Fallback to GET parameter
        elseif (isset($_GET['id']) && !empty($_GET['id'])) {
            $productId = (int)$_GET['id'];
        }
        
        if ($productId <= 0) {
            throw new Exception('Invalid product ID');
        }
        
        // Step 4: Process form data
        $formData = [
            'title' => $_POST['title'] ?? '',
            'status_id' => $_POST['status_id'] ?? 1,
            'shelf_id' => !empty($_POST['shelf_id']) ? $_POST['shelf_id'] : null,
            'category_id' => $_POST['category_id'] ?? null,
            'price' => !empty($_POST['price']) ? $_POST['price'] : null,
            'condition_id' => !empty($_POST['condition_id']) ? $_POST['condition_id'] : null,
            'notes' => $_POST['notes'] ?? '',
            'internal_notes' => $_POST['internal_notes'] ?? '',
            'year' => !empty($_POST['year']) ? $_POST['year'] : null,
            'publisher' => $_POST['publisher'] ?? '',
            'special_price' => isset($_POST['special_price']),
            'rare' => isset($_POST['rare']),
            'recommended' => isset($_POST['recommended']),
            'language_id' => !empty($_POST['language_id']) ? $_POST['language_id'] : null
        ];
        
        // Step 5: Process authors and genres
        $authorsJson = $_POST['authors_json'] ?? '';
        $authors = [];
        if (!empty($authorsJson)) {
            $authors = json_decode($authorsJson, true);
            if (!is_array($authors)) {
                $authors = [];
            }
        }
        $formData['authors'] = $authors;
        
        $genresJson = $_POST['genres_json'] ?? '';
        $genres = [];
        if (!empty($genresJson)) {
            $genres = json_decode($genresJson, true);
            if (!is_array($genres)) {
                $genres = [];
            }
        }
        $formData['genres'] = $genres;
        
        // Step 6: Validate
        if (empty($formData['title'])) {
            throw new Exception('Titel är obligatorisk');
        }
        if (empty($formData['category_id'])) {
            throw new Exception('Kategori är obligatorisk');
        }
        
        // Step 7: Update product in database
        // Start transaction
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }
        
        try {
            // Update main product data
            $sql = "UPDATE product SET 
                    title = ?, status = ?, shelf_id = ?, category_id = ?, price = ?, 
                    condition_id = ?, notes = ?, internal_notes = ?, year = ?, 
                    publisher = ?, special_price = ?, rare = ?, recommended = ?, language_id = ?
                    WHERE prod_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $formData['title'],
                $formData['status_id'],
                $formData['shelf_id'] ?: null,
                $formData['category_id'],
                $formData['price'] ?: null,
                $formData['condition_id'] ?: null,
                $formData['notes'] ?: '',
                $formData['internal_notes'] ?: '',
                $formData['year'] ?: null,
                $formData['publisher'] ?: '',
                $formData['special_price'] ? 1 : 0,
                $formData['rare'] ? 1 : 0,
                $formData['recommended'] ? 1 : 0,
                $formData['language_id'] ?: null,
                $productId
            ]);
            
            // Update authors
            $stmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = ?");
            $stmt->execute([$productId]);
            
            foreach ($formData['authors'] as $authorName) {
                $authorName = trim($authorName);
                if (empty($authorName)) continue;
                
                // Check if author exists
                $stmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = ?");
                $stmt->execute([$authorName]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $authorId = $existing['author_id'];
                } else {
                    // Create new author
                    $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (?)");
                    $stmt->execute([$authorName]);
                    $authorId = $pdo->lastInsertId();
                }
                
                // Link author to product
                $stmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (?, ?)");
                $stmt->execute([$productId, $authorId]);
            }
            
            // Update genres
            $stmt = $pdo->prepare("DELETE FROM product_genre WHERE product_id = ?");
            $stmt->execute([$productId]);
            
            foreach ($formData['genres'] as $genreId) {
                $genreId = (int)$genreId;
                if ($genreId > 0) {
                    $stmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (?, ?)");
                    $stmt->execute([$productId, $genreId]);
                }
            }
            
            // Step 8: Handle images if any (outside of main transaction to avoid conflicts)
            $imageMessage = '';
            if (isset($_FILES['item_images']) && !empty($_FILES['item_images']['name'][0])) {
                // Commit the main transaction first
                if ($pdo->inTransaction()) {
                    $pdo->commit();
                }
                
                // Handle images separately
                try {
                    // Initialize ImageProcessor without passing transaction
                    global $app_config;
                    $imageProcessor = new ImageProcessor($pdo, $app_config['uploads']);
                    $imageResult = $imageProcessor->uploadProductImages($_FILES['item_images'], $productId);
                    if ($imageResult['success'] && $imageResult['uploaded_count'] > 0) {
                        $imageMessage = ' ' . $imageResult['uploaded_count'] . ' nya bilder uppladdade.';
                    } elseif (!$imageResult['success']) {
                        // Log image error but don't fail the whole update
                        error_log('Image upload failed: ' . ($imageResult['message'] ?? 'Unknown error'));
                        if (!empty($imageResult['errors'])) {
                            error_log('Image errors: ' . implode(', ', $imageResult['errors']));
                        }
                    }
                } catch (Exception $imageEx) {
                    // Log image error but don't fail the whole update
                    error_log('Image upload exception: ' . $imageEx->getMessage());
                }
                
                // Start a new transaction for logging
                $pdo->beginTransaction();
            }
            
            // Step 9: Log the update
            $currentUser = getSessionUser();
            $userId = $currentUser ? $currentUser['user_id'] : 1;
            
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description, product_id) 
                VALUES (?, 'update', ?, ?)
            ");
            $logStmt->execute([$userId, "Uppdaterade produkt: " . $formData['title'], $productId]);
            
            // Commit the transaction
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
            
        } catch (Exception $dbEx) {
            // Rollback on any database error
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $dbEx;
        }
        
        // Step 10: Success response
        echo json_encode([
            'success' => true,
            'message' => 'Produkt uppdaterad framgångsrikt' . $imageMessage
        ]);
        exit;
        
    } catch (Exception $e) {
        // Only rollback if there's an active transaction
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log the error for debugging
        error_log("Product update error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        echo json_encode([
            'success' => false,
            'message' => 'Ett fel inträffade: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Step 11: Regular page loading (non-AJAX)

checkAuth(2);

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) {
    header("Location: " . url('admin.php?tab=search'));
    exit;
}

// Get product data
try {
    $sql = "SELECT
        p.prod_id, p.title, p.status, p.shelf_id, p.category_id, p.price,
        p.condition_id, p.notes, p.internal_notes, p.year, p.publisher,
        p.special_price, p.rare, p.recommended, p.date_added, p.language_id
    FROM product p WHERE p.prod_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$product) {
        header("Location: " . url('admin.php?tab=search'));
        exit;
    }
    
    // Get authors
    $authorSql = "SELECT a.author_id, a.author_name
                  FROM author a
                  JOIN product_author pa ON a.author_id = pa.author_id
                  WHERE pa.product_id = ?";
    
    $authorStmt = $pdo->prepare($authorSql);
    $authorStmt->execute([$productId]);
    $product->authors = $authorStmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get genres
    $genreSql = "SELECT g.genre_id, g.genre_sv_name as genre_name
                FROM genre g
                JOIN product_genre pg ON g.genre_id = pg.genre_id
                WHERE pg.product_id = ?";
    
    $genreStmt = $pdo->prepare($genreSql);
    $genreStmt->execute([$productId]);
    $product->genres = $genreStmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get images
    $imagesSql = "SELECT image_id, image_path, image_uploaded_at
                 FROM image 
                 WHERE prod_id = ?
                 ORDER BY image_uploaded_at ASC";
    $imagesStmt = $pdo->prepare($imagesSql);
    $imagesStmt->execute([$productId]);
    $product->images = $imagesStmt->fetchAll(PDO::FETCH_OBJ);
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Format data for JavaScript
$authorsData = array_map(function($author) {
    return ['id' => $author->author_id, 'name' => $author->author_name];
}, $product->authors);

$genresData = array_map(function($genre) {
    return ['id' => $genre->genre_id, 'name' => $genre->genre_name];
}, $product->genres);

// Simplified render function
function renderSelectOptions($pdo, $table, $idField, $nameField, $selectedValue = '', $locale = 'sv') {
    try {
        $localizedField = $nameField . '_' . $locale . '_name';
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$localizedField'");
        $useLocalized = $stmt->rowCount() > 0;
        
        $displayField = $useLocalized ? $localizedField : $nameField;
        $sql = "SELECT $idField, $displayField as display_name FROM `$table` ORDER BY $displayField";
        
        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($selectedValue == $row[$idField]) ? 'selected' : '';
            echo "<option value=\"{$row[$idField]}\" $selected>" . htmlspecialchars($row['display_name']) . "</option>";
        }
    } catch (PDOException $e) {
        error_log("Error rendering select options: " . $e->getMessage());
    }
}

include dirname(__DIR__) . '/templates/admin_header.php';
?>

<!-- Messages -->
<div id="message-container"></div>

<!-- Main Content Container -->
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Redigera produkt: <?php echo htmlspecialchars($product->title); ?></h1>
        <div>
            <a href="<?php echo url('admin.php?tab=search'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Tillbaka till sökning
            </a>
        </div>
    </div>

    <form id="edit-product-form" method="POST" enctype="multipart/form-data" action="<?php echo url('admin/adminsingleproduct.php?id=' . $productId); ?>">
        <!-- CRITICAL: Hidden product ID field -->
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
        
        <div class="row">
            <!-- Images Column -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bilder</h5>
                        
                        <!-- Existing Images -->
                        <div class="mb-3">
                            <h6>Befintliga bilder:</h6>
                            <div id="existing-images">
                                <?php if (!empty($product->images)): ?>
                                    <?php foreach ($product->images as $image): ?>
                                        <div class="image-container mb-2" data-image-id="<?php echo $image->image_id; ?>">
                                            <img src="<?php echo url($image->image_path); ?>" 
                                                 alt="Produktbild" class="image-preview rounded img-fluid">
                                            <button type="button" class="btn btn-sm btn-danger mt-1" 
                                                    onclick="deleteImage(<?php echo $image->image_id; ?>)"
                                                    title="Ta bort bild">Ta bort</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Inga bilder uppladdade</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- New Images Upload -->
                        <div class="mb-3">
                            <h6>Ladda upp nya bilder:</h6>
                            <div class="item-image-previews mb-3" id="item-image-previews">
                                <?php if (empty($product->images)): ?>
                                    <img src="<?php echo url('assets/images/default_antiqe_image.webp'); ?>" 
                                         alt="Standard bild" class="img-fluid rounded shadow default-preview"
                                         id="default-image-preview">
                                <?php endif; ?>
                            </div>
                            <input class="form-control" type="file" id="item-image-upload" 
                                   name="item_images[]" multiple accept="image/*">
<small class="form-text text-muted">
    Max <?php echo ($app_config['uploads']['max_size'] / (1024 * 1024)); ?>MB per bild, 
    <?php echo strtoupper(implode(', ', $app_config['uploads']['allowed_extensions'])); ?>
</small>
<small class="form-text text-muted mt-1 d-block">
    <i class="fas fa-compress-alt me-1"></i>
    <a href="https://www.birme.net/?target_width=1000&target_height=1000&auto_width=true&auto_focal=false&image_format=webp&quality_webp=70" 
       target="_blank" class="text-decoration-none">
        För att förminska bilder använd Birme
    </a>
</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Fields Column -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Produktinformation</h5>

                        <!-- Category and Status -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="item-category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" id="item-category" name="category_id" required>
                                    <option value="">Välj kategori</option>
                                    <?php renderSelectOptions($pdo, 'category', 'category_id', 'category', $product->category_id); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-status" class="form-label">Status</label>
                                <select class="form-select" id="item-status" name="status_id">
                                    <?php renderSelectOptions($pdo, 'status', 'status_id', 'status', $product->status); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 position-relative mb-2">
                                <label for="item-title" class="form-label">Titel <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="item-title" name="title" 
                                       value="<?php echo htmlspecialchars($product->title); ?>" required>
                            </div>
                        </div>

                        <!-- Authors -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Författare</label>
                                <div class="selected-authors mb-2" 
                                     data-authors='<?php echo json_encode($authorsData); ?>'>
                                    <?php if (!empty($product->authors)): ?>
                                        <?php foreach ($product->authors as $author): ?>
                                            <div class="selected-author badge bg-secondary p-2 me-2 mb-2" 
                                                 data-name="<?php echo htmlspecialchars($author->author_name); ?>">
                                                <?php echo htmlspecialchars($author->author_name); ?> <span class="ms-1">×</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <em class="text-muted">Ingen författare vald</em>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-12 position-relative mb-2">
                                    <div class="input-group input-plus">
                                        <input type="text" class="form-control" id="author-name" 
                                               placeholder="Ange författarens namn" autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary" style="height: 38px;" id="add-author-btn">
                                            <i class="fas fa-plus"></i> Lägg till
                                        </button>
                                    </div>
                                    <div id="suggest-author" class="list-group position-absolute w-100" style="z-index: 1000; top: 100%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Genres -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Genre</label>
                                <div class="selected-genres mb-2" 
                                     data-genres='<?php echo json_encode($genresData); ?>'>
                                    <?php if (!empty($product->genres)): ?>
                                        <?php foreach ($product->genres as $genre): ?>
                                            <div class="selected-genre badge bg-secondary p-2 me-2 mb-2" 
                                                 data-genre-id="<?php echo $genre->genre_id; ?>">
                                                <?php echo htmlspecialchars($genre->genre_name); ?> <span class="ms-1">×</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <em class="text-muted">Ingen genre vald</em>
                                    <?php endif; ?>
                                </div>
                                <div class="input-group input-plus">
                                    <select class="form-select" id="item-genre">
                                        <option value="">Välj genre</option>
                                        <?php renderSelectOptions($pdo, 'genre', 'genre_id', 'genre'); ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" style="height: 38px;" id="add-genre-btn">
                                        <i class="fas fa-plus"></i> Lägg till
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Price, Condition, Shelf -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-price" class="form-label">Pris (€)</label>
                                <input type="number" step="0.01" class="form-control" id="item-price" name="price" 
                                       value="<?php echo $product->price; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="item-condition" class="form-label">Skick</label>
                                <select class="form-select" id="item-condition" name="condition_id">
                                    <option value="">Välj skick</option>
                                    <?php renderSelectOptions($pdo, 'condition', 'condition_id', 'condition', $product->condition_id); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-shelf" class="form-label">Hylla</label>
                                <select class="form-select" id="item-shelf" name="shelf_id">
                                    <option value="">Välj hylla</option>
                                    <?php renderSelectOptions($pdo, 'shelf', 'shelf_id', 'shelf', $product->shelf_id); ?>
                                </select>
                            </div>
                        </div>

                        <!-- Language, Year, Publisher -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-language" class="form-label">Språk</label>
                                <select class="form-select" id="item-language" name="language_id">
                                    <option value="">Välj språk</option>
                                    <?php renderSelectOptions($pdo, 'language', 'language_id', 'language', $product->language_id); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-year" class="form-label">År</label>
                                <input type="number" class="form-control" id="item-year" name="year" 
                                       value="<?php echo $product->year; ?>" min="1400" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col-md-4 position-relative">
                                <label for="item-publisher" class="form-label">Förlag</label>
                                <input type="text" class="form-control" id="item-publisher" name="publisher" 
                                       value="<?php echo htmlspecialchars($product->publisher); ?>" autocomplete="off">
                                <div id="suggest-publisher" class="list-group position-absolute w-100" style="z-index: 1000; top: 100%;"></div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="item-notes" class="form-label">Produktbeskrivning (synlig för kunder)</label>
                            <textarea class="form-control" id="item-notes" name="notes" rows="3"><?php echo htmlspecialchars($product->notes); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-internal-notes" class="form-label">Intern beskrivning (endast för personal)</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes" rows="2"><?php echo htmlspecialchars($product->internal_notes); ?></textarea>
                        </div>

                        <!-- Checkboxes -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-special-price" 
                                           name="special_price" value="1" <?php echo $product->special_price ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="item-special-price">Special Pris</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-rare" 
                                           name="rare" value="1" <?php echo $product->rare ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="item-rare">Raritet</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-recommended" 
                                           name="recommended" value="1" <?php echo $product->recommended ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="item-recommended">Rekommenderad</label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
<div class="d-flex justify-content-between">
    
<button type="button" class="btn btn-outline-danger" onclick="deleteSingleProduct(<?php echo $productId; ?>)">
            <i class="fas fa-trash-alt me-1"></i> Radera
        </button>      

<button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">Återställ</button>
        
    
    <button type="submit" class="btn btn-primary">
        <span class="submit-text">
            <i class="fas fa-save"></i> Spara ändringar
        </span>
        <span class="submit-spinner d-none">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Sparar...
        </span>
    </button>
</div>

                        <!-- Hidden fields -->
                        <input type="hidden" id="authors-json" name="authors_json" value="">
                        <input type="hidden" id="genres-json" name="genres_json" value="">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.selected-author, .selected-genre {
    cursor: pointer;
    transition: all 0.2s;
}
.selected-author:hover, .selected-genre:hover {
    background-color: #dc3545 !important;
}
.image-preview {
    max-width: 150px;
    max-height: 150px;
    object-fit: cover;
}
.image-container {
    position: relative;
    display: inline-block;
    margin: 5px;
}
</style>

<script>
// Initialize existing data
document.addEventListener('DOMContentLoaded', function() {
    // Set existing authors
    const authorsData = <?php echo json_encode($authorsData); ?>;
    if (authorsData.length > 0) {
        window.productAuthors = authorsData.map(author => ({
            id: author.id,
            name: author.name
        }));
        if (typeof window.addProductHandlers !== 'undefined') {
            window.addProductHandlers.updateAuthorsJson();
        }
    }
    
    // Set existing genres
    const genresData = <?php echo json_encode($genresData); ?>;
    if (genresData.length > 0) {
        window.productGenres = genresData.map(genre => ({
            id: genre.id,
            name: genre.name
        }));
        if (typeof window.addProductHandlers !== 'undefined') {
            window.addProductHandlers.updateGenresJson();
        }
    }
});

// Delete image function
function deleteImage(imageId) {
    if (!confirm('Är du säker på att du vill ta bort denna bild?')) {
        return;
    }
    
    fetch('<?php echo url("admin/delete_image.php"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ image_id: imageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-image-id="${imageId}"]`).remove();
        } else {
            showMessage('Fel vid borttagning av bild: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade vid borttagning av bild', 'danger');
    });
}

// Reset form function
function resetForm() {
    if (!confirm('Är du säker på att du vill återställa alla ändringar?')) {
        return;
    }
    
    document.getElementById('edit-product-form').reset();
    
    const authorsData = <?php echo json_encode($authorsData); ?>;
    const genresData = <?php echo json_encode($genresData); ?>;
    
    window.productAuthors = [...authorsData];
    window.productGenres = [...genresData];
    
    if (typeof window.addProductHandlers !== 'undefined') {
        window.addProductHandlers.updateAuthorsJson();
        window.addProductHandlers.updateGenresJson();
    }
}

// Show message function
function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('message-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    messageContainer.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Delete single product function (different from data-operations.js)
function deleteSingleProduct(productId) {
    if (!confirm('Är du säker på att du vill ta bort denna produkt? Denna åtgärd kan inte ångras!')) {
        return;
    }
    
    // Show loading state
    const deleteBtn = event.target;
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Raderar...';
    deleteBtn.disabled = true;
    
    fetch('list_ajax_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            action: 'batch_action',
            batch_action: 'delete',
            product_ids: JSON.stringify([productId])
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage('Produkt raderad framgångsrikt', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo url("admin.php?tab=search"); ?>';
            }, 1500);
        } else {
            showMessage('Fel vid radering: ' + data.message, 'danger');
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade vid radering', 'danger');
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    });
}
     
</script>

<script src="<?php echo url('assets/js/addproduct-handlers.js'); ?>"></script>

<?php
include dirname(__DIR__) . '/templates/admin_footer.php';
?>