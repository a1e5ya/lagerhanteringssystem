<?php
/**
 * Add Product - Fixed Version
 *
 * This file contains the form and processing logic for adding new products to the inventory.
 * Fixed to work with AJAX submission and proper image handling.
 *
 * @package Bookstore
 * @author System Administrator
 * @version 2.0
 */

// init.php already includes config.php and ImageProcessor.php
require_once '../init.php';

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
 * Creates a new product with related data
 *
 * @param array $data Product data array
 * @param PDO $pdo Database connection
 * @return array Result with product ID on success, error message on failure
 */
function createProduct($data, $pdo) {
    try {
        // Start transaction to ensure data integrity
        $pdo->beginTransaction();

        // Set default values for non-required fields
        $status_id = !empty($data['status_id']) ? $data['status_id'] : 1;
        $shelf_id = !empty($data['shelf_id']) ? $data['shelf_id'] : null;
        $category_id = !empty($data['category_id']) ? $data['category_id'] : null;
        $price = !empty($data['price']) ? $data['price'] : null;
        $condition_id = !empty($data['condition_id']) ? $data['condition_id'] : null;
        $notes = !empty($data['notes']) ? $data['notes'] : '';
        $internal_notes = !empty($data['internal_notes']) ? $data['internal_notes'] : '';
        $language_id = !empty($data['language_id']) ? $data['language_id'] : null;
        $year = !empty($data['year']) ? $data['year'] : null;
        $publisher = !empty($data['publisher']) ? $data['publisher'] : '';

        // Only set to 1 if explicitly checked
        $special_price = isset($data['special_price']) && $data['special_price'] == 1 ? 1 : 0;
        $rare = isset($data['rare']) && $data['rare'] == 1 ? 1 : 0;
        $recommended = isset($data['recommended']) && $data['recommended'] == 1 ? 1 : 0;

        // Debug log
        error_log("Creating product with data: " . print_r([
            'title' => $data['title'],
            'authors' => $data['authors'] ?? [],
            'genres' => $data['genres'] ?? [],
            'special_price' => $special_price,
            'rare' => $rare,
            'recommended' => $recommended
        ], true));

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
            $data['title'],
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

        // Process authors
        if (!empty($data['authors']) && is_array($data['authors'])) {
            foreach ($data['authors'] as $author_name) {
                $author_name = trim($author_name);
                if (empty($author_name)) {
                    continue; // Skip empty authors
                }

                // Check if author already exists
                $stmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = ?");
                $stmt->execute([$author_name]);
                $existing_author = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_author) {
                    $author_id = $existing_author['author_id'];
                } else {
                    // Create new author
                    $stmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (?)");
                    $stmt->execute([$author_name]);
                    $author_id = $pdo->lastInsertId();
                }

                // Create product_author relationship
                $stmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (?, ?)");
                $stmt->execute([$product_id, $author_id]);
            }
        }

        // Process genres
        if (!empty($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genre_id) {
                $genre_id = (int)$genre_id;
                if (empty($genre_id)) {
                    continue; // Skip empty genres
                }

                // Create product_genre relationship
                $stmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (?, ?)");
                $stmt->execute([$product_id, $genre_id]);
            }
        }

        // Log the creation
        $currentUser = getSessionUser();
        $userId = $currentUser ? $currentUser['user_id'] : 1;
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description, product_id) 
            VALUES (:user_id, :event_type, :event_description, :product_id)
        ");
        
        $eventDescription = "Skapade produkt: " . $data['title'];
        
        $logStmt->execute([
            ':user_id' => $userId,
            ':event_type' => 'create',
            ':event_description' => $eventDescription,
            ':product_id' => $product_id
        ]);

        // Commit transaction
        $pdo->commit();
        return ['success' => true, 'product_id' => $product_id];
    } catch (PDOException $e) {
        // Roll back transaction if error
        $pdo->rollBack();
        error_log($e->getMessage());
        return ['success' => false, 'message' => 'Ett fel inträffade vid skapande av produkt: ' . $e->getMessage()];
    }
}

/**
 * Renders input alternatives for select fields
 *
 * @param PDO $pdo Database connection
 * @param string $table Table name
 * @param string $id_field ID field name
 * @param string $name_field Name field name
 * @param string $selected_value Currently selected value
 * @param string $locale Current locale code (sv, fi, etc.)
 * @return void
 */
