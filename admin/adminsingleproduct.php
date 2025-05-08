<?php
/**
 * Edit Product
 * 
 * Contains:
 * - Product editing form
 * - Product deletion functionality
 * 
 * Functions:
 * - getProductById() - Gets product data by ID
 * - editProduct() - Updates product data
 * - deleteProduct() - Removes a product
 * - uploadImage() - Handles product image uploads
 */

// Include necessary files
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/db_functions.php';
require_once '../includes/auth.php';
require_once '../includes/ui.php';

// Check authentication - requires admin or editor role
checkAuth(2); // Role 2 (Editor) or above required

// Initialize variables
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$errorMessage = '';
$successMessage = '';
$currentUser = getSessionUser();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which action was requested
    if (isset($_POST['save-item'])) {
        // Edit product
        $result = editProduct($productId, $_POST, $_FILES);
        if ($result['success']) {
            $successMessage = $result['message'];
            // Refresh product data after update
            $product = getProductById($productId);
        } else {
            $errorMessage = $result['message'];
        }
    } elseif (isset($_POST['delete-item'])) {
        // Delete product
        $result = deleteProduct($productId);
        if ($result['success']) {
            // Redirect to admin page after successful deletion
            header('Location: ../admin.php?tab=search&message=' . urlencode($result['message']));
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    }
}

// Get product data if ID is provided
if ($productId > 0) {
    $product = getProductById($productId);
    
    // If product not found, redirect to admin page
    if (!$product) {
        header('Location: ../admin.php?tab=search&error=' . urlencode('Produkt hittades inte.'));
        exit;
    }
} else {
    // No product ID provided, redirect to admin page
    header('Location: ../admin.php?tab=search&error=' . urlencode('Ingen produkt specificerad.'));
    exit;
}

// Get categories, genres, shelves, conditions, and statuses for form dropdowns
$categories = getCategoriesForDropdown($pdo);
$genres = getGenresForDropdown($pdo);
$shelves = getShelvesForDropdown($pdo);
$conditions = getConditionsForDropdown($pdo);
$statuses = getStatusesForDropdown($pdo);

// Get transaction history for this product
$transactions = getProductTransactions($productId);

// Page title
$pageTitle = "Redigera: " . htmlspecialchars($product->title) . " - Admin";

// Include admin header
include '../templates/admin_header.php';

/**
 * Gets complete product data by ID
 * 
 * @param int $productId Product ID to fetch
 * @return object|null Product data or null if not found
 */
function getProductById($productId) {
    global $pdo;
    
    try {
        // SQL to fetch product with related data
        $sql = "SELECT
                p.*,
                s.status_name,
                cat.category_name,
                sh.shelf_name,
                con.condition_name,
                con.condition_code,
                GROUP_CONCAT(DISTINCT a.author_id SEPARATOR ',') AS author_ids,
                GROUP_CONCAT(DISTINCT a.first_name SEPARATOR '|') AS author_first_names,
                GROUP_CONCAT(DISTINCT a.last_name SEPARATOR '|') AS author_last_names,
                GROUP_CONCAT(DISTINCT g.genre_id SEPARATOR ',') AS genre_ids,
                GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ',') AS genre_names
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
            $product->author_ids_array = !empty($product->author_ids) ? explode(',', $product->author_ids) : [];
            $product->author_first_names_array = !empty($product->author_first_names) ? explode('|', $product->author_first_names) : [];
            $product->author_last_names_array = !empty($product->author_last_names) ? explode('|', $product->author_last_names) : [];
            $product->genre_ids_array = !empty($product->genre_ids) ? explode(',', $product->genre_ids) : [];
            $product->genre_names_array = !empty($product->genre_names) ? explode(',', $product->genre_names) : [];
        }
        
        return $product;
    } catch (PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
        return null;
    }
}

/**
 * Updates product data
 * 
 * @param int $productId Product ID to update
 * @param array $formData Form data from POST
 * @param array $fileData File upload data
 * @return array Result with success flag and message
 */
