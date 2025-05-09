<?php
/**
 * Add Product
 * 
 * Contains:
 * - Product creation form
 * 
 * Functions:
 * - createProduct()
 * - uploadImage()
 * - renderInputAlternatives()
 */

require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

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
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $status_id = isset($_POST['status_id']) ? $_POST['status_id'] : null;
    $shelf_id = isset($_POST['shelf_id']) ? $_POST['shelf_id'] : null;
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $condition_id = isset($_POST['condition_id']) ? $_POST['condition_id'] : null;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
    $internal_notes = isset($_POST['internal_notes']) ? $_POST['internal_notes'] : null;
    $language = isset($_POST['language']) ? $_POST['language'] : null;
    $year = isset($_POST['year']) ? $_POST['year'] : null;
    $publisher = isset($_POST['publisher']) ? $_POST['publisher'] : null;
    $special_price = isset($_POST['special_price']) ? 1 : 0; // Checkbox
    $rare = isset($_POST['rare']) ? 1 : 0; // Checkbox

    // Get author information - single author from fields
    $author_first = isset($_POST['author_first']) ? $_POST['author_first'] : null;
    $author_last = isset($_POST['author_last']) ? $_POST['author_last'] : null;

    // Get author information - multiple authors from JSON
    $authors_json = isset($_POST['authors_json']) ? $_POST['authors_json'] : null;
    $authors = [];

    if (!empty($authors_json)) {
        $authors = json_decode($authors_json, true);
    }
    // If no authors in JSON but fields are filled, use those
    elseif (!empty($author_first) || !empty($author_last)) {
        $authors[] = [
            'first_name' => $author_first,
            'last_name' => $author_last
        ];
    }

    // Get genre information
    $genre_id = isset($_POST['genre_id']) ? $_POST['genre_id'] : null;
    $genres_json = isset($_POST['genres_json']) ? $_POST['genres_json'] : null;
    $genres = [];

    if (!empty($genres_json)) {
        $genres = json_decode($genres_json, true);
    }
    // If no genres in JSON but genre_id is selected, use that
    elseif (!empty($genre_id)) {
        $genres[] = [
            'genre_id' => $genre_id
        ];
    }

    // Check if the required field (title) is filled
    if ($title) {
        $imagePath = 'assets/images/product-image-placeholder.png'; // default path / placeholder image

        // Handle file upload
        if (isset($_FILES['item-image']) && $_FILES['item-image']['error'] === UPLOAD_ERR_OK) {

            $fileTmpPath = $_FILES['item-image']['tmp_name'];
            $fileName = $_FILES['item-image']['name'];
            $uploadFileDir = '../assets/uploads/';
            $dest_path = $uploadFileDir . basename($fileName);

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imagePath = 'assets/uploads/' . basename($fileName); // Store relative path for database insertion
            } else {
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => "Error moving the uploaded file."]);
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error moving the uploaded file.";
                    header('Location: admin.php?tab=addproduct');
                    exit();
                }
            }
        }

        try {
            // Start transaction to ensure data integrity
            $pdo->beginTransaction();

            
            $stmt = $pdo->prepare("INSERT INTO product (title, status, shelf_id, category_id, price, condition_id, notes, internal_notes, language, year, publisher, special_price, rare, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $status_id, $shelf_id, $category_id, $price, $condition_id, $notes, $internal_notes, $language, $year, $publisher, $special_price, $rare, $imagePath]);

            // Get the inserted product's ID
            $product_id = $pdo->lastInsertId();

            // Process authors
            if (!empty($authors)) {
                foreach ($authors as $author) {
                    $first_name = $author['first_name'] ?? '';
                    $last_name = $author['last_name'] ?? '';

                    if (empty($first_name) && empty($last_name)) {
                        continue; // Skip empty authors
                    }

                    // Check if author already exists
                    $stmt = $pdo->prepare("SELECT author_id FROM author WHERE first_name = ? AND last_name = ?");
                    $stmt->execute([$first_name, $last_name]);
                    $existing_author = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existing_author) {
                        $author_id = $existing_author['author_id'];
                    } else {
                        // Create new author
                        $stmt = $pdo->prepare("INSERT INTO author (first_name, last_name) VALUES (?, ?)");
                        $stmt->execute([$first_name, $last_name]);
                        $author_id = $pdo->lastInsertId();
                    }

                    // Create product_author relationship
                    $stmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (?, ?)");
                    $stmt->execute([$product_id, $author_id]);
                }
            }

             // Process genres
             if (!empty($genres)) {
                foreach ($genres as $genre) {
                    $genre_id = $genre['genre_id'] ?? null;
                    
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

            // Return success response
            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => "Product added successfully!"]);
                exit();
            } else {
                $_SESSION['message'] = "Product added successfully!";
                header('Location: admin.php?tab=addproduct');
                exit();
            }
        } catch (PDOException $e) {
            // Roll back transaction if error
            $pdo->rollBack();

            error_log($e->getMessage()); // Log the error
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => "An error occurred: " . $e->getMessage()]);
                exit();
            } else {
                $_SESSION['error_message'] = "An error occurred. Please try again.";
                header('Location: admin.php?tab=addproduct');
                exit();
            }
        }
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => "Please fill in the required field: Title."]);
            exit();
        } else {
            $_SESSION['error_message'] = "Please fill in the required field: Title.";
            header('Location: admin.php?tab=addproduct');
            exit();
        }
    }
}
?>

