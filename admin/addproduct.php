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

require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Safely echoes content with proper HTML escaping and NULL handling
 * 
 * @param mixed $content The content to echo
 * @return void
 */
function safeEcho($content) {
    echo htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8');
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
            $data['status_id'], 
            $data['shelf_id'], 
            $data['category_id'], 
            $data['price'], 
            $data['condition_id'], 
            $data['notes'], 
            $data['internal_notes'], 
            $data['language_id'], 
            $data['year'], 
            $data['publisher'], 
            $data['special_price'], 
            $data['rare'],
            $data['recommended']
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
        $localized_name_field = $name_field . '_' . $locale;
        
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
                error_log("Could not find column $localized_name_field or $name_field in table $table");
                
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
        error_log("SQL for $table: $sql");
        
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

// Get user locale preference (default to 'en')
$locale = $_SESSION['locale'] ?? 'en';

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
        'status_id' => $_POST['status_id'] ?? null,
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

    // Check if the required field (title) is filled
    if ($formData['title']) {
        try {
            // Create the product
            $result = createProduct($formData, $pdo);
            
            if ($result) {
                $successMsg = "Product added successfully!";
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
                                    <?php renderInputAlternatives($pdo, 'status', 'status_id', 'status', '', $locale); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 position-relative">
                                <label for="author-name" class="form-label">Författare</label>
                                <input type="text" class="form-control" id="author-name" name="author_name" 
                                    autocomplete="off" placeholder="Ange författarens namn">
                                <div id="suggest-author" class="list-group position-absolute w-100 zindex-dropdown"></div>
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
                                    <?php renderInputAlternatives($pdo, 'condition', 'condition_id', 'condition', '', $locale); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="item-shelf" class="form-label">Hylla</label>
                                <select class="form-select" id="item-shelf" name="shelf_id" required>
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

                        <!-- Hidden fields for JSON data -->
                        <input type="hidden" id="authors-json" name="authors_json" value="">
                        <input type="hidden" id="genres-json" name="genres_json" value="">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
/**
 * Initializes the add product form and its features
 * 
 * @return {void}
 */
document.addEventListener('DOMContentLoaded', function() {
    const authorInput = document.getElementById('author-name');
    const authorSuggestions = document.getElementById('suggest-author');
    const publisherInput = document.getElementById('item-publisher');
    const publisherSuggestions = document.getElementById('suggest-publisher');
    const imageUpload = document.getElementById('item-image-upload');
    const imagePreview = document.getElementById('new-item-image');
    const authorsJsonInput = document.getElementById('authors-json');
    const genresJsonInput = document.getElementById('genres-json');
    
    let authors = [];
    let genres = [];
    
    /**
     * Loads author suggestions based on user input
     * 
     * @param {string} query The search query
     * @return {void}
     */
    function loadAuthorSuggestions(query) {
        if (query.length < 2) {
            authorSuggestions.innerHTML = '';
            return;
        }
        
        fetch(`api/authors.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                authorSuggestions.innerHTML = '';
                data.forEach(author => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = author.author_name;
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        authorInput.value = author.author_name;
                        authors.push(author.author_name);
                        updateAuthorsJson();
                        authorSuggestions.innerHTML = '';
                    });
                    authorSuggestions.appendChild(item);
                });
            })
            .catch(error => console.error('Error fetching authors:', error));
    }
    
    /**
     * Updates the hidden JSON field for authors
     * 
     * @return {void}
     */
    function updateAuthorsJson() {
        authorsJsonInput.value = JSON.stringify(authors);
    }
    
    /**
     * Updates the hidden JSON field for genres
     * 
     * @return {void}
     */
    function updateGenresJson() {
        genresJsonInput.value = JSON.stringify(genres);
    }
    
    // Set up author input event listener
    authorInput.addEventListener('input', function() {
        loadAuthorSuggestions(this.value);
    });
    
    // Document click handler to close suggestion lists when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== authorInput && e.target !== authorSuggestions) {
            authorSuggestions.innerHTML = '';
        }
        if (e.target !== publisherInput && e.target !== publisherSuggestions) {
            publisherSuggestions.innerHTML = '';
        }
    });
    
    // Handle image preview
    imageUpload.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Genre selection handler
    document.getElementById('item-genre').addEventListener('change', function() {
        if (this.value) {
            const genreId = this.value;
            const genreName = this.options[this.selectedIndex].text;
            
            // Add to genres array if not already present
            if (!genres.includes(genreId)) {
                genres.push(genreId);
                updateGenresJson();
            }
        }
    });
});
</script>