function renderInputAlternatives($pdo, $table, $id_field, $name_field, $selected_value = '', $locale = 'sv') {
    try {
        // Construct the language-specific field name
        $localized_name_field = $name_field . '_' . $locale . '_name';

        // Check if the localized column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$localized_name_field'");
        $column_exists = $stmt->rowCount() > 0;

        if ($column_exists) {
            // Use language-specific column
            $sql = "SELECT $id_field, {$localized_name_field} AS display_name FROM `$table`";
        } else {
            // Fallback to checking if base name field exists
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$name_field'");
            if ($stmt->rowCount() > 0) {
                $sql = "SELECT $id_field, $name_field AS display_name FROM `$table`";
            } else {
                // If neither exists, try a more flexible approach
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` WHERE Field LIKE '%name%' AND Field != '$id_field'");
                $name_column = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($name_column) {
                    $found_name_field = $name_column['Field'];
                    $sql = "SELECT $id_field, $found_name_field AS display_name FROM `$table`";
                } else {
                    echo "<option value=''>Ingen namnkolumn hittades i $table</option>";
                    return;
                }
            }
        }

        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row[$id_field];
            $name = $row['display_name'];
            $selected = ($selected_value == $id) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($id) . "\" $selected>" . htmlspecialchars($name) . "</option>";
        }
    } catch (PDOException $e) {
        error_log("Error rendering options for $table: " . $e->getMessage());
        echo "<option value=''>Fel: " . htmlspecialchars($e->getMessage()) . "</option>";
    }
}

// Check if AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

function getAvailableStatusId($pdo) {
    try {
        $sql = "SELECT status_id FROM status WHERE status_sv_name = 'Tillgänglig' OR status_fi_name = 'Saatavilla'";
        $stmt = $pdo->query($sql);
        return $stmt->fetchColumn() ?: 1; // Default to 1 if not found
    } catch (Exception $e) {
        error_log("Error getting available status ID: " . $e->getMessage());
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
        // Get form data
        $formData = [
            'title' => $_POST['title'] ?? null,
            'status_id' => $_POST['status_id'] ?? 1,
            'shelf_id' => $_POST['shelf_id'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
            'price' => $_POST['price'] ?? null,
            'condition_id' => $_POST['condition_id'] ?? null,
            'notes' => $_POST['notes'] ?? null,
            'internal_notes' => $_POST['internal_notes'] ?? null,
            'language_id' => $_POST['language_id'] ?? null,
            'year' => $_POST['year'] ?? null,
            'publisher' => $_POST['publisher'] ?? null,
            'special_price' => isset($_POST['special_price']) ? 1 : 0,
            'rare' => isset($_POST['rare']) ? 1 : 0,
            'recommended' => isset($_POST['recommended']) ? 1 : 0
        ];

        // Process authors from JSON
        $authorsJson = $_POST['authors_json'] ?? null;
        $authors = [];
        if (!empty($authorsJson)) {
            $authors = json_decode($authorsJson, true);
            if (!is_array($authors)) {
                $authors = [];
            }
        }
        $formData['authors'] = $authors;

        // Process genres from JSON
        $genresJson = $_POST['genres_json'] ?? null;
        $genres = [];
        if (!empty($genresJson)) {
            $genres = json_decode($genresJson, true);
            if (!is_array($genres)) {
                $genres = [];
            }
        }
        $formData['genres'] = $genres;

        // Validate required fields
        if (empty($formData['title'])) {
            throw new Exception('Titel är obligatorisk');
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
                'message' => $successMsg,
                'product_id' => $product_id
            ]);
            exit;
        }

        // Fallback for non-AJAX requests
        $_SESSION['message'] = $successMsg;
        header('Location: admin.php?tab=addproduct');
        exit;

    } catch (Exception $e) {
        $errorMsg = "Ett fel inträffade: " . $e->getMessage();
        error_log("Add product error: " . $e->getMessage());
        
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => $errorMsg]);
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
                            <small class="form-text text-muted">Välj en eller flera bilder (Max <?php echo ($app_config['uploads']['max_size'] / (1024 * 1024)); ?>MB per bild, <?php echo strtoupper(implode(', ', $app_config['uploads']['allowed_extensions'])); ?>)</small>
                        </div>
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
                                        $statusStmt = $pdo->query("SELECT status_id FROM status WHERE status_sv_name = 'Tillgänglig' LIMIT 1");
                                        if ($result = $statusStmt->fetch(PDO::FETCH_ASSOC)) {
                                            $availableId = $result['status_id'];
                                        }
                                    } catch(Exception $e) {
                                        error_log("Error getting available status ID: " . $e->getMessage());
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
                                <input type="text" class="form-control" id="item-title" name="title" required>
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
                                            autocomplete="off" placeholder="Ange författarens namn">
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
                                <input type="number" step="0.01" class="form-control" id="item-price" name="price">
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
                                    max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col-md-4 position-relative">
                                <label for="item-publisher" class="form-label">Förlag</label>
                                <input type="text" class="form-control" id="item-publisher" name="publisher"
                                    autocomplete="off">
                                <div id="suggest-publisher" class="list-group position-absolute w-100 zindex-dropdown"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="item-notes" class="form-label">Produktbeskrivning (synlig för kunder)</label>
                            <textarea class="form-control" id="item-notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-internal-notes" class="form-label">Intern beskrivning (endast för personal)</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes" rows="2"></textarea>
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
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="<?php echo url('assets/js/addproduct-handlers.js'); ?>"></script>