function editProduct($productId, $formData, $fileData) {
    global $pdo;
    
    try {
        // Start transaction for data integrity
        $pdo->beginTransaction();
        
        // Extract form data
        $title = trim($formData['edit-title'] ?? '');
        $status = (int)($formData['edit-status'] ?? 1);
        $category = (int)($formData['edit-category'] ?? 0);
        $shelf = (int)($formData['edit-shelf'] ?? 0);
        $price = (float)($formData['edit-price'] ?? 0.00);
        $condition = (int)($formData['edit-condition'] ?? 0);
        $notes = trim($formData['edit-notes'] ?? '');
        $internalNotes = trim($formData['edit-internal-notes'] ?? '');
        $language = trim($formData['edit-language'] ?? '');
        $year = (int)($formData['edit-year'] ?? 0);
        $publisher = trim($formData['edit-publisher'] ?? '');
        $specialPrice = isset($formData['edit-special-price']) ? 1 : 0;
        $rare = isset($formData['edit-rare']) ? 1 : 0;
        
        // Author information
        $authorFirst = trim($formData['edit-author-first'] ?? '');
        $authorLast = trim($formData['edit-author-last'] ?? '');
        
        // Genre information
        $genre = (int)($formData['edit-genre'] ?? 0);
        
        // Image handling
        $imagePath = null;
        if (isset($fileData['item-image-upload']) && $fileData['item-image-upload']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($fileData['item-image-upload'], $productId);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            }
        }
        
        // Validate required fields
        if (empty($title)) {
            return ['success' => false, 'message' => 'Titel är ett obligatoriskt fält.'];
        }
        
        if ($category <= 0) {
            return ['success' => false, 'message' => 'Kategori är ett obligatoriskt fält.'];
        }
        
        if ($price <= 0) {
            return ['success' => false, 'message' => 'Pris måste vara större än 0.'];
        }
        
        // Update product base information
        $sql = "UPDATE product SET 
                title = :title,
                status = :status,
                shelf_id = :shelf_id,
                category_id = :category_id,
                price = :price,
                condition_id = :condition_id,
                notes = :notes,
                internal_notes = :internal_notes,
                language = :language,
                year = :year,
                publisher = :publisher,
                special_price = :special_price,
                rare = :rare";
                
        // Only update image if a new one was uploaded
        if ($imagePath) {
            $sql .= ", image = :image";
        }
                
        $sql .= " WHERE prod_id = :prod_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':shelf_id', $shelf, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':condition_id', $condition, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':internal_notes', $internalNotes);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':special_price', $specialPrice, PDO::PARAM_INT);
        $stmt->bindParam(':rare', $rare, PDO::PARAM_INT);
        $stmt->bindParam(':prod_id', $productId, PDO::PARAM_INT);
        
        if ($imagePath) {
            $stmt->bindParam(':image', $imagePath);
        }
        
        $stmt->execute();
        
        // Handle author update if provided
        if (!empty($authorFirst) || !empty($authorLast)) {
            // First check if author exists
            $authorStmt = $pdo->prepare("SELECT author_id FROM author WHERE first_name = :first_name AND last_name = :last_name");
            $authorStmt->bindParam(':first_name', $authorFirst);
            $authorStmt->bindParam(':last_name', $authorLast);
            $authorStmt->execute();
            
            $authorId = $authorStmt->fetchColumn();
            
            if (!$authorId) {
                // Create new author
                $insertAuthorStmt = $pdo->prepare("INSERT INTO author (first_name, last_name) VALUES (:first_name, :last_name)");
                $insertAuthorStmt->bindParam(':first_name', $authorFirst);
                $insertAuthorStmt->bindParam(':last_name', $authorLast);
                $insertAuthorStmt->execute();
                
                $authorId = $pdo->lastInsertId();
            }
            
            // Clear existing authors for this product
            $deleteAuthorStmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = :product_id");
            $deleteAuthorStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $deleteAuthorStmt->execute();
            
            // Add new author relationship
            $insertRelationStmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (:product_id, :author_id)");
            $insertRelationStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $insertRelationStmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
            $insertRelationStmt->execute();
        }
        
        // Handle genre update if provided
        if ($genre > 0) {
            // Check if the product already has this genre
            $genreCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM product_genre WHERE product_id = :product_id AND genre_id = :genre_id");
            $genreCheckStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $genreCheckStmt->bindParam(':genre_id', $genre, PDO::PARAM_INT);
            $genreCheckStmt->execute();
            
            if ($genreCheckStmt->fetchColumn() == 0) {
                // Add new genre relationship
                $insertGenreStmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (:product_id, :genre_id)");
                $insertGenreStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $insertGenreStmt->bindParam(':genre_id', $genre, PDO::PARAM_INT);
                $insertGenreStmt->execute();
            }
        }
        
        // Log the update
        $currentUser = getSessionUser();
        $userId = $currentUser ? $currentUser['user_id'] : 1;
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description, product_id) 
            VALUES (:user_id, :event_type, :event_description, :product_id)
        ");
        
        $eventDescription = "Uppdaterade produkt: " . $title;
        
        $logStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $logStmt->bindValue(':event_type', 'update');
        $logStmt->bindParam(':event_description', $eventDescription);
        $logStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $logStmt->execute();
        
        // Commit transaction
        $pdo->commit();
        
        return [
            'success' => true, 
            'message' => 'Produkten har uppdaterats framgångsrikt.'
        ];
    } catch (PDOException $e) {
        // Roll back transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error updating product: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Ett fel inträffade: ' . $e->getMessage()
        ];
    }
}

