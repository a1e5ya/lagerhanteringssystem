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
$categories = getCategoriesForDropdown($pdo, 'sv');
$genres = getGenresForDropdown($pdo, 'sv');
$shelves = getShelvesForDropdown($pdo, 'sv');
$conditions = getConditionsForDropdown($pdo, 'sv');
$statuses = getStatusesForDropdown($pdo, 'sv');
$languages = getLanguagesForDropdown($pdo, 'sv');
$authors = getAuthorsForDropdown($pdo);

// Get transaction history for this product
$transactions = getProductTransactions($productId);

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
        <!-- Item Images and Upload -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Produktbilder</h5>
                    
                    <!-- Image Gallery -->
                    <div class="image-gallery mb-3">
                        <?php if (empty($productImages)): ?>
                            <?php
                            // Check if legacy product image exists
                            $legacyImagePath = '../uploads/products/' . $product->prod_id . '.jpg';
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
                            
                            $imageToShow = file_exists($legacyImagePath) ? $legacyImagePath : $defaultImage;
                            ?>
                            <div class="text-center mb-3">
                                <img src="<?php echo $imageToShow; ?>" alt="<?php echo htmlspecialchars($product->title); ?>" class="img-fluid rounded shadow product-image">
                            </div>
                        <?php else: ?>
                            <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($productImages as $index => $image): ?>
                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                            <img src="../<?php echo htmlspecialchars($image->image_path); ?>" class="d-block w-100 rounded product-image" alt="<?php echo htmlspecialchars($product->title); ?>">
                                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                                <p class="mb-0"><?php echo $image->is_primary ? 'Primär bild' : 'Bild ' . ($index + 1); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($productImages) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Föregående</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Nästa</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Thumbnails -->
                            <?php if (count($productImages) > 1): ?>
                                <div class="d-flex flex-wrap justify-content-center mt-2">
                                    <?php foreach ($productImages as $index => $image): ?>
                                        <div class="image-thumbnail m-1 <?php echo $image->is_primary ? 'primary-thumbnail' : ''; ?>" data-bs-target="#productImageCarousel" data-bs-slide-to="<?php echo $index; ?>">
                                            <img src="../<?php echo htmlspecialchars($image->image_path); ?>" class="img-thumbnail" alt="Thumbnail">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Image Management -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manageImagesModal">
                                        <i class="fas fa-cog me-1"></i> Hantera bilder
                                    </button>
                                    <form method="post" action="" class="d-inline-block">
                                        <input type="hidden" name="delete_all_images" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Är du säker på att du vill ta bort alla bilder?');">
                                            <i class="fas fa-trash-alt me-1"></i> Ta bort alla
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Image Upload Form -->
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

<!-- Manage Images Modal -->
<div class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageImagesModalLabel">Hantera produktbilder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="manage-images-form">
                    <div class="row">
                        <?php foreach ($productImages as $image): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="../<?php echo htmlspecialchars($image->image_path); ?>" class="card-img-top" alt="Produktbild">
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input primary-image-radio" type="radio" name="primary_image" id="primary_<?php echo $image->image_id; ?>" value="<?php echo $image->image_id; ?>" <?php echo $image->is_primary ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="primary_<?php echo $image->image_id; ?>">
                                                Primär bild
                                            </label>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveImage(<?php echo $image->image_id; ?>, 'up')">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveImage(<?php echo $image->image_id; ?>, 'down')">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteImage(<?php echo $image->image_id; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
                <button type="button" class="btn btn-primary" onclick="saveImageChanges()">Spara ändringar</button>
            </div>
        </div>
    </div>
</div>

<script>
// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize multiple select plugins
    if (typeof($.fn.select2) !== 'undefined') {
        $('#edit-authors').select2({
            placeholder: 'Välj författare',
            allowClear: true
        });
        
        $('#edit-genres').select2({
            placeholder: 'Välj genrer',
            allowClear: true
        });
    }
    
    // Preview image upload
    const imageInput = document.getElementById('item-image-upload');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Auto-submit form when file is selected
                document.getElementById('image-upload-form').submit();
            }
        });
    }
    
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
    
    // Confirm deletion when delete form is submitted
    const deleteForm = document.querySelector('form[name="delete-item"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const confirmed = confirm('Är du verkligen säker på att du vill ta bort denna produkt? Denna åtgärd kan inte ångras!');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
    
    // Image thumbnail click events
    const thumbnails = document.querySelectorAll('.image-thumbnail');
    thumbnails.forEach(function(thumbnail, index) {
        thumbnail.addEventListener('click', function() {
            const carousel = new bootstrap.Carousel(document.getElementById('productImageCarousel'));
            carousel.to(index);
        });
    });
});

