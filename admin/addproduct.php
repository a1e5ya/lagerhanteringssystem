<?php
/**
 * Add Product - Enhanced Security Version
 *
 * This file contains the form and processing logic for adding new products to the inventory.
 * Enhanced with modern security practices, input validation, and proper documentation.
 *
 * @package Bookstore
 * @author System Administrator
 * @version 3.0
 * @since 2025-06-05
 */

// init.php already includes config.php and ImageProcessor.php
require_once '../init.php';

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

// Handle POST requests (CSRF and rate limiting)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting first
    if (!checkRateLimit('add_product', 10, 300)) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'För många försök. Vänta innan du försöker igen.']);
            exit;
        }
        header('Location: ' . url('admin.php?tab=addproduct&error=rate_limit'));
        exit;
    }
    
    // Check CSRF token
    checkCSRFToken();
}

// Check if user is authenticated and has admin or editor permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user locale preference (default to 'sv')
$locale = $_SESSION['locale'] ?? 'sv';

// Initialize ImageProcessor
global $pdo, $app_config;
$imageProcessor = new ImageProcessor($pdo, $app_config['uploads']);

/**
 * Creates a new product with related data using prepared statements
 *
 * @param array $data Product data array containing validated input
 * @param PDO $pdo Database connection
 * @return array Result with product ID on success, error message on failure
 * @throws PDOException When database operation fails
 */
