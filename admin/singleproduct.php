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


// Page title
$pageTitle = "Redigera: " . htmlspecialchars($product->title) . " - Admin";

// Include admin header
include '../templates/admin_header.php';
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

        <!-- Item Details Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Grundinformation</h5>
                    <form id="edit-item-form" method="post" action="">
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

                        <div class="mb-3">
                            <label class="form-label">Författare</label>
                            <div class="selected-authors mb-2">
                                <?php if (!empty($product->authors)): ?>
                                    <?php foreach ($product->authors as $author): ?>
                                        <div class="selected-author badge bg-secondary p-2 me-2 mb-2">
                                            <?php echo htmlspecialchars($author->author_name); ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <em class="text-muted">Ingen författare vald</em>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <select class="form-select" id="edit-authors" name="edit-authors[]" multiple>
                                        <?php foreach ($authors as $author): ?>
                                            <option value="<?php echo $author['author_id']; ?>" 
                                                <?php echo in_array($author['author_id'], array_map(function($a) { return $a->author_id; }, $product->authors ?? [])) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($author['author_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="new-author-name" name="new-author-name" placeholder="Lägg till ny författare">
                                        <button type="button" class="btn btn-outline-secondary" id="add-author-btn">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
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
                                <label for="edit-genres" class="form-label">Genrer</label>
                                <select class="form-select" id="edit-genres" name="edit-genres[]" multiple>
                                    <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo $genre['genre_id']; ?>" <?php echo in_array($genre['genre_id'], $product->genre_ids_array ?? []) ? 'selected' : ''; ?>>
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
                                <select class="form-select" id="edit-language" name="edit-language">
                                    <option value="">Välj språk</option>
                                    <?php foreach ($languages as $language): ?>
                                    <option value="<?php echo $language['language_id']; ?>" <?php echo ($product->language_id == $language['language_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($language['language_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
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
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-special-price" name="edit-special-price" <?php echo ($product->special_price == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="edit-special-price">
                                        Speciellt pris
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-rare" name="edit-rare" <?php echo ($product->rare == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="edit-rare">
                                        Sällsynt objekt
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-recommended" name="edit-recommended" <?php echo ($product->recommended == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="edit-recommended">
                                        Rekommenderad
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
</div>



<script>
    
    // Add new author button
    const addAuthorBtn = document.getElementById('add-author-btn');
    if (addAuthorBtn) {
        addAuthorBtn.addEventListener('click', function() {
            const newAuthorInput = document.getElementById('new-author-name');
            const authorName = newAuthorInput.value.trim();
            
            if (authorName) {
                // Add to the visual list
                const selectedAuthorsDiv = document.querySelector('.selected-authors');
                const newAuthorBadge = document.createElement('div');
                newAuthorBadge.className = 'selected-author badge bg-success p-2 me-2 mb-2';
                newAuthorBadge.textContent = authorName;
                
                // Remove "no author" message if it exists
                const noAuthorMessage = selectedAuthorsDiv.querySelector('em');
                if (noAuthorMessage) {
                    selectedAuthorsDiv.removeChild(noAuthorMessage);
                }
                
                selectedAuthorsDiv.appendChild(newAuthorBadge);
                
                // Clear the input
                newAuthorInput.value = '';
            }
        });
    }
    </script>


