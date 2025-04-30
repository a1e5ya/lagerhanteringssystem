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

require_once '../config/config.php'; // Adjust the path as necessary

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Only process POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Turn off output buffering
    if (ob_get_level()) ob_end_clean();
    
    // Set header for JSON response
    if ($isAjax) {
        header('Content-Type: application/json');
    }
    
    // Collect form data
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

    // Check if the required field (title) is filled
    if ($title) {
        $imagePath = 'assets/images/product-image-placeholder.png'; // Default image path

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
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO product (title, status, shelf_id, category_id, price, condition_id, notes, internal_notes, language, year, publisher, special_price, rare, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $status_id, $shelf_id, $category_id, $price, $condition_id, $notes, $internal_notes, $language, $year, $publisher, $special_price, $rare, $imagePath]);

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
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Produktbild</h5>
                        <div class="item-image-container mb-3">
                            <img src="assets/images/src-book.webp" alt="Produktbild" class="img-fluid rounded shadow" id="new-item-image">
                        </div>
                        <div class="mb-3">
                            <label for="item-image-upload" class="form-label">Ladda upp bild</label>
                            <input class="form-control" type="file" id="item-image-upload" name="item-image">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Grundinformation</h5>
                        <div class="mb-3">
                            <label for="item-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="item-title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="item-status" class="form-label">Status</label>
                            <select class="form-select" id="item-status" name="status_id" required>
                                <option value="">Select Status</option>
                                <?php
                                // Fetch status from the database
                                $stmt = $pdo->query("SELECT status_id, status_name FROM `status`");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['status_id']) . '">' . htmlspecialchars($row['status_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-shelf" class="form-label">Shelf</label>
                            <select class="form-select" id="item-shelf" name="shelf_id" required>
                                <option value="">Select Shelf</option>
                                <?php
                                // Fetch categories from the database
                                $stmt = $pdo->query("SELECT shelf_id, shelf_name FROM `shelf`");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['shelf_id']) . '">' . htmlspecialchars($row['shelf_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-category" class="form-label">Category</label>
                            <select class="form-select" id="item-category" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                // Fetch categories from the database
                                $stmt = $pdo->query("SELECT category_id, category_name FROM `category`");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-price" class="form-label">Price (€)</label>
                            <input type="number" step="0.01" class="form-control" id="item-price" name="price">
                        </div>

                        <div class="mb-3">
                            <label for="item-condition" class="form-label">Condition</label>
                            <select class="form-select" id="item-condition" name="condition_id" required>
                                <option value="">Select Condition</option>
                                <?php
                                // Fetch conditions from the database
                                $stmt = $pdo->query("SELECT condition_id, condition_name FROM `condition`");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['condition_id']) . '">' . htmlspecialchars($row['condition_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="item-notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-internal-notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control" id="item-internal-notes" name="internal_notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="item-language" class="form-label">Language</label>
                            <select class="form-select" id="item-language" name="language">
                                <option value="">Select Language</option>
                                <option value="english">English</option>
                                <option value="svenska">Svenska</option>
                                <option value="suomi">Suomi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="item-year" name="year" min="1900" max="<?php echo date('Y'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="item-publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="item-publisher" name="publisher">
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" id="item-special-price" name="special_price" value="1">
                            <label class="form-check-label" for="item-special-price">Special Price</label>
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" id="item-rare" name="rare" value="1">
                            <label class="form-check-label" for="item-rare">Rare</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>