<?php
/**
 * Edit Product
 * 
 * Contains:
 * - Product editing form
 * 
 * Functions:
 * - editProduct()
 * - deleteProduct()
 * - uploadImage()
 */

// Include necessary files - adjusted paths to go up one directory level
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/db_functions.php';
require_once '../includes/auth.php';
require_once '../includes/ui.php';

// TODO: Authentication check
// This will be implemented later with checkAuth() function
// checkAuth(2); // Role 2 (Editor) or above required


// Load language settings if multilingual support is enabled
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Page title
$pageTitle = "Redigera Produkt - Admin";

// Include admin header
include '../templates/admin_header.php';

// TODO: Get product data
// This will be implemented later with getProductById() function
// $product = getProductById($productId);
?>

<!-- Main Content Container -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Objektdetaljer</h1>
        <div>
            <a href="admin.php" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Tillbaka till lagerhantering
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Item Image and Upload -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Objektbild</h5>
                    <div class="item-image-container mb-3">
                        <img src="" alt="Objektbild" class="img-fluid rounded shadow" id="item-image">
                    </div>
                    <div class="mb-3">
                        <label for="item-image-upload" class="form-label">Ladda upp ny bild</label>
                        <input class="form-control" type="file" id="item-image-upload" name="item-image-upload">
                    </div>
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
                        <input type="hidden" id="item-id" name="item-id" value="<?php echo $productId; ?>">

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="edit-title" class="form-label">Titel</label>
                                <input type="text" class="form-control" id="edit-title" name="edit-title" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-status" class="form-label">Status</label>
                                <select class="form-select" id="edit-status" name="edit-status">
                                    <option value="1">Tillgänglig</option>
                                    <option value="2">Såld</option>
                                    <option value="3">Reserverad</option>
                                    <option value="4">Skadad</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-author-first" class="form-label">Författare förnamn</label>
                                <input type="text" class="form-control" id="edit-author-first" name="edit-author-first">
                            </div>
                            <div class="col-md-6">
                                <label for="edit-author-last" class="form-label">Författare efternamn</label>
                                <input type="text" class="form-control" id="edit-author-last" name="edit-author-last">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-category" class="form-label">Kategori</label>
                                <select class="form-select" id="edit-category" name="edit-category" required>
                                    <option value="">Välj kategori</option>
                                    <?php
                                    // Placeholder for category options
                                    // Will be replaced with actual database query
                                    $categories = [
                                        1 => 'Bok',
                                        5 => 'CD',
                                        6 => 'Vinyl',
                                        7 => 'DVD',
                                        8 => 'Serier',
                                        9 => 'Samlarobjekt'
                                    ];
                                    
                                    foreach ($categories as $id => $name) {
                                        echo "<option value=\"$id\">$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-genre" class="form-label">Genre</label>
                                <select class="form-select" id="edit-genre" name="edit-genre">
                                    <option value="">Välj genre</option>
                                    <?php
                                    // Placeholder for genre options
                                    // Will be replaced with actual database query
                                    $genres = [
                                        1 => 'Romaner',
                                        3 => 'Historia',
                                        4 => 'Dikter',
                                        5 => 'Biografi',
                                        6 => 'Barnböcker',
                                        7 => 'Rock',
                                        8 => 'Jazz',
                                        9 => 'Klassisk',
                                        10 => 'Äventyr'
                                    ];
                                    
                                    foreach ($genres as $id => $name) {
                                        echo "<option value=\"$id\">$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit-price" class="form-label">Pris (€)</label>
                                <input type="number" step="0.01" class="form-control" id="edit-price" name="edit-price" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-condition" class="form-label">Skick</label>
                                <select class="form-select" id="edit-condition" name="edit-condition">
                                    <option value="">Välj skick</option>
                                    <?php
                                    // Placeholder for condition options
                                    // Will be replaced with actual database query
                                    $conditions = [
                                        1 => 'Nyskick (K-1)',
                                        2 => 'Mycket bra (K-2)',
                                        3 => 'Bra (K-3)',
                                        4 => 'Acceptabelt (K-4)'
                                    ];
                                    
                                    foreach ($conditions as $id => $name) {
                                        echo "<option value=\"$id\">$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="edit-shelf" class="form-label">Hyllplats</label>
                                <select class="form-select" id="edit-shelf" name="edit-shelf">
                                    <option value="">Välj hylla</option>
                                    <?php
                                    // Placeholder for shelf options
                                    // Will be replaced with actual database query
                                    $shelves = [
                                        1 => 'Finlandssvenska',
                                        3 => 'Lokalhistoria',
                                        4 => 'Sjöfart',
                                        5 => 'Barn/Ungdom',
                                        6 => 'Musik',
                                        7 => 'Film'
                                    ];
                                    
                                    foreach ($shelves as $id => $name) {
                                        echo "<option value=\"$id\">$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="edit-notes" class="form-label">Anteckningar (synlig för kunder)</label>
                            <textarea class="form-control" id="edit-notes" name="edit-notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit-internal-notes" class="form-label">Interna anteckningar (endast för personal)</label>
                            <textarea class="form-control" id="edit-internal-notes" name="edit-internal-notes" rows="2"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-special-price" name="edit-special-price">
                                    <label class="form-check-label" for="edit-special-price">
                                        Speciellt pris
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit-rare" name="edit-rare">
                                    <label class="form-check-label" for="edit-rare">
                                        Sällsynt objekt
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body" id="card-controls">
                                <button type="button" class="btn btn-outline-danger" id="delete-item-btn" name="delete-item">
                                    <i class="fas fa-trash-alt me-1"></i> Ta bort objekt
                                </button>
                                <button type="submit" class="btn btn-primary" id="save-item-btn" name="save-item">
                                    <i class="fas fa-save me-1"></i> Spara ändringar
                                </button>
                            </div>
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
                            <th>Anteckningar</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-history-body">
                        <?php
                        // Placeholder for transaction history
                        // Will be replaced with actual database query
                        ?>
                        <tr>
                            <td>2025-04-10 14:32</td>
                            <td>Objekt skapat</td>
                            <td>admin</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Include admin footer
include '../templates/admin_footer.php';
?>