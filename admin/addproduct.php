<?php
/**
 * Add Product
 *
 * This file contains the form and processing logic for adding new products to the inventory.
 *
 * @package Bookstore
 * @author System Administrator
 * @version 1.1
 */

// init.php already includes config.php and ImageProcessor.php
require_once '../init.php'; // Correct path

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



/**
 * Creates a new product with related data
 *
 * @param array $data Product data array
 * @param PDO $pdo Database connection
 * @return int|bool The product ID on success, false on failure
 */
function createProduct($data, $pdo) {
    try {
        // Start transaction to ensure data integrity
        $pdo->beginTransaction();

        // Set default values for non-required fields
        $status_id = !empty($data['status_id']) ? $data['status_id'] : 1;
        $shelf_id = !empty($data['shelf_id']) ? $data['shelf_id'] : null;
        $category_id = !empty($data['category_id']) ? $data['category_id'] : null;
        $price = !empty($data['price']) ? $data['price'] : 0;
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
        if (!empty($data['authors'])) {
            foreach ($data['authors'] as $author_name) {
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
        if (!empty($data['genres'])) {
            foreach ($data['genres'] as $genre_id) {
                if (empty($genre_id)) {
                    continue; // Skip empty genres
                }

                // Create product_genre relationship
                $stmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (?, ?)");
                $stmt->execute([$product_id, $genre_id]);
            }
        }

        // Commit transaction
        $pdo->commit();
        return $product_id;
    } catch (PDOException $e) {
        // Roll back transaction if error
        $pdo->rollBack();
        error_log($e->getMessage());
        return false;
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
        $localized_name_field = $name_field . '_' . $locale . '_name'; // <--- ADDED '_name' here;

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
                //error_log("Could not find column $localized_name_field or $name_field in table $table");

                // Get the first column that might be a name column
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` WHERE Field LIKE '%name%' AND Field != '$id_field'");
                $name_column = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($name_column) {
                    $found_name_field = $name_column['Field'];
                    $sql = "SELECT $id_field, $found_name_field AS display_name FROM `$table`";
                } else {
                    echo "<option value=''>No name column found in $table</option>";
                    return;
                }
            }
        }

        // For DEBUGGING
        // error_log("SQL for $table: $sql");

        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row[$id_field];
            $name = $row['display_name'];
            $selected = ($selected_value == $id) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($id) . "\" $selected>" . htmlspecialchars($name) . "</option>";
        }
    } catch (PDOException $e) {
        error_log("Error rendering options for $table: " . $e->getMessage());
        echo "<option value=''>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
    }
}

// Check if AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Get user locale preference (default to 'sv')
$locale = $_SESSION['locale'] ?? 'sv';

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

// Initialize ImageProcessor (needs the global $pdo and $app_config from init.php)
global $pdo, $app_config; // Ensure these are accessible if not already by default
$uploadDir = $app_config['uploads']['product_images_path']; // Correct access
$imageProcessor = new ImageProcessor($pdo, $uploadDir); // Change $app_config to $uploadDir

// Only process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Turn off output buffering
    if (ob_get_level())
        ob_end_clean();

    // Set header for JSON response
    if ($isAjax) {
        header('Content-Type: application/json');
    }

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

    // Get author information
    $author_name = $_POST['author_name'] ?? null;
    $authors_json = $_POST['authors_json'] ?? null;
    $authors = [];

    if (!empty($authors_json)) {
        $authors = json_decode($authors_json, true);
    } elseif (!empty($author_name)) {
        $authors[] = $author_name;
    }

    $formData['authors'] = $authors;

    // Get genre information
    $genre_id = $_POST['genre_id'] ?? null;
    $genres_json = $_POST['genres_json'] ?? null;
    $genres = [];

    if (!empty($genres_json)) {
        $genres = json_decode($genres_json, true);
    } elseif (!empty($genre_id)) {
        $genres[] = $genre_id;
    }

    $formData['genres'] = $genres;

      // Check if the required fields are filled
      if ($formData['title'] && $formData['category_id']) {
        try {
            // Create the product
            $product_id = createProduct($formData, $pdo); // Get the product ID

            if ($product_id) {
                // Process image uploads after product creation
                // $_FILES['item_images'] will contain an array of files due to name="item_images[]"
                $imageUploadResult = $imageProcessor->uploadProductImages($_FILES['item_images'], $product_id);

                $successMsg = "Product added successfully!";
                if (!$imageUploadResult['success']) {
                    $successMsg .= " However, " . $imageUploadResult['message']; // Append image upload errors
                    if (!empty($imageUploadResult['errors'])) {
                        $successMsg .= " Details: " . implode(", ", $imageUploadResult['errors']); // Show detailed image errors
                    }
                }

                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => $successMsg]);
                    exit();
                } else {
                    $_SESSION['message'] = $successMsg;
                    header('Location: admin.php?tab=addproduct');
                    exit();
                }
            } else {
                throw new Exception("Failed to create product.");
            }
        } catch (Exception $e) {
            $errorMsg = "An error occurred: " . $e->getMessage();
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => $errorMsg]);
                exit();
            } else {
                $_SESSION['error_message'] = $errorMsg;
                header('Location: admin.php?tab=addproduct');
                exit();
            }
        }
    } else {
        $errorMsg = "Please fill in the required field: Title.";
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => $errorMsg]);
            exit();
        } else {
            $_SESSION['error_message'] = $errorMsg;
            header('Location: admin.php?tab=addproduct');
            exit();
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .zindex-dropdown {
            z-index: 1000; /* Ensure dropdown is above other elements */
        }
        /* Style for image preview container for multiple images */
        .item-image-previews {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Space between images */
            margin-bottom: 15px;
        }
        .item-image-previews img {
            max-width: 100px; /* Smaller thumbnails */
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            padding: 2px;
        }
        .item-image-previews img.default-preview {
            max-width: 100%; /* Default image can be larger */
            height: auto;
        }
    </style>
</head>
<body>

<div class="tab-pane fade show active" id="add">
    <form id="add-item-form" method="POST" action="" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Produktbilder</h5> <div class="item-image-previews mb-3" id="item-image-previews">
                            <img src="assets/images/default_antiqe_image.webp" alt="Default Produktbild" class="img-fluid rounded shadow default-preview"
                                id="default-image-preview">
                        </div>
                        <div class="mb-3">
                            <label for="item-image-upload" class="form-label">Ladda upp bilder</label> <input class="form-control" type="file" id="item-image-upload" name="item_images[]" multiple accept="image/*"> <small class="form-text text-muted">Välj en eller flera bilder (Max <?php echo ($app_config['uploads']['max_size'] / (1024 * 1024)); ?>MB per bild, <?php echo strtoupper(implode(', ', $app_config['uploads']['allowed_extensions'])); ?>)</small>
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
                                <label for="item-title" class="form-label">Titel</label>
                                <input type="text" class="form-control" id="item-title" name="title" required>
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

                        <div class="mb-3">
                            <label class="form-label">Författare</label>
                            <div class="selected-authors mb-2">
                                <em class="text-muted">Ingen författare vald</em>
                            </div>

                            <div class="row">
                                <div class="col-md-12 position-relative mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="author-name" name="author_name"
                                            autocomplete="off" placeholder="Ange författarens namn">
                                        <button type="button" class="btn btn-outline-secondary" id="add-author-btn">
                                            <i class="fas fa-plus"></i> Lägg till
                                        </button>
                                    </div>
                                    <div id="suggest-author" class="list-group position-absolute w-100 zindex-dropdown"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="item-category" class="form-label">Kategori</label>
                                <select class="form-select" id="item-category" name="category_id" required>
                                    <option value="">Välj Kategori</option>
                                    <?php renderInputAlternatives($pdo, 'category', 'category_id', 'category', '', $locale); ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="item-genre" class="form-label">Genre</label>
                                <select class="form-select" id="item-genre" name="genre_id">
                                    <option value="">Välj Genre</option>
                                    <?php renderInputAlternatives($pdo, 'genre', 'genre_id', 'genre', '', $locale); ?>
                                </select>
                                <div class="selected-genres mt-2"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="add-genre-btn">
                                    <i class="fas fa-plus"></i> Lägg till genre
                                </button>
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
                                <input type="number" class="form-control" id="item-year" name="year" min="1900"
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
                            <label for="item-internal-notes" class="form-label">Intern beskrivning (endast för
                                personal)</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes"
                                rows="2"></textarea>
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

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">Rensa</button>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>

                        <input type="hidden" id="authors-json" name="authors_json" value="">
                        <input type="hidden" id="genres-json" name="genres_json" value="">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="assets/js/image-handler.js"></script>

</body>
</html>