/**
 * Image management functions
 */

// Move image up or down in display order
function moveImage(imageId, direction) {
    // This would be handled via AJAX in a full implementation
    console.log(`Move image ${imageId} ${direction}`);
    
    // For demo purposes, just show a message
    alert(`Funktionen för att flytta bild ${direction === 'up' ? 'uppåt' : 'nedåt'} är inte implementerad i denna demo.`);
}

// Delete a specific image
function deleteImage(imageId) {
    if (confirm('Är du säker på att du vill ta bort denna bild?')) {
        // This would be handled via AJAX in a full implementation
        console.log(`Delete image ${imageId}`);
        
        // For demo purposes, just show a message
        alert('Funktionen för att ta bort enskilda bilder är inte implementerad i denna demo.');
    }
}

// Save image changes (order and primary image)
function saveImageChanges() {
    // This would be handled via AJAX in a full implementation
    console.log('Save image changes');
    
    // Get the selected primary image
    const primaryImageRadio = document.querySelector('input[name="primary_image"]:checked');
    const primaryImageId = primaryImageRadio ? primaryImageRadio.value : null;
    
    console.log(`Set primary image to ${primaryImageId}`);
    
    // For demo purposes, just show a message
    alert('Ändringar sparade!');
    
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('manageImagesModal'));
    if (modal) {
        modal.hide();
    }
    
    // Refresh the page to show changes
    location.reload();
}
</script>

<style>
/* Custom styles for this page */
.product-image {
    max-height: 300px;
    object-fit: contain;
}

