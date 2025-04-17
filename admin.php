<?php
/**
 * Admin Dashboard
 * 
 * Contains:
 * - Main admin dashboard
 * - Authentication check
 * - Tab navigation
 */

// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/auth.php';
require_once 'includes/ui.php';

// Temporary function placeholders until the real ones are implemented
if (!function_exists('checkAuth')) {
    function checkAuth($role = null) {
        // Placeholder function - no authentication check for now
        return true;
    }
}

if (!function_exists('getSessionUser')) {
    function getSessionUser() {
        // Placeholder function - return a default user
        return ['user_role' => 1, 'user_username' => 'admin'];
    }
}

if (!function_exists('searchProducts')) {
    function searchProducts($params = []) {
        // Placeholder function - return an empty array for now
        return [];
    }
}

if (!function_exists('getProductAuthors')) {
    function getProductAuthors($productId) {
        // Placeholder function
        return [];
    }
}

if (!function_exists('getCategoryName')) {
    function getCategoryName($categoryId) {
        // Placeholder function
        return 'Kategori';
    }
}

if (!function_exists('getShelfName')) {
    function getShelfName($shelfId) {
        // Placeholder function
        return 'Hylla';
    }
}

if (!function_exists('getStatusName')) {
    function getStatusName($statusId) {
        // Placeholder function
        return 'Status';
    }
}

if (!function_exists('selectTableData')) {
    function selectTableData($tablename, $whereClause = null) {
        // Placeholder function - return an empty array for now
        return [];
    }
}

// Check if user is authenticated and has admin permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Get current user info
$currentUser = getSessionUser();

// Page title
$pageTitle = "Lagerhanteringssystem - Karis Antikvariat";

// Include admin header
include_once 'templates/admin_header.php';
?>