/**
 * Deletes a product
 * 
 * @param int $productId Product ID to delete
 * @return array Result with success flag and message
 */
function deleteProduct($productId) {
    global $pdo;
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get product title for log
        $titleStmt = $pdo->prepare("SELECT title FROM product WHERE prod_id = :product_id");
        $titleStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $titleStmt->execute();
        $productTitle = $titleStmt->fetchColumn();
        
        // First delete related records
        
        // Delete author relationships
        $deleteAuthorsStmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = :product_id");
        $deleteAuthorsStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteAuthorsStmt->execute();
        
        // Delete genre relationships
        $deleteGenresStmt = $pdo->prepare("DELETE FROM product_genre WHERE product_id = :product_id");
        $deleteGenresStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteGenresStmt->execute();
        
        // Update event log to remove references to this product
        $updateLogStmt = $pdo->prepare("UPDATE event_log SET product_id = NULL WHERE product_id = :product_id");
        $updateLogStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $updateLogStmt->execute();
        
        // Now delete the product itself
        $deleteProductStmt = $pdo->prepare("DELETE FROM product WHERE prod_id = :product_id");
        $deleteProductStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteProductStmt->execute();
        
        // Log the deletion
        $currentUser = getSessionUser();
        $userId = $currentUser ? $currentUser['user_id'] : 1;
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description) 
            VALUES (:user_id, :event_type, :event_description)
        ");
        
        $eventDescription = "Raderade produkt: " . $productTitle;
        
        $logStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $logStmt->bindValue(':event_type', 'delete');
        $logStmt->bindParam(':event_description', $eventDescription);
        $logStmt->execute();
        
        // Try to delete the product image if it exists
        $imagePath = '../uploads/products/' . $productId . '.jpg';
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
        
        // Commit transaction
        $pdo->commit();
        
        return [
            'success' => true, 
            'message' => 'Produkten har raderats framgångsrikt.'
        ];
    } catch (PDOException $e) {
        // Roll back transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error deleting product: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Ett fel inträffade: ' . $e->getMessage()
        ];
    }
}

/**
 * Handles image upload for a product
 * 
 * @param array $file File upload data
 * @param int $productId Product ID
 * @return array Result with success flag and path
 */
function uploadImage($file, $productId) {
    // Define upload directory
    $uploadDir = '../uploads/products/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate filename using product ID
    $filename = $productId . '.jpg';
    $uploadPath = $uploadDir . $filename;
    
    // Check if file is an image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return [
            'success' => false, 
            'message' => 'Filen är inte en bild.'
        ];
    }
    
    // Check file size (5MB limit)
    if ($file['size'] > 5000000) {
        return [
            'success' => false, 
            'message' => 'Filen är för stor (max 5MB).'
        ];
    }
    
    // Allow only certain file formats
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        return [
            'success' => false, 
            'message' => 'Endast JPG, JPEG, PNG & GIF filer är tillåtna.'
        ];
    }
    
    // Try to upload the file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Return relative path for database storage
        return [
            'success' => true, 
            'path' => 'uploads/products/' . $filename
        ];
    } else {
        return [
            'success' => false, 
            'message' => 'Ett fel inträffade vid uppladdning av bilden.'
        ];
    }
}

/**
 * Gets categories for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Categories
 */
function getCategoriesForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets genres for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Genres
 */
function getGenresForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT genre_id, genre_name FROM genre ORDER BY genre_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching genres: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets shelves for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Shelves
 */
function getShelvesForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT shelf_id, shelf_name FROM shelf ORDER BY shelf_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching shelves: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets conditions for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Conditions
 */
function getConditionsForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT condition_id, condition_name, condition_code FROM `condition` ORDER BY condition_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching conditions: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets statuses for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Statuses
 */
function getStatusesForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT status_id, status_name FROM status ORDER BY status_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching statuses: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets transaction history for a product
 * 
 * @param int $productId Product ID
 * @return array Transaction history
 */
function getProductTransactions($productId) {
    global $pdo;
    
    try {
        $sql = "SELECT
                el.event_id,
                el.event_timestamp,
                el.event_type,
                el.event_description,
                u.user_username
            FROM
                event_log el
            LEFT JOIN
                user u ON el.user_id = u.user_id
            WHERE
                el.product_id = :product_id
            ORDER BY
                el.event_timestamp DESC";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching transaction history: " . $e->getMessage());
        return [];
    }
}
?>

<!-- Main Content Container -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Redigera: <?php echo htmlspecialchars($product->title); ?></h1>
        <div>
            <a href="../admin.php?tab=search" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Tillbaka till lagerhantering
            </a>
        </div>
    </div>
    
    <?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Item Image and Upload -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Objektbild</h5>
                    <div class="item-image-container mb-3">
                        <?php
                        // Check if product image exists
                        $imagePath = '../uploads/products/' . $product->prod_id . '.jpg';
                        $defaultImage = '../assets/images/src-book.webp'; // Default image
                        
                        // Adjust default image based on category
                        if ($product->category_id == 5) { // CD
                            $defaultImage = '../assets/images/src-cd.webp';
                        } elseif ($product->category_id == 6) { // Vinyl
                            $defaultImage = '../assets/images/src-vinyl.webp';
                        } elseif ($product->category_id == 7) { // DVD
                            $defaultImage = '../assets/images/src-dvd.webp';
                        } elseif ($product->category_id == 8) { // Comics/Magazines
                            $defaultImage = '../assets/images/src-magazine.webp';
                        }
                        
                        $imageToShow = file_exists($imagePath) ? $imagePath : $defaultImage;
                        
                        if (!empty($product->image) && file_exists('../' . $product->image)) {
                            $imageToShow = '../' . $product->image;
                        }
                        ?>
                        <img src="<?php echo $imageToShow; ?>" alt="<?php echo htmlspecialchars($product->title); ?>" class="img-fluid rounded shadow" id="item-image">
                    </div>
                    <form id="image-upload-form" method="post" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="item-image-upload" class="form-label">Ladda upp ny bild</label>
                            <input class="form-control" type="file" id="item-image-upload" name="item-image-upload" accept="image/*">
                        </div>
                        <button type="submit" name="upload-image" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Ladda upp bild
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Item Details Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Grundinformation</h5>
                    <form id="edit-item-form" method="post" action="" enctype="multipart/form-data">
                        <!-- Hidden ID field -->
                        <input type="hidden" id="item-id" name="item-id" value="<?php echo $product->prod_id; ?>">

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="edit-title" class="form-label">Titel</label>
                                <input type="text" class="form-control" id="edit-title" name="edit-title" value="<?php echo htmlspecialchars($product->title); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-status" class="form-label">Status</label>
                                <select class="form-select" id="edit-status" name="edit-status">
                                    <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status['status_id']; ?>" <?php echo ($product->status == $status['status_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($status['status_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-author-first" class="form-label">Författare förnamn</label>
                                <input type="text" class="form-control" id="edit-author-first" name="edit-author-first" 
                                    value="<?php echo htmlspecialchars($product->author_first_names_array[0] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="edit-author-last" class="form-label">Författare efternamn</label>
                                <input type="text" class="form-control" id="edit-author-last" name="edit-author-last"
                                    value="<?php echo htmlspecialchars($product->author_last_names_array[0] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-category" class="form-label">Kategori</label>
                                <select class="form-select" id="edit-category" name="edit-category" required>
                                    <option value="">Välj kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($product->category_id == $category['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-genre" class="form-label">Genre</label>
                                <select class="form-select" id="edit-genre" name="edit-genre">
                                    <option value="">Välj genre</option>
                                    <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo $genre['genre_id']; ?>" <?php echo (in_array($genre['genre_id'], $product->genre_ids_array ?? [])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit-price" class="form-label">Pris (€)</label>
                                <input type="number" step="0.01" class="form-control" id="edit-price" name="edit-price" value="<?php echo htmlspecialchars($product->price); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-condition" class="form-label">Skick</label>
                                <select class="form-select" id="edit-condition" name="edit-condition">
                                    <option value="">Välj skick</option>
                                    <?php foreach ($conditions as $condition): ?>
                                    <option value="<?php echo $condition['condition_id']; ?>" <?php echo ($product->condition_id == $condition['condition_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($condition['condition_name']); ?> (<?php echo htmlspecialchars($condition['condition_code']); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-shelf" class="form-label">Hylla</label>
                                <select class="form-select" id="edit-shelf" name="edit-shelf">
                                    <option value="">Välj hylla</option>
                                    <?php foreach ($shelves as $shelf): ?>
                                    <option value="<?php echo $shelf['shelf_id']; ?>" <?php echo ($product->shelf_id == $shelf['shelf_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($shelf['shelf_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit-language" class="form-label">Språk</label>
                                <input type="text" class="form-control" id="edit-language" name="edit-language" value="<?php echo htmlspecialchars($product->language ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="edit-year" class="form-label">År</label>
                                <input type="number" class="form-control" id="edit-year" name="edit-year" value="<?php echo htmlspecialchars($product->year ?? ''); ?>" min="1400" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="edit-publisher" class="form-label">Förlag</label>
                                <input type="text" class="form-control" id="edit-publisher" name="edit-publisher" value="<?php echo htmlspecialchars($product->publisher ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="edit-notes" class="form-label">Produktbeskrivning (synlig för kunder)</label>
                            <textarea class="form-control" id="edit-notes" name="edit-notes" rows="3"><?php echo htmlspecialchars($product->notes ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit-internal-notes" class="form-label">Intern beskrivning (endast för personal)</label>
                            <textarea class="form-control" id="edit-internal-notes" name="edit-internal-notes" rows="2"><?php echo htmlspecialchars($product->internal_notes ?? ''); ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-special-price" name="edit-special-price" <?php echo ($product->special_price == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="edit-special-price">
                                        Speciellt pris
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-rare" name="edit-rare" <?php echo ($product->rare == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="edit-rare">
                                        Sällsynt objekt
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-danger" id="delete-button" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                                <i class="fas fa-trash-alt me-1"></i> Ta bort objekt
                            </button>
                            <button type="submit" class="btn btn-primary" name="save-item">
                                <i class="fas fa-save me-1"></i> Spara ändringar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Transaktionshistorik</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="transaction-history-table">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Åtgärd</th>
                            <th>Användare</th>
                            <th>Beskrivning</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-history-body">
                        <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Ingen transaktionshistorik tillgänglig.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', strtotime($transaction['event_timestamp'])); ?></td>
                                <td>
                                    <?php 
                                    $eventTypeLabel = '';
                                    switch ($transaction['event_type']) {
                                        case 'create':
                                            $eventTypeLabel = '<span class="badge bg-success">Skapat</span>';
                                            break;
                                        case 'update':
                                            $eventTypeLabel = '<span class="badge bg-primary">Uppdaterat</span>';
                                            break;
                                        case 'delete':
                                            $eventTypeLabel = '<span class="badge bg-danger">Raderat</span>';
                                            break;
                                        default:
                                            $eventTypeLabel = '<span class="badge bg-secondary">' . ucfirst($transaction['event_type']) . '</span>';
                                    }
                                    echo $eventTypeLabel;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($transaction['user_username'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars($transaction['event_description']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteProductModalLabel">Bekräfta borttagning</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Är du säker på att du vill ta bort produkten <strong><?php echo htmlspecialchars($product->title); ?></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Denna åtgärd kan inte ångras! All data kopplad till denna produkt kommer att raderas permanent.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <form action="" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product->prod_id; ?>">
                    <button type="submit" name="delete-item" class="btn btn-danger">Ta bort permanent</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Preview image upload
    const imageInput = document.getElementById('item-image-upload');
    const imagePreview = document.getElementById('item-image');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Confirm deletion when form is submitted
    const deleteForm = document.querySelector('form[name="delete-item"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const confirmed = confirm('Är du verkligen säker på att du vill ta bort denna produkt? Denna åtgärd kan inte ångras!');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
});
</script>