.image-thumbnail {
    width: 60px;
    height: 60px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.image-thumbnail:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

.image-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.primary-thumbnail {
    border: 2px solid #0d6efd;
}

.selected-author {
    display: inline-block;
}
</style>

<?php
// Include admin footer
include '../templates/admin_footer.php';
?><?php
/**
 * Edit Product
 * 
 * Contains:
 * - Product editing form
 * - Product deletion functionality
 * - Multiple author/genre support
 * - Multiple image support
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

// Get current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

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
    } elseif (isset($_POST['upload-image'])) {
        // Handle image upload
        if (isset($_FILES['item-image-upload']) && $_FILES['item-image-upload']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['item-image-upload'], $productId);
            if ($uploadResult['success']) {
                $successMessage = 'Bild har laddats upp framgångsrikt.';
                // Refresh product data after image upload
                $product = getProductById($productId);
            } else {
                $errorMessage = $uploadResult['message'];
            }
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

// Get categories, genres, shelves, conditions, statuses, and languages for form dropdowns
$categories = getCategoriesForDropdown($pdo, $language);
$genres = getGenresForDropdown($pdo, $language);
$shelves = getShelvesForDropdown($pdo, $language);
$conditions = getConditionsForDropdown($pdo, $language);
$statuses = getStatusesForDropdown($pdo, $language);
$languages = getLanguagesForDropdown($pdo, $language);
$authors = getAuthorsForDropdown($pdo);

// Get transaction history for this product
$transactions = getProductTransactions($productId);

// Get product images
$productImages = getProductImages($productId);

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
    global $pdo, $language;
    
    // Debug
    error_log("Attempting to get product with ID: $productId");
    
    // Determine which field to use based on language
    $categoryNameField = ($language === 'fi') ? 'cat.category_fi_name' : 'cat.category_sv_name';
    $shelfNameField = ($language === 'fi') ? 'sh.shelf_fi_name' : 'sh.shelf_sv_name';
    $statusNameField = ($language === 'fi') ? 's.status_fi_name' : 's.status_sv_name';
    $conditionNameField = ($language === 'fi') ? 'con.condition_fi_name' : 'con.condition_sv_name';
    
    try {
        // SQL to fetch product with related data
        $sql = "SELECT
                p.*,
                {$statusNameField} as status_name,
                s.status_id,
                {$categoryNameField} as category_name,
                IFNULL({$shelfNameField}, '') as shelf_name,
                IFNULL({$conditionNameField}, '') as condition_name,
                IFNULL(con.condition_code, '') as condition_code,
                IFNULL(lang.language_sv_name, '') as language_name
            FROM
                product p
            JOIN
                category cat ON p.category_id = cat.category_id
            JOIN
                `status` s ON p.status = s.status_id
            LEFT JOIN
                shelf sh ON p.shelf_id = sh.shelf_id
            LEFT JOIN
                `condition` con ON p.condition_id = con.condition_id
            LEFT JOIN
                `language` lang ON p.language_id = lang.language_id
            WHERE
                p.prod_id = :productId";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($product) {
            // Log success
            error_log("Successfully retrieved product: " . $product->title);
            
            // Get authors for this product
            $authorSql = "SELECT a.author_id, a.author_name
                         FROM author a
                         JOIN product_author pa ON a.author_id = pa.author_id
                         WHERE pa.product_id = :productId";
            
            $authorStmt = $pdo->prepare($authorSql);
            $authorStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $authorStmt->execute();
            
            $authors = $authorStmt->fetchAll(PDO::FETCH_OBJ);
            $product->authors = $authors ?: []; // Ensure it's at least an empty array
            
            // Create formatted author list
            $authorNames = array_map(function($author) {
                return $author->author_name;
            }, $authors);
            
            $product->author_names = !empty($authorNames) ? implode(', ', $authorNames) : '';
            
            // Get genres for this product
            $genreSql = "SELECT g.genre_id, " . 
                       ($language === 'fi' ? 'g.genre_fi_name' : 'g.genre_sv_name') . " as genre_name
                       FROM genre g
                       JOIN product_genre pg ON g.genre_id = pg.genre_id
                       WHERE pg.product_id = :productId";
            
            $genreStmt = $pdo->prepare($genreSql);
            $genreStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $genreStmt->execute();
            
            $genres = $genreStmt->fetchAll(PDO::FETCH_OBJ);
            $product->genres = $genres ?: []; // Ensure it's at least an empty array
            
            // Create genre arrays for use in templates
            $product->genre_ids_array = array_map(function($genre) {
                return $genre->genre_id;
            }, $genres);
            
            $product->genre_names_array = array_map(function($genre) {
                return $genre->genre_name;
            }, $genres);
        } else {
            // Log failure
            error_log("Product not found with ID: $productId");
        }
        
        return $product;
    } catch (PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
        return null;
    }
}

/**
 * Gets all product images
 * 
 * @param int $productId Product ID
 * @return array Image data
 */
function getProductImages($productId) {
    global $pdo;
    
    try {
        $sql = "SELECT 
                pi.product_image_id, i.image_id, i.image_path, i.image_name, 
                pi.is_primary, pi.display_order
            FROM 
                product_image pi
            JOIN 
                image i ON pi.image_id = i.image_id
            WHERE 
                pi.product_id = :productId
            ORDER BY 
                pi.is_primary DESC, pi.display_order ASC";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error fetching product images: " . $e->getMessage());
        return [];
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
        $languageId = (int)($formData['edit-language'] ?? 0);
        $year = (int)($formData['edit-year'] ?? 0);
        $publisher = trim($formData['edit-publisher'] ?? '');
        $specialPrice = isset($formData['edit-special-price']) ? 1 : 0;
        $rare = isset($formData['edit-rare']) ? 1 : 0;
        $recommended = isset($formData['edit-recommended']) ? 1 : 0;
        
        // Author information
        $authorIds = isset($formData['edit-authors']) ? $formData['edit-authors'] : [];
        $newAuthorName = trim($formData['new-author-name'] ?? '');
        
        // Genre information
        $genreIds = isset($formData['edit-genres']) ? $formData['edit-genres'] : [];
        
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
                language_id = :language_id,
                year = :year,
                publisher = :publisher,
                special_price = :special_price,
                rare = :rare,
                recommended = :recommended
                WHERE prod_id = :prod_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':shelf_id', $shelf, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':condition_id', $condition, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':internal_notes', $internalNotes);
        $stmt->bindParam(':language_id', $languageId, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':special_price', $specialPrice, PDO::PARAM_INT);
        $stmt->bindParam(':rare', $rare, PDO::PARAM_INT);
        $stmt->bindParam(':recommended', $recommended, PDO::PARAM_INT);
        $stmt->bindParam(':prod_id', $productId, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Handle new author if provided
        if (!empty($newAuthorName)) {
            // Check if author exists
            $authorStmt = $pdo->prepare("SELECT author_id FROM author WHERE author_name = :author_name");
            $authorStmt->bindParam(':author_name', $newAuthorName);
            $authorStmt->execute();
            
            $authorId = $authorStmt->fetchColumn();
            
            if (!$authorId) {
                // Create new author
                $insertAuthorStmt = $pdo->prepare("INSERT INTO author (author_name) VALUES (:author_name)");
                $insertAuthorStmt->bindParam(':author_name', $newAuthorName);
                $insertAuthorStmt->execute();
                
                $authorId = $pdo->lastInsertId();
            }
            
            // Add to selected authors array
            $authorIds[] = $authorId;
        }
        
        // Update authors
        // First clear existing authors for this product
        $deleteAuthorStmt = $pdo->prepare("DELETE FROM product_author WHERE product_id = :product_id");
        $deleteAuthorStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteAuthorStmt->execute();
        
        // Then add new author relationships
        if (!empty($authorIds)) {
            foreach ($authorIds as $authorId) {
                $insertRelationStmt = $pdo->prepare("INSERT INTO product_author (product_id, author_id) VALUES (:product_id, :author_id)");
                $insertRelationStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $insertRelationStmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
                $insertRelationStmt->execute();
            }
        }
        
        // Update genres
        // First clear existing genres for this product
        $deleteGenreStmt = $pdo->prepare("DELETE FROM product_genre WHERE product_id = :product_id");
        $deleteGenreStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteGenreStmt->execute();
        
        // Then add new genre relationships
        if (!empty($genreIds)) {
            foreach ($genreIds as $genreId) {
                $insertGenreStmt = $pdo->prepare("INSERT INTO product_genre (product_id, genre_id) VALUES (:product_id, :genre_id)");
                $insertGenreStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $insertGenreStmt->bindParam(':genre_id', $genreId, PDO::PARAM_INT);
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
        
        // Get and delete images
        $getImagesStmt = $pdo->prepare("
            SELECT i.image_id, i.image_path 
            FROM product_image pi
            JOIN image i ON pi.image_id = i.image_id
            WHERE pi.product_id = :product_id
        ");
        $getImagesStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $getImagesStmt->execute();
        $images = $getImagesStmt->fetchAll(PDO::FETCH_OBJ);
        
        // Delete product image relationships
        $deleteImageRelationsStmt = $pdo->prepare("DELETE FROM product_image WHERE product_id = :product_id");
        $deleteImageRelationsStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $deleteImageRelationsStmt->execute();
        
        // Delete each image file and record
        foreach ($images as $image) {
            // Delete image file
            $fullPath = '../' . $image->image_path;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            
            // Delete image record
            $deleteImageStmt = $pdo->prepare("DELETE FROM image WHERE image_id = :image_id");
            $deleteImageStmt->bindParam(':image_id', $image->image_id, PDO::PARAM_INT);
            $deleteImageStmt->execute();
        }
        
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
        
        // Try to delete the old product image if it exists (legacy support)
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
 * @return array Result with success flag and message
 */
function uploadImage($file, $productId) {
    global $pdo;
    
    try {
        // Define upload directory
        $uploadDir = '../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
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
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Generate unique filename
        $uniqueId = uniqid();
        $filename = $productId . '_' . $uniqueId . '.' . $imageFileType;
        $uploadPath = $uploadDir . $filename;
        $relativePath = 'uploads/products/' . $filename;
        
        // Try to upload the file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Insert image record into the database
            $stmt = $pdo->prepare("
                INSERT INTO image (image_name, image_path) 
                VALUES (:image_name, :image_path)
            ");
            $stmt->bindParam(':image_name', $file['name']);
            $stmt->bindParam(':image_path', $relativePath);
            $stmt->execute();
            
            $imageId = $pdo->lastInsertId();
            
            // Check if this is the first image for the product
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM product_image WHERE product_id = :product_id
            ");
            $checkStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            $isPrimary = ($checkStmt->fetchColumn() == 0) ? 1 : 0;
            
            // Get next display order
            $orderStmt = $pdo->prepare("
                SELECT COALESCE(MAX(display_order), 0) + 1 FROM product_image WHERE product_id = :product_id
            ");
            $orderStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $orderStmt->execute();
            $displayOrder = $orderStmt->fetchColumn();
            
            // Link image to product
            $linkStmt = $pdo->prepare("
                INSERT INTO product_image (product_id, image_id, is_primary, display_order) 
                VALUES (:product_id, :image_id, :is_primary, :display_order)
            ");
            $linkStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $linkStmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);
            $linkStmt->bindParam(':is_primary', $isPrimary, PDO::PARAM_INT);
            $linkStmt->bindParam(':display_order', $displayOrder, PDO::PARAM_INT);
            $linkStmt->execute();
            
            // Commit transaction
            $pdo->commit();
            
            return [
                'success' => true, 
                'message' => 'Bilden har laddats upp framgångsrikt.'
            ];
        } else {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            
            return [
                'success' => false, 
                'message' => 'Ett fel inträffade vid uppladdning av bilden.'
            ];
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error uploading image: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Databasfel: ' . $e->getMessage()
        ];
    }
}

/**
 * Gets categories for dropdown
 * 
 * @param PDO $pdo Database connection
 * @param string $language Current language
 * @return array Categories
 */
function getCategoriesForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT category_id, {$field} AS category_name FROM category ORDER BY {$field}");
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
 * @param string $language Current language
 * @return array Genres
 */
function getGenresForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'genre_fi_name' : 'genre_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT genre_id, {$field} AS genre_name FROM genre ORDER BY {$field}");
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
 * @param string $language Current language
 * @return array Shelves
 */
function getShelvesForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'shelf_fi_name' : 'shelf_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT shelf_id, {$field} AS shelf_name FROM shelf ORDER BY {$field}");
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
 * @param string $language Current language
 * @return array Conditions
 */
function getConditionsForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'condition_fi_name' : 'condition_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT condition_id, {$field} AS condition_name, condition_code FROM `condition` ORDER BY condition_id");
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
 * @param string $language Current language
 * @return array Statuses
 */
function getStatusesForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'status_fi_name' : 'status_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT status_id, {$field} AS status_name FROM status ORDER BY status_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching statuses: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets languages for dropdown
 * 
 * @param PDO $pdo Database connection
 * @param string $language Current language
 * @return array Languages
 */
function getLanguagesForDropdown($pdo, $language) {
    $field = ($language === 'fi') ? 'language_fi_name' : 'language_sv_name';
    
    try {
        $stmt = $pdo->query("SELECT language_id, {$field} AS language_name FROM language ORDER BY {$field}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching languages: " . $e->getMessage());
        return [];
    }
}

/**
 * Gets authors for dropdown
 * 
 * @param PDO $pdo Database connection
 * @return array Authors
 */
function getAuthorsForDropdown($pdo) {
    try {
        $stmt = $pdo->query("SELECT author_id, author_name FROM author ORDER BY author_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching authors: " . $e->getMessage());
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