<!-- Main Content Container -->
<div class="container my-4">
    <!-- Inventory System -->
    <div id="inventory-system">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="inventory-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="search-tab" data-bs-toggle="tab" href="#search">Sök</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="add-tab" data-bs-toggle="tab" href="#add">Lägg till objekt</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="edit-database-tab" data-bs-toggle="tab" href="#edit-database">Redigera databas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="lists-tab" data-bs-toggle="tab" href="#lists">Listor</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content border border-top-0 p-4 bg-white">
            <!-- Search Tab -->
            <div class="tab-pane fade show active" id="search">
                <div class="row mb-3">
                    <div class="col-12 mb-3">
                        <label for="search-term" class="form-label">Sökterm</label>
                        <input type="text" class="form-control" id="search-term" placeholder="Ange titel, författare eller ID">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="category-filter" class="form-label">Kategorifilter</label>
                        <select class="form-select" id="category-filter">
                            <option value="any">Alla</option>
                            <!-- Categories will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button id="search-btn" class="btn btn-primary w-100">Sök</button>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-hover" id="inventory-table">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Författare</th>
                                <th>Kategori</th>
                                <th>Hylla</th>
                                <th>Pris</th>
                                <th>Status</th>
                                <th>Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-body">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Inga objekt hittades.</td>
                            </tr>
                            <!-- The PHP code for displaying products is commented out since the functions aren't ready
                            <?php
                            /*
                            // Get search parameters (if any)
                            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                            $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'any';
                            
                            // Prepare search parameters
                            $searchParams = array();
                            if (!empty($searchTerm)) {
                                $searchParams['search'] = $searchTerm;
                            }
                            if ($categoryFilter !== 'any') {
                                $searchParams['category'] = $categoryFilter;
                            }
                            
                            // Get products based on search parameters
                            $products = searchProducts($searchParams);
                            
                            // Render products in table
                            if (count($products) > 0) {
                                foreach ($products as $product) {
                                    // Get authors
                                    $authors = getProductAuthors($product['prod_id']);
                                    $authorNames = array();
                                    foreach ($authors as $author) {
                                        $authorNames[] = trim($author['first_name'] . ' ' . $author['last_name']);
                                    }
                                    $authorDisplay = implode(', ', $authorNames);
                                    
                                    // Get category and shelf
                                    $categoryName = getCategoryName($product['category_id']);
                                    $shelfName = getShelfName($product['shelf_id']);
                                    
                                    // Get status
                                    $statusName = getStatusName($product['status']);
                                    $statusClass = '';
                                    switch ($product['status']) {
                                        case 1: // Available
                                            $statusClass = 'text-success';
                                            break;
                                        case 2: // Sold
                                            $statusClass = 'text-danger';
                                            break;
                                        case 3: // Reserved
                                            $statusClass = 'text-warning';
                                            break;
                                        case 4: // Damaged
                                            $statusClass = 'text-secondary';
                                            break;
                                    }
                                    
                                    // Format price
                                    $price = '€' . number_format($product['price'], 2);
                                    
                                    echo '<tr class="clickable-row" data-id="' . $product['prod_id'] . '">';
                                    echo '<td>' . $product['prod_id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($product['title']) . '</td>';
                                    echo '<td>' . htmlspecialchars($authorDisplay) . '</td>';
                                    echo '<td>' . htmlspecialchars($categoryName) . '</td>';
                                    echo '<td>' . htmlspecialchars($shelfName) . '</td>';
                                    echo '<td>' . $price . '</td>';
                                    echo '<td class="' . $statusClass . '">' . htmlspecialchars($statusName) . '</td>';
                                    echo '<td>';
                                    echo '<div class="btn-group btn-group-sm">';
                                    echo '<a href="admin/adminsingleproduct.php?id=' . $product['prod_id'] . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>';
                                    
                                    // Quick status change buttons
                                    if ($product['status'] == 1) { // If Available, show Sell button
                                        echo '<button class="btn btn-outline-success quick-sell" data-id="' . $product['prod_id'] . '"><i class="fas fa-shopping-cart"></i></button>';
                                    } else if ($product['status'] == 2) { // If Sold, show Return button
                                        echo '<button class="btn btn-outline-warning quick-return" data-id="' . $product['prod_id'] . '"><i class="fas fa-undo"></i></button>';
                                    }
                                    
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center text-muted py-3">Inga objekt hittades.</td></tr>';
                            }
                            */
                            ?>
                            -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Item Tab -->
            <div class="tab-pane fade" id="add">
                <div class="row">
                    <!-- Item Image and Upload -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Objektbild</h5>
                                <div class="item-image-container mb-3">
                                    <img src="assets/images/src-book.webp" alt="Objektbild" class="img-fluid rounded shadow" id="new-item-image">
                                </div>
                                <div class="mb-3">
                                    <label for="item-image-upload" class="form-label">Ladda upp bild</label>
                                    <input class="form-control" type="file" id="item-image-upload" name="item-image">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Details Form -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Grundinformation</h5>
                                <form id="add-item-form" method="post" action="admin/addproduct.php" enctype="multipart/form-data">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label for="item-title" class="form-label">Titel</label>
                                            <input type="text" class="form-control" id="item-title" name="title" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="item-status" class="form-label">Status</label>
                                            <select class="form-select" id="item-status" name="status">
                                                <!-- Status options will be populated dynamically -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="author-first" class="form-label">Författare förnamn</label>
                                            <input type="text" class="form-control" id="author-first" name="authorFirstName">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="author-last" class="form-label">Författare efternamn</label>
                                            <input type="text" class="form-control" id="author-last" name="authorLastName">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="item-category" class="form-label">Kategori</label>
                                            <select class="form-select" id="item-category" name="category" required>
                                                <!-- Categories will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="item-genre" class="form-label">Genre</label>
                                            <select class="form-select" id="item-genre" name="genre">
                                                <!-- Genres will be populated dynamically -->
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
                                            <select class="form-select" id="item-condition" name="condition">
                                                <!-- Conditions will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="item-shelf" class="form-label">Hyllplats</label>
                                            <select class="form-select" id="item-shelf" name="shelf">
                                                <!-- Shelves will be populated dynamically -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="item-language" class="form-label">Språk</label>
                                            <select class="form-select" id="item-language" name="language">
                                                <!-- Languages will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="item-year" class="form-label">Utgivningsår</label>
                                            <input type="number" class="form-control" id="item-year" name="year">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="item-publisher" class="form-label">Förlag</label>
                                            <input type="text" class="form-control" id="item-publisher" name="publisher">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="item-notes" class="form-label">Anteckningar (synlig för kunder)</label>
                                        <textarea class="form-control" id="item-notes" name="notes" rows="3"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="item-internal-notes" class="form-label">Interna anteckningar (endast för personal)</label>
                                        <textarea class="form-control" id="item-internal-notes" name="internalNotes" rows="2"></textarea>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="item-special-price" name="specialPrice" value="1">
                                                <label class="form-check-label" for="item-special-price">
                                                    Speciellt pris
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="item-rare" name="rare" value="1">
                                                <label class="form-check-label" for="item-rare">
                                                    Sällsynt objekt
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="clear-form-btn" class="btn btn-outline-secondary me-2">Rensa</button>
                                        <button type="submit" class="btn btn-primary">Lägg till</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Database Tab Content -->
            <div class="tab-pane fade" id="edit-database">
                <!-- Categories Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Kategorier</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <input type="text" class="form-control me-2" id="new-category" placeholder="Ny kategori">
                            <button class="btn btn-primary" id="add-category-btn">Lägg till</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kategorinamn</th>
                                        <th width="150px">Åtgärder</th>
                                    </tr>
                                </thead>
                                <tbody id="categories-list">
                                    <!-- Categories will be loaded dynamically -->
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Inga kategorier hittades.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Shelf Locations Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Hyllplatser</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <input type="text" class="form-control me-2" id="new-shelf" placeholder="Ny hyllplats">
                            <button class="btn btn-primary" id="add-shelf-btn">Lägg till</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hyllnamn</th>
                                        <th width="150px">Åtgärder</th>
                                    </tr>
                                </thead>
                                <tbody id="shelves-list">
                                    <!-- Shelves will be loaded dynamically -->
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Inga hyllplatser hittades.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Genres Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Genrer</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <input type="text" class="form-control me-2" id="new-genre" placeholder="Ny genre">
                            <button class="btn btn-primary" id="add-genre-btn">Lägg till</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Genrenamn</th>
                                        <th width="150px">Åtgärder</th>
                                    </tr>
                                </thead>
                                <tbody id="genres-list">
                                    <!-- Genres will be loaded dynamically -->
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Inga genrer hittades.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lists Tab -->
            <div class="tab-pane fade" id="lists">
                <!-- Lists content here, similar to before but with static placeholders -->
                <div class="table-responsive">
                    <table class="table table-hover" id="lists-table">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Författare</th>
                                <th>Kategori</th>
                                <th>Hylla</th>
                                <th>Skick</th>
                                <th>Pris</th>
                                <th>Status</th>
                                <th>Tillagd datum</th>
                            </tr>
                        </thead>
                        <tbody id="lists-body">
                            <!-- Table content will be loaded by JavaScript -->
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">Inga objekt hittades.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include admin footer
include_once 'templates/admin_footer.php';
?>

<script>
    // Basic JavaScript for page functionality
    document.addEventListener('DOMContentLoaded', function() {
        // For now, this is just a shell - the real implementation will come later
        console.log('Admin page loaded');
    });
</script>