<div class="tab-pane fade show active" id="add">
    <form id="add-item-form" method="POST" action="" enctype="multipart/form-data">
        <div class="row">
            <!-- Image upload -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Produktbild</h5>
                        <div class="item-image-container mb-3">
                            <img src="assets/images/src-book.webp" alt="Produktbild" class="img-fluid rounded shadow"
                                id="new-item-image">
                        </div>
                        <div class="mb-3">
                            <label for="item-image-upload" class="form-label">Ladda upp bild</label>
                            <input class="form-control" type="file" id="item-image-upload" name="item-image">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product details -->
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
                                <select class="form-select" id="item-status" name="status_id" required>
                                    <option value="">Välj Status</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT status_id, status_name FROM `status`");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['status_id']) . '">' . htmlspecialchars($row['status_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 position-relative">
                                <label for="author-first" class="form-label">Författare förnamn</label>
                                <input type="text" class="form-control" id="author-first" name="author_first"
                                    autocomplete="off">
                                <div id="suggest-author-first"
                                    class="list-group position-absolute w-100 zindex-dropdown"></div>
                            </div>
                            <div class="col-md-6 position-relative">
                                <label for="author-last" class="form-label">Författare efternamn</label>
                                <input type="text" class="form-control" id="author-last" name="author_last"
                                    autocomplete="off">
                                <div id="suggest-author-last"
                                    class="list-group position-absolute w-100 zindex-dropdown"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="item-category" class="form-label">Kategori</label>
                                <select class="form-select" id="item-category" name="category_id" required>
                                    <option value="">Välj Kategori</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT category_id, category_name FROM `category`");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="item-genre" class="form-label">Genre</label>
                                <select class="form-select" id="item-genre" name="genre_id">
                                    <option value="">Välj Genre</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT genre_id, genre_name FROM `genre`");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['genre_id']) . '">' . htmlspecialchars($row['genre_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-price" class="form-label">Pris (€)</label>
                                <input type="number" step="0.01" class="form-control" id="item-price" name="price">
                            </div>
                            <div class="col-md-4">
                                <label for="item-condition" class="form-label">Skick</label>
                                <select class="form-select" id="item-condition" name="condition_id" required>
                                    <option value="">Välj Skick</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT condition_id, condition_name FROM `condition`");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['condition_id']) . '">' . htmlspecialchars($row['condition_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-shelf" class="form-label">Hylla</label>
                                <select class="form-select" id="item-shelf" name="shelf_id" required>
                                    <option value="">Välj Hylla</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT shelf_id, shelf_name FROM `shelf`");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['shelf_id']) . '">' . htmlspecialchars($row['shelf_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="item-language" class="form-label">Språk</label>
                                <select class="form-select" id="item-language" name="language">
                                    <option value="">Välj Språk</option>
                                    <option value="english">English</option>
                                    <option value="svenska">Svenska</option>
                                    <option value="suomi">Suomi</option>
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
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-special-price"
                                        name="special_price" value="1">
                                    <label class="form-check-label" for="item-special-price">Special Pris</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="item-rare" name="rare"
                                        value="1">
                                    <label class="form-check-label" for="item-rare">Raritet</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">Rensa</button>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </form>

</div>