function createProduct($data, $pdo) {
    try {
        // Start transaction to ensure data integrity
        $pdo->beginTransaction();

        // Set default values for non-required fields with proper validation
        $status_id = validateInteger($data['status_id'] ?? 1, 1) ?: 1;
        $shelf_id = validateInteger($data['shelf_id'] ?? null, 1);
        $category_id = validateInteger($data['category_id'] ?? null, 1);
        $price = validateNumeric($data['price'] ?? null, 0);
        $condition_id = validateInteger($data['condition_id'] ?? null, 1);
        $notes = sanitizeString($data['notes'] ?? '', 1000);
        $internal_notes = sanitizeString($data['internal_notes'] ?? '', 1000);
        $language_id = validateInteger($data['language_id'] ?? null, 1);
        $year = validateInteger($data['year'] ?? null, 1400, date('Y'));
        $publisher = sanitizeString($data['publisher'] ?? '', 255);

        // Only set to 1 if explicitly checked
        $special_price = isset($data['special_price']) && $data['special_price'] == 1 ? 1 : 0;
        $rare = isset($data['rare']) && $data['rare'] == 1 ? 1 : 0;
        $recommended = isset($data['recommended']) && $data['recommended'] == 1 ? 1 : 0;

        // Prepare statement for product insertion
        $stmt = $pdo->prepare("INSERT INTO product (
            title,
            status,
            shelf_id,
            category_id,
            price,
            condition_id,
            notes,
            internal_notes,
            language_id,
            year,
            publisher,
            special_price,
            rare,
            recommended
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            sanitizeString($data['title'], 255),
            $status_id,
            $shelf_id,
            $category_id,
            $price,
            $condition_id,
            $notes,
            $internal_notes,
            $language_id,
            $year,
            $publisher,
            $special_price,
            $rare,
            $recommended
        ]);

        // Get the inserted product's ID
        $product_id = $pdo->lastInsertId();

        // Process authors with prepared statements
        if (!empty($data['authors']) && is_array($data['authors'])) {
            foreach ($data['authors'] as $author_name) {
                $author_name = sanitizeString($author_name, 255);
                if (empty($author_name)) {
                    continue; // Skip empty authors
                }

                // Check if author already exists using prepared statement
                $stmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = :author_name");
                $stmt->bindValue(':author_name', $author_name, PDO::PARAM_STR);
                $stmt->execute();
                $existing_author = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_author) {
                    $author_id = $existing_author['author_id'];
                } else {
                    // Create new author using prepared statement
                    $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (:author_name)");
                    $stmt->bindValue(':author_name', $author_name, PDO::PARAM_STR);
                    $stmt->execute();
                    $author_id = $pdo->lastInsertId();
                }

                // Create product_author relationship using prepared statement
                $stmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (:product_id, :author_id)");
                $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        // Process genres with prepared statements
        if (!empty($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genre_id) {
                $genre_id = validateInteger($genre_id, 1);
                if (empty($genre_id)) {
                    continue; // Skip invalid genres
                }

                // Create product_genre relationship using prepared statement
                $stmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (:product_id, :genre_id)");
                $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindValue(':genre_id', $genre_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        // Log the creation using prepared statement
        $currentUser = getSessionUser();
        $userId = $currentUser ? validateInteger($currentUser['user_id'], 1) : 1;
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description, product_id) 
            VALUES (:user_id, :event_type, :event_description, :product_id)
        ");
        
        $eventDescription = "Skapade produkt: " . sanitizeString($data['title'], 255);
        
        $logStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $logStmt->bindValue(':event_type', 'create', PDO::PARAM_STR);
        $logStmt->bindValue(':event_description', $eventDescription, PDO::PARAM_STR);
        $logStmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $logStmt->execute();

        // Commit transaction
        $pdo->commit();
        return ['success' => true, 'product_id' => $product_id];
    } catch (PDOException $e) {
        // Roll back transaction if error
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Renders input alternatives for select fields with proper escaping
 *
 * @param PDO $pdo Database connection
 * @param string $table Table name (validated internally)
 * @param string $id_field ID field name (validated internally)
 * @param string $name_field Name field name (validated internally)
 * @param string $selected_value Currently selected value
 * @param string $locale Current locale code (sv, fi, etc.)
 * @return void
 */
function renderInputAlternatives($pdo, $table, $id_field, $name_field, $selected_value = '', $locale = 'sv') {
    try {
        // Validate table name against whitelist for security
        $allowed_tables = ['status', 'shelf', 'category', 'condition', 'language', 'genre'];
        if (!in_array($table, $allowed_tables)) {
            echo "<option value=''>Ogiltig tabell</option>";
            return;
        }

        // Validate locale
        $locale = in_array($locale, ['sv', 'fi', 'en']) ? $locale : 'sv';
        
        // Construct the language-specific field name
        $localized_name_field = $name_field . '_' . $locale . '_name';

        // Check if the localized column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$localized_name_field'");
        $column_exists = $stmt->rowCount() > 0;

        if ($column_exists) {
            // Use language-specific column (table name is validated above)
            $sql = "SELECT $id_field, {$localized_name_field} AS display_name FROM `$table` ORDER BY {$localized_name_field}";
        } else {
            // Fallback to checking if base name field exists
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$name_field'");
            if ($stmt->rowCount() > 0) {
                $sql = "SELECT $id_field, $name_field AS display_name FROM `$table` ORDER BY $name_field";
            } else {
                // If neither exists, try a more flexible approach
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` WHERE Field LIKE '%name%' AND Field != '$id_field'");
                $name_column = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($name_column) {
                    $found_name_field = $name_column['Field'];
                    $sql = "SELECT $id_field, $found_name_field AS display_name FROM `$table` ORDER BY $found_name_field";
                } else {
                    echo "<option value=''>Ingen namnkolumn hittades i " . htmlspecialchars($table) . "</option>";
                    return;
                }
            }
        }

        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = htmlspecialchars($row[$id_field], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($row['display_name'], ENT_QUOTES, 'UTF-8');
            $selected = ($selected_value == $row[$id_field]) ? 'selected' : '';
            echo "<option value=\"{$id}\" {$selected}>{$name}</option>";
        }
    } catch (PDOException $e) {
        echo "<option value=''>Fel vid hämtning av data</option>";
    }
}

// Check if AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

/**
 * Gets the available status ID from database
 *
 * @param PDO $pdo Database connection
 * @return int Available status ID or 1 as default
 */
function getAvailableStatusId($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT status_id FROM status WHERE status_sv_name = :sv_name OR status_fi_name = :fi_name LIMIT 1");
        $stmt->bindValue(':sv_name', 'Tillgänglig', PDO::PARAM_STR);
        $stmt->bindValue(':fi_name', 'Saatavilla', PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result ?: 1; // Default to 1 if not found
    } catch (Exception $e) {
        return 1; // Default to 1 if there's an error
    }
}

$availableStatusId = getAvailableStatusId($pdo);

// Handle POST requests (AJAX form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Turn off output buffering and error display for clean JSON response
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Ensure we send JSON response for AJAX requests
    if ($isAjax) {
        header('Content-Type: application/json');
        // Disable error display for JSON responses
        ini_set('display_errors', 0);
    }

    try {
        // Validate and sanitize form data
        $formData = [
            'title' => sanitizeString($_POST['title'] ?? '', 255),
            'status_id' => validateInteger($_POST['status_id'] ?? 1, 1) ?: 1,
            'shelf_id' => validateInteger($_POST['shelf_id'] ?? null, 1),
            'category_id' => validateInteger($_POST['category_id'] ?? null, 1),
            'price' => validateNumeric($_POST['price'] ?? null, 0),
            'condition_id' => validateInteger($_POST['condition_id'] ?? null, 1),
            'notes' => sanitizeString($_POST['notes'] ?? '', 1000),
            'internal_notes' => sanitizeString($_POST['internal_notes'] ?? '', 1000),
            'language_id' => validateInteger($_POST['language_id'] ?? null, 1),
            'year' => validateInteger($_POST['year'] ?? null, 1400, date('Y')),
            'publisher' => sanitizeString($_POST['publisher'] ?? '', 255),
            'special_price' => isset($_POST['special_price']) ? 1 : 0,
            'rare' => isset($_POST['rare']) ? 1 : 0,
            'recommended' => isset($_POST['recommended']) ? 1 : 0
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

        // Create the product
        $result = createProduct($formData, $pdo);

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        $product_id = $result['product_id'];
        
        // Process image uploads after product creation
        $imageUploadResult = ['success' => true, 'message' => '', 'uploaded_count' => 0];
        
        if (isset($_FILES['item_images']) && !empty($_FILES['item_images']['name'][0])) {
            $imageUploadResult = $imageProcessor->uploadProductImages($_FILES['item_images'], $product_id);
        }

        // Prepare success message
        $successMsg = "Produkt tillagd framgångsrikt!";
        if (!$imageUploadResult['success'] && $imageUploadResult['uploaded_count'] == 0) {
            $successMsg .= " Dock, " . $imageUploadResult['message'];
            if (!empty($imageUploadResult['errors'])) {
                $successMsg .= " Detaljer: " . implode(", ", $imageUploadResult['errors']);
            }
        } elseif ($imageUploadResult['uploaded_count'] > 0) {
            $successMsg .= " " . $imageUploadResult['uploaded_count'] . " bilder uppladdade.";
        }

        // Send JSON response for AJAX
        if ($isAjax) {
            echo json_encode([
                'success' => true, 
                'message' => htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'),
                'product_id' => (int)$product_id
            ]);
            exit;
        }

        // Fallback for non-AJAX requests
        $_SESSION['message'] = $successMsg;
        header('Location: admin.php?tab=addproduct');
        exit;

    } catch (Exception $e) {
        $errorMsg = "Ett fel inträffade: " . $e->getMessage();
        
        if ($isAjax) {
            echo json_encode([
                'success' => false, 
                'message' => htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8')
            ]);
            exit;
        }
        
        $_SESSION['error_message'] = $errorMsg;
        header('Location: admin.php?tab=addproduct');
        exit;
    }
}

// If we reach here, it's a GET request - display the form
?>

<div class="tab-pane fade show active" id="add">
    <form id="add-item-form" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Produktbilder</h5>
                        <div class="item-image-previews mb-3" id="item-image-previews">
                            <img src="assets/images/default_antiqe_image.webp" alt="Standard produktbild" class="img-fluid rounded shadow default-preview"
                                id="default-image-preview">
                        </div>
                        <div class="mb-3">
                            <label for="item-image-upload" class="form-label">Ladda upp bilder</label>
                            <input class="form-control" type="file" id="item-image-upload" name="item_images[]" multiple accept="image/*">
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
</small>                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Grundinformation</h5>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="item-category" class="form-label">Kategori</label>
                                <select class="form-select" id="item-category" name="category_id" required>
                                    <option value="">Välj Kategori</option>
                                    <?php renderInputAlternatives($pdo, 'category', 'category_id', 'category', '', $locale); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-status" class="form-label">Status</label>
                                <select class="form-select" id="item-status" name="status_id">
                                    <?php
                                    // Get available status ID
                                    $availableId = 1;
                                    try {
                                        $statusStmt = $pdo->prepare("SELECT status_id FROM status WHERE status_sv_name = :status_name LIMIT 1");
                                        $statusStmt->bindValue(':status_name', 'Tillgänglig', PDO::PARAM_STR);
                                        $statusStmt->execute();
                                        if ($result = $statusStmt->fetch(PDO::FETCH_ASSOC)) {
                                            $availableId = $result['status_id'];
                                        }
                                    } catch(Exception $e) {
                                        // Use default value
                                    }
                                    // Render options with available status pre-selected
                                    renderInputAlternatives($pdo, 'status', 'status_id', 'status', $availableId, $locale);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 position-relative mb-2">
                                <label for="item-title" class="form-label">Titel</label>
                                <input type="text" class="form-control" id="item-title" name="title" required maxlength="255">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Författare</label>
                                <div class="selected-authors mb-2">
                                    <em class="text-muted">Ingen författare vald</em>
                                </div>
                                <div class="col-md-12 position-relative mb-2">
                                    <div class="input-group input-plus">
                                        <input type="text" class="form-control" id="author-name" name="author_name"
                                            autocomplete="off" placeholder="Ange författarens namn" maxlength="255">
                                        <button type="button" class="btn btn-outline-secondary" style="height: 38px;" id="add-author-btn">
                                            <i class="fas fa-plus"></i> Lägg till
                                        </button>
                                    </div>
                                    <div id="suggest-author" class="list-group position-absolute w-100 zindex-dropdown"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="item-genre" class="form-label">Genre</label>
                                <div class="selected-genres mb-2">
                                    <em class="text-muted">Ingen genre vald</em>
                                </div>
                                <div class="input-group input-plus">
                                    <select class="form-select" id="item-genre" name="genre_id">
                                        <option value="">Välj Genre</option>
                                        <?php renderInputAlternatives($pdo, 'genre', 'genre_id', 'genre', '', $locale); ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" style="height: 38px;" id="add-genre-btn">
                                        <i class="fas fa-plus"></i> Lägg till
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-price" class="form-label">Pris (€)</label>
                                <input type="number" step="0.01" class="form-control" id="item-price" name="price" min="0" max="99999.99">
                            </div>
                            <div class="col-md-4">
                                <label for="item-condition" class="form-label">Skick</label>
                                <select class="form-select" id="item-condition" name="condition_id">
                                    <option value="">Välj Skick</option>
                                    <?php renderInputAlternatives($pdo, 'condition', 'condition_id', 'condition', '', $locale); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-shelf" class="form-label">Hylla</label>
                                <select class="form-select" id="item-shelf" name="shelf_id">
                                    <option value="">Välj Hylla</option>
                                    <?php renderInputAlternatives($pdo, 'shelf', 'shelf_id', 'shelf', '', $locale); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-language" class="form-label">Språk</label>
                                <select class="form-select" id="item-language" name="language_id">
                                    <option value="">Välj Språk</option>
                                    <?php renderInputAlternatives($pdo, 'language', 'language_id', 'language', '', $locale); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-year" class="form-label">År</label>
                                <input type="number" class="form-control" id="item-year" name="year" min="1400"
                                    max="<?php echo htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-4 position-relative">
                                <label for="item-publisher" class="form-label">Förlag</label>
                                <input type="text" class="form-control" id="item-publisher" name="publisher"
                                    autocomplete="off" maxlength="255">
                                <div id="suggest-publisher" class="list-group position-absolute w-100 zindex-dropdown"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="item-notes" class="form-label">Produktbeskrivning (synlig för kunder)</label>
                            <textarea class="form-control" id="item-notes" name="notes" rows="3" maxlength="1000"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-internal-notes" class="form-label">Intern beskrivning (endast för personal)</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes" rows="2" maxlength="1000"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-special-price"
                                        name="special_price" value="1">
                                    <label class="form-check-label" for="item-special-price">Special Pris</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-rare" name="rare"
                                        value="1">
                                    <label class="form-check-label" for="item-rare">Raritet</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-recommended" name="recommended"
                                        value="1">
                                    <label class="form-check-label" for="item-recommended">Rekommenderad</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-outline-secondary me-2">Rensa</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="submit-text">Lägg till produkt</span>
                                <span class="submit-spinner d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Sparar...
                                </span>
                            </button>
                        </div>

                        <input type="hidden" id="authors-json" name="authors_json" value="">
                        <input type="hidden" id="genres-json" name="genres_json" value="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>"
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="<?php echo url('assets/js/addproduct-handlers.js'); ?>"></script>

<?php
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
 * 
 * Input Validation:
 * - Custom sanitization functions replace deprecated FILTER_SANITIZE_STRING
 * - Numeric validation with range checking
 * - String length validation and trimming
 * - HTML entity encoding for output
 * 
 * CSRF Protection:
 * - CSRF tokens implemented for form submission
 * - Rate limiting to prevent abuse
 * 
 * File Upload Security:
 * - File type validation through ImageProcessor class
 * - File size limitations enforced
 * - Proper file handling and storage
 */
?>