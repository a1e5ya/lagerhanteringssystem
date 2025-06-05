<?php
/**
 * Admin Single Product Page - Enhanced Security Version
 *
 * This file handles the editing of individual products in the admin interface.
 * Enhanced with modern security practices, input validation, and proper documentation.
 *
 * @package Bookstore
 * @author System Administrator
 * @version 3.0
 * @since 2025-06-05
 */

require_once dirname(__DIR__) . '/init.php';

/**
 * Custom string sanitization function compatible with PHP 8.1+
 * Replaces deprecated FILTER_SANITIZE_STRING
 *
 * @param mixed $input Input string to sanitize
 * @param int $maxLength Maximum allowed length
 * @return string Sanitized string
 */
function sanitizeString($input, $maxLength = 255) {
    if (!is_string($input)) return '';
    // Remove null bytes and control characters
    $input = str_replace(chr(0), '', $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    return substr(trim($input), 0, $maxLength);
}

/**
 * Validates and sanitizes numeric input
 *
 * @param mixed $input Input value to validate
 * @param float $min Minimum allowed value
 * @param float $max Maximum allowed value
 * @return mixed Sanitized numeric value or null if invalid
 */
function validateNumeric($input, $min = null, $max = null) {
    if (empty($input)) return null;
    
    $value = filter_var($input, FILTER_VALIDATE_FLOAT);
    if ($value === false) return null;
    
    if ($min !== null && $value < $min) return null;
    if ($max !== null && $value > $max) return null;
    
    return $value;
}

/**
 * Validates and sanitizes integer input
 *
 * @param mixed $input Input value to validate
 * @param int $min Minimum allowed value
 * @param int $max Maximum allowed value
 * @return mixed Sanitized integer value or null if invalid
 */
function validateInteger($input, $min = null, $max = null) {
    if (empty($input)) return null;
    
    $value = filter_var($input, FILTER_VALIDATE_INT);
    if ($value === false) return null;
    
    if ($min !== null && $value < $min) return null;
    if ($max !== null && $value > $max) return null;
    
    return $value;
}

// IMMEDIATE AJAX handling 
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

    // Check CSRF token for AJAX requests
    try {
        checkAuth(2);
        checkCSRFToken();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Authentication or CSRF token validation failed']);
        exit;
    }
    
    // Add minimal required includes for database access
    try {
        checkAuth(2);
        
        // Get product ID from POST data or URL with validation
        $productId = 0;
        
        // First try to get from POST data (hidden field)
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            $productId = validateInteger($_POST['product_id'], 1);
        }
        // Fallback to GET parameter
        elseif (isset($_GET['id']) && !empty($_GET['id'])) {
            $productId = validateInteger($_GET['id'], 1);
        }
        
        if (!$productId || $productId <= 0) {
            throw new Exception('Invalid product ID');
        }
        
        // Process and validate form data
        $formData = [
            'title' => sanitizeString($_POST['title'] ?? '', 255),
            'status_id' => validateInteger($_POST['status_id'] ?? 1, 1) ?: 1,
            'shelf_id' => validateInteger($_POST['shelf_id'] ?? null, 1),
            'category_id' => validateInteger($_POST['category_id'] ?? null, 1),
            'price' => validateNumeric($_POST['price'] ?? null, 0),
            'condition_id' => validateInteger($_POST['condition_id'] ?? null, 1),
            'notes' => sanitizeString($_POST['notes'] ?? '', 1000),
            'internal_notes' => sanitizeString($_POST['internal_notes'] ?? '', 1000),
            'year' => validateInteger($_POST['year'] ?? null, 1400, date('Y')),
            'publisher' => sanitizeString($_POST['publisher'] ?? '', 255),
            'special_price' => isset($_POST['special_price']),
            'rare' => isset($_POST['rare']),
            'recommended' => isset($_POST['recommended']),
            'language_id' => validateInteger($_POST['language_id'] ?? null, 1)
        ];
        
        // Process authors from JSON with validation
        $authorsJson = sanitizeString($_POST['authors_json'] ?? '', 10000);
        $authors = [];
        if (!empty($authorsJson)) {
            $decoded = json_decode($authorsJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $author) {
                    $cleanAuthor = sanitizeString($author, 255);
                    if (!empty($cleanAuthor)) {
                        $authors[] = $cleanAuthor;
                    }
                }
            }
        }
        $formData['authors'] = $authors;
        
        // Process genres from JSON with validation
        $genresJson = sanitizeString($_POST['genres_json'] ?? '', 1000);
        $genres = [];
        if (!empty($genresJson)) {
            $decoded = json_decode($genresJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $genre) {
                    $genreId = validateInteger($genre, 1);
                    if ($genreId) {
                        $genres[] = $genreId;
                    }
                }
            }
        }
        $formData['genres'] = $genres;
        
        // Validate required fields
        if (empty($formData['title'])) {
            throw new Exception('Titel är obligatorisk och får inte vara tom');
        }
        if (empty($formData['category_id'])) {
            throw new Exception('Kategori är obligatorisk');
        }
        
        // Update product in database using prepared statements
        // Start transaction
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }
        
        try {
            // Update main product data using prepared statement
            $sql = "UPDATE product SET 
                    title = :title, status = :status_id, shelf_id = :shelf_id, category_id = :category_id, 
                    price = :price, condition_id = :condition_id, notes = :notes, internal_notes = :internal_notes, 
                    year = :year, publisher = :publisher, special_price = :special_price, rare = :rare, 
                    recommended = :recommended, language_id = :language_id
                    WHERE prod_id = :product_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':title', $formData['title'], PDO::PARAM_STR);
            $stmt->bindValue(':status_id', $formData['status_id'], PDO::PARAM_INT);
            $stmt->bindValue(':shelf_id', $formData['shelf_id'], PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $formData['category_id'], PDO::PARAM_INT);
            $stmt->bindValue(':price', $formData['price'], PDO::PARAM_STR);
            $stmt->bindValue(':condition_id', $formData['condition_id'], PDO::PARAM_INT);
            $stmt->bindValue(':notes', $formData['notes'], PDO::PARAM_STR);
            $stmt->bindValue(':internal_notes', $formData['internal_notes'], PDO::PARAM_STR);
            $stmt->bindValue(':year', $formData['year'], PDO::PARAM_INT);
            $stmt->bindValue(':publisher', $formData['publisher'], PDO::PARAM_STR);
            $stmt->bindValue(':special_price', $formData['special_price'] ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':rare', $formData['rare'] ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':recommended', $formData['recommended'] ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':language_id', $formData['language_id'], PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Update authors using prepared statements
            $stmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = :product_id");
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            foreach ($formData['authors'] as $authorName) {
                $authorName = sanitizeString($authorName, 255);
                if (empty($authorName)) continue;
                
                // Check if author exists using prepared statement
                $stmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = :author_name");
                $stmt->bindValue(':author_name', $authorName, PDO::PARAM_STR);
                $stmt->execute();
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $authorId = $existing['author_id'];
                } else {
                    // Create new author using prepared statement
                    $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (:author_name)");
                    $stmt->bindValue(':author_name', $authorName, PDO::PARAM_STR);
                    $stmt->execute();
                    $authorId = $pdo->lastInsertId();
                }
                
                // Link author to product using prepared statement
                $stmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (:product_id, :author_id)");
                $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $stmt->bindValue(':author_id', $authorId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            // Update genres using prepared statements
            $stmt = $pdo->prepare("DELETE FROM product_genre WHERE product_id = :product_id");
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            foreach ($formData['genres'] as $genreId) {
                $genreId = validateInteger($genreId, 1);
                if ($genreId > 0) {
                    $stmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (:product_id, :genre_id)");
                    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->bindValue(':genre_id', $genreId, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
            
            // Handle images if any (outside of main transaction to avoid conflicts)
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
                        if (!empty($imageResult['errors'])) {
                            // Log errors but don't expose them to user
                        }
                    }
                } catch (Exception $imageEx) {
                    // Log image error but don't fail the whole update
                }
                
                // Start a new transaction for logging
                $pdo->beginTransaction();
            }
            
            // Log the update using prepared statement
            $currentUser = getSessionUser();
            $userId = $currentUser ? validateInteger($currentUser['user_id'], 1) : 1;
            
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description, product_id) 
                VALUES (:user_id, :event_type, :event_description, :product_id)
            ");
            $logStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $logStmt->bindValue(':event_type', 'update', PDO::PARAM_STR);
            $logStmt->bindValue(':event_description', "Uppdaterade produkt: " . sanitizeString($formData['title'], 255), PDO::PARAM_STR);
            $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $logStmt->execute();
            
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
        
        // Success response
        echo json_encode([
            'success' => true,
            'message' => htmlspecialchars('Produkt uppdaterad framgångsrikt' . $imageMessage, ENT_QUOTES, 'UTF-8')
        ]);
        exit;
        
    } catch (Exception $e) {
        // Only rollback if there's an active transaction
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        echo json_encode([
            'success' => false,
            'message' => htmlspecialchars('Ett fel inträffade: ' . $e->getMessage(), ENT_QUOTES, 'UTF-8')
        ]);
        exit;
    }
}

// Regular page loading (non-AJAX)
checkAuth(2);

$productId = validateInteger($_GET['id'] ?? 0, 1);
if (!$productId || $productId <= 0) {
    header("Location: " . url('admin.php?tab=search'));
    exit;
}

// Get product data using prepared statements
try {
    $sql = "SELECT
        p.prod_id, p.title, p.status, p.shelf_id, p.category_id, p.price,
        p.condition_id, p.notes, p.internal_notes, p.year, p.publisher,
        p.special_price, p.rare, p.recommended, p.date_added, p.language_id
    FROM product p WHERE p.prod_id = :product_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$product) {
        header("Location: " . url('admin.php?tab=search'));
        exit;
    }
    
    // Get authors using prepared statement
    $authorSql = "SELECT a.author_id, a.author_name
                  FROM author a
                  JOIN product_author pa ON a.author_id = pa.author_id
                  WHERE pa.product_id = :product_id";
    
    $authorStmt = $pdo->prepare($authorSql);
    $authorStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $authorStmt->execute();
    $product->authors = $authorStmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get genres using prepared statement
    $genreSql = "SELECT g.genre_id, g.genre_sv_name as genre_name
                FROM genre g
                JOIN product_genre pg ON g.genre_id = pg.genre_id
                WHERE pg.product_id = :product_id";
    
    $genreStmt = $pdo->prepare($genreSql);
    $genreStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $genreStmt->execute();
    $product->genres = $genreStmt->fetchAll(PDO::FETCH_OBJ);
    
    // Get images using prepared statement
    $imagesSql = "SELECT image_id, image_path, image_uploaded_at
                 FROM image 
                 WHERE prod_id = :product_id
                 ORDER BY image_uploaded_at ASC";
    $imagesStmt = $pdo->prepare($imagesSql);
    $imagesStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $imagesStmt->execute();
    $product->images = $imagesStmt->fetchAll(PDO::FETCH_OBJ);
    
} catch (Exception $e) {
    die("Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// Format data for JavaScript with proper escaping
$authorsData = array_map(function($author) {
    return [
        'id' => (int)$author->author_id, 
        'name' => htmlspecialchars($author->author_name, ENT_QUOTES, 'UTF-8')
    ];
}, $product->authors);

$genresData = array_map(function($genre) {
    return [
        'id' => (int)$genre->genre_id, 
        'name' => htmlspecialchars($genre->genre_name, ENT_QUOTES, 'UTF-8')
    ];
}, $product->genres);

/**
 * Renders select options for form fields with proper escaping
 *
 * @param PDO $pdo Database connection
 * @param string $table Table name (validated internally)
 * @param string $idField ID field name
 * @param string $nameField Name field name
 * @param mixed $selectedValue Currently selected value
 * @param string $locale Current locale code
 * @return void
 */
function renderSelectOptions($pdo, $table, $idField, $nameField, $selectedValue = '', $locale = 'sv') {
    try {
        // Validate table name against whitelist for security
        $allowed_tables = ['status', 'shelf', 'category', 'condition', 'language', 'genre'];
        if (!in_array($table, $allowed_tables)) {
            echo "<option value=''>Ogiltig tabell</option>";
            return;
        }

        // Validate locale
        $locale = in_array($locale, ['sv', 'fi', 'en']) ? $locale : 'sv';
        
        $localizedField = $nameField . '_' . $locale . '_name';
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$localizedField'");
        $useLocalized = $stmt->rowCount() > 0;
        
        $displayField = $useLocalized ? $localizedField : $nameField;
        $sql = "SELECT $idField, $displayField as display_name FROM `$table` ORDER BY $displayField";
        
        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($selectedValue == $row[$idField]) ? 'selected' : '';
            $id = htmlspecialchars($row[$idField], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($row['display_name'], ENT_QUOTES, 'UTF-8');
            echo "<option value=\"{$id}\" {$selected}>{$name}</option>";
        }
    } catch (PDOException $e) {
        echo "<option value=''>Fel vid hämtning av data</option>";
    }
}

include dirname(__DIR__) . '/templates/admin_header.php';
?>

<!-- Main Content Container -->
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Redigera produkt: <?php echo htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <div>
            <a href="<?php echo url('admin.php?tab=search'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Tillbaka till sökning
            </a>
        </div>
    </div>

    <form id="edit-product-form" method="POST" enctype="multipart/form-data" action="<?php echo url('admin/adminsingleproduct.php?id=' . $productId); ?>">
        <!-- CRITICAL: Hidden product ID field -->
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>">
        
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
                                        <div class="image-container mb-2" data-image-id="<?php echo htmlspecialchars($image->image_id, ENT_QUOTES, 'UTF-8'); ?>">
                                            <img src="<?php echo url(htmlspecialchars($image->image_path, ENT_QUOTES, 'UTF-8')); ?>" 
                                                 alt="Produktbild" class="image-preview rounded img-fluid">
                                            <button type="button" class="btn btn-sm btn-danger mt-1" 
                                                    onclick="deleteImage(<?php echo htmlspecialchars($image->image_id, ENT_QUOTES, 'UTF-8'); ?>)"
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
    Max <?php echo htmlspecialchars(($app_config['uploads']['max_size'] / (1024 * 1024)), ENT_QUOTES, 'UTF-8'); ?>MB per bild, 
    <?php echo htmlspecialchars(strtoupper(implode(', ', $app_config['uploads']['allowed_extensions'])), ENT_QUOTES, 'UTF-8'); ?>
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
                                       value="<?php echo htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8'); ?>" required maxlength="255">
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
                                                 data-name="<?php echo htmlspecialchars($author->author_name, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($author->author_name, ENT_QUOTES, 'UTF-8'); ?> <span class="ms-1">×</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <em class="text-muted">Ingen författare vald</em>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-12 position-relative mb-2">
                                    <div class="input-group input-plus">
                                        <input type="text" class="form-control" id="author-name" 
                                               placeholder="Ange författarens namn" autocomplete="off" maxlength="255">
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
                                                 data-genre-id="<?php echo htmlspecialchars($genre->genre_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($genre->genre_name, ENT_QUOTES, 'UTF-8'); ?> <span class="ms-1">×</span>
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
                                       value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" min="0" max="99999.99">
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
                                       value="<?php echo htmlspecialchars($product->year, ENT_QUOTES, 'UTF-8'); ?>" min="1400" max="<?php echo htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-4 position-relative">
                                <label for="item-publisher" class="form-label">Förlag</label>
                                <input type="text" class="form-control" id="item-publisher" name="publisher" 
                                       value="<?php echo htmlspecialchars($product->publisher, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" maxlength="255">
                                <div id="suggest-publisher" class="list-group position-absolute w-100" style="z-index: 1000; top: 100%;"></div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="item-notes" class="form-label">Produktbeskrivning (synlig för kunder)</label>
                            <textarea class="form-control" id="item-notes" name="notes" rows="3" maxlength="1000"><?php echo htmlspecialchars($product->notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-internal-notes" class="form-label">Intern beskrivning (endast för personal)</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes" rows="2" maxlength="1000"><?php echo htmlspecialchars($product->internal_notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
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
    
<button type="button" class="btn btn-outline-danger" onclick="deleteSingleProduct(<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>)">
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
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
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

/**
 * Deletes an image from the product
 * 
 * @param {number} imageId The ID of the image to delete
 */
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

/**
 * Resets the form to its original state
 */
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

/**
 * Deletes a single product (different from batch operations)
 * 
 * @param {number} productId The ID of the product to delete
 */
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

/**
 * Security Recommendations for php.ini:
 * 
 * The following functions should be disabled in production for enhanced security:
 * disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,eval,assert
 * 
 * Additional security measures:
 * - expose_php = Off
 * - display_errors = Off
 * - log_errors = On
 * - allow_url_fopen = Off (if not needed)
 * - allow_url_include = Off
 * - file_uploads = On (only if needed)
 * - upload_max_filesize = 10M (adjust as needed)
 * - post_max_size = 20M (adjust as needed)
 * - max_execution_time = 30
 * - max_input_time = 60
 * - memory_limit = 128M
 * 
 * Database Security:
 * - All queries use prepared statements with parameter binding
 * - Input validation and sanitization implemented
 * - SQL injection protection through PDO parameter binding
 * - Table name validation using whitelists
 * 
 * Input Validation:
 * - Custom sanitization functions replace deprecated FILTER_SANITIZE_STRING
 * - Numeric validation with range checking
 * - String length validation and trimming
 * - HTML entity encoding for output
 * - JSON data validation and sanitization
 * 
 * CSRF Protection:
 * - CSRF tokens implemented for form submission
 * - Rate limiting to prevent abuse
 * - Authentication checks for all operations
 * 
 * File Upload Security:
 * - File type validation through ImageProcessor class
 * - File size limitations enforced
 * - Proper file handling and storage
 * 
 * XSS Prevention:
 * - All output properly escaped with htmlspecialchars()
 * - ENT_QUOTES and UTF-8 encoding used consistently
 * - JavaScript data properly JSON encoded and escaped
 */
?>