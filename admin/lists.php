<?php
/**
 * Lists Management - Secured Version
 * 
 * Provides comprehensive product list management with filtering capabilities,
 * batch operations, and export functionality. Enhanced with security features
 * including input validation, output sanitization, and proper error handling.
 * 
 * Features:
 * - Advanced product filtering with multiple criteria
 * - Batch operations for bulk product management
 * - Export functionality with data validation
 * - Secure dropdown population with proper sanitization
 * - CSRF protection for all forms
 * 
 * @package    KarisAntikvariat
 * @subpackage Admin
 * @author     Axxell
 * @version    2.0
 * @since      2024-01-01
 */

require_once '../init.php';

// Check authentication and authorization - requires editor role or higher
checkAuth(2);

/**
 * Get dropdown options for filter forms with enhanced security
 * 
 * Retrieves all necessary dropdown data for the filtering interface
 * with proper sanitization and error handling.
 * 
 * @return array Associative array containing dropdown options
 * @throws PDOException If database error occurs
 */
function getDropdownOptions() {
    global $pdo;
    $options = [];
    
    try {
        // Get categories with language support
        $language = isset($_SESSION['language']) && in_array($_SESSION['language'], ['sv', 'fi']) 
            ? $_SESSION['language'] 
            : 'sv';
        
        $categoryField = ($language === 'fi') ? 'category_fi_name' : 'category_sv_name';
        $stmt = $pdo->prepare("SELECT category_id, {$categoryField} as category_name FROM category ORDER BY {$categoryField}");
        $stmt->execute();
        $options['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get shelves with language support
        $shelfField = ($language === 'fi') ? 'shelf_fi_name' : 'shelf_sv_name';
        $stmt = $pdo->prepare("SELECT shelf_id, {$shelfField} as shelf_name FROM shelf ORDER BY {$shelfField}");
        $stmt->execute();
        $options['shelves'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get conditions with language support
        $conditionField = ($language === 'fi') ? 'condition_fi_name' : 'condition_sv_name';
        $stmt = $pdo->prepare("SELECT condition_id, {$conditionField} as condition_name FROM `condition` ORDER BY condition_id");
        $stmt->execute();
        $options['conditions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get genres with language support
        $genreField = ($language === 'fi') ? 'genre_fi_name' : 'genre_sv_name';
        $stmt = $pdo->prepare("SELECT genre_id, {$genreField} as genre_name FROM genre ORDER BY {$genreField}");
        $stmt->execute();
        $options['genres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statuses with language support
        $statusField = ($language === 'fi') ? 'status_fi_name' : 'status_sv_name';
        $stmt = $pdo->prepare("SELECT status_id, {$statusField} as status_name FROM status ORDER BY status_id");
        $stmt->execute();
        $options['statuses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $options;
        
    } catch (PDOException $e) {
        throw new PDOException('Failed to fetch dropdown options');
    }
}

try {
    $dropdownOptions = getDropdownOptions();
} catch (PDOException $e) {
    // Fallback to empty arrays if database error occurs
    $dropdownOptions = [
        'categories' => [],
        'shelves' => [],
        'conditions' => [],
        'genres' => [],
        'statuses' => []
    ];
}
?>

<div class="tab-pane fade show active" id="lists">
    <div class="mb-4">
        <!-- Quick filter buttons - Enhanced Layout -->
        <div class="row align-items-center">
            <!-- First Column: Rea, Raritet, Rekommenderat -->
            <div class="col-md-4">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger w-100" id="list-special-price">
                        <i class="fas fa-percentage me-1"></i> Rea
                    </button>
                    <button class="btn btn-outline-warning w-100" id="list-rare">
                        <i class="fas fa-gem me-1"></i> Raritet
                    </button>
                    <button class="btn btn-outline-primary w-100" id="list-recommended" style="border-color: #007bff; color: #007bff;">
                        <i class="fas fa-star me-1"></i> Rekommenderat
                    </button>
                </div>
            </div>
            
            <!-- Second Column: Inventering av hylla, Objekt äldre än -->
            <div class="col-md-4">
                <div class="d-grid gap-2">
                    <div class="card">
                        <div class="card-body p-2 text-center">
                            <label class="form-label small mb-1">Inventering av hylla</label>
                            <select class="form-select form-select-sm" id="shelf-selector">
                                <option value="">Välj hylla</option>
                                <?php foreach ($dropdownOptions['shelves'] as $shelf): ?>
                                    <option value="<?= safeEcho($shelf['shelf_name']) ?>"><?= safeEcho($shelf['shelf_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body p-2 text-center">
                            <label class="form-label small mb-1">Objekt äldre än</label>
                            <input type="number" class="form-control form-control-sm" id="year-threshold" 
                                   placeholder="År" min="1800" max="<?= date('Y') + 10 ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Third Column: Utan pris, Dåligt skick, Rensa alla filter -->
            <div class="col-md-4">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-secondary w-100" id="list-no-price">
                        <i class="fas fa-tag-slash me-1"></i> Utan pris
                    </button>
                    <button class="btn btn-outline-secondary w-100" id="list-poor-condition">
                        <i class="fas fa-exclamation-triangle me-1"></i> Dåligt skick
                    </button>
                    <button class="btn btn-outline-danger w-100" id="clear-all-filters">
                        <i class="fas fa-times me-1"></i> Rensa alla filter
                    </button>
                </div>
            </div>
        </div>
        <br>

        <!-- Advanced Filtering Section -->
        <div class="card mb-4">
            <div class="card-header bg-light" id="filter-header" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Avancerad filtrering</h5>
                    <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
                </div>
            </div>
            <div class="card-body" id="filter-body" style="display: block;">
                <style>
                .toggle-icon.rotated {
                    transform: rotate(180deg);
                }
                </style>
                <!-- Search Form with CSRF Protection -->
                <form id="lists-search-form" class="mb-4">
                    <?= getCSRFTokenField() ?>
                    <div class="row g-3">
                        <!-- First Row -->
                        <div class="col-md-3">
                            <label for="category-filter" class="form-label">Kategori</label>
                            <select class="form-select" id="category-filter" name="category">
                                <option value="">Alla kategorier</option>
                                <?php foreach ($dropdownOptions['categories'] as $category): ?>
                                <option value="<?= safeEcho($category['category_id']) ?>">
                                    <?= safeEcho($category['category_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="list-genre" class="form-label">Genre</label>
                            <select class="form-select" id="list-genre" name="genre">
                                <option value="">Alla genrer</option>
                                <?php foreach ($dropdownOptions['genres'] as $genre): ?>
                                    <option value="<?= safeEcho($genre['genre_name']) ?>"><?= safeEcho($genre['genre_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="list-condition" class="form-label">Skick</label>
                            <select class="form-select" id="list-condition" name="condition">
                                <option value="">Alla skick</option>
                                <?php foreach ($dropdownOptions['conditions'] as $condition): ?>
                                    <option value="<?= safeEcho($condition['condition_name']) ?>"><?= safeEcho($condition['condition_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="list-status" class="form-label">Status</label>
                            <select class="form-select" id="list-status" name="status">
                                <option value="all">Alla statusar</option>
                                <option value="" selected>Tillgänglig</option>
                                <option value="Såld">Såld</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <!-- Second Row -->
                        <div class="col-md-3">
                            <label for="shelf-filter" class="form-label">Hylla</label>
                            <select class="form-select" id="shelf-filter" name="shelf">
                                <option value="">Alla hyllor</option>
                                <?php foreach ($dropdownOptions['shelves'] as $shelf): ?>
                                    <option value="<?= safeEcho($shelf['shelf_name']) ?>"><?= safeEcho($shelf['shelf_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Prisintervall</label>
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="Min €" id="price-min" 
                                       min="0" max="999999.99" step="0.01">
                                <span class="input-group-text">till</span>
                                <input type="number" class="form-control" placeholder="Max €" id="price-max" 
                                       min="0" max="999999.99" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <label class="form-label">Datum tillagt</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="date-min" 
                                       min="2000-01-01" max="<?= date('Y-m-d') ?>">
                                <span class="input-group-text">till</span>
                                <input type="date" class="form-control" id="date-max" 
                                       min="2000-01-01" max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-9">
                            <label for="search-term" class="form-label">Fritextsökning</label>
                            <input type="text" class="form-control" id="search-term" name="search" 
                                placeholder="Sök i titel, författare eller anteckningar" maxlength="255">
                        </div>
                        <div class="col-md-3 d-flex justify-content-end align-items-end">
                            <button type="button" class="btn btn-success" id="apply-filters">
                                <i class="fas fa-filter me-1"></i> Tillämpa filter
                            </button>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for sorting and pagination -->
                    <input type="hidden" name="sort" value="">
                    <input type="hidden" name="order" value="asc">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="limit" value="20">
                    <input type="hidden" name="view_type" value="lists">
                </form>
            </div>
        </div>
    </div>

    <!-- Product List Table -->
    <div class="table-responsive">
        <table class="table table-hover" id="inventory-table">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th data-sort="title">Titel</th>
                    <th data-sort="author_name">Författare</th>
                    <th data-sort="category_name">Kategori</th>
                    <th data-sort="shelf_name">Hylla</th>
                    <th data-sort="condition_name">Skick</th>
                    <th data-sort="price">Pris</th>
                    <th data-sort="status">Status</th>
                    <th>Märkning</th>
                    <th data-sort="date_added">Tillagd datum</th>
                </tr>
            </thead>
            <tbody id="inventory-body">
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Laddar...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination controls -->
    <div class="mt-3" id="pagination-controls">
        <div class="row align-items-center">
            <!-- Page size selector -->
            <div class="col-md-4 mb-2 mb-md-0">
                <div class="d-flex align-items-center">
                    <label class="me-2">Visa</label>
                    <select class="form-select form-select-sm" id="page-size-selector" style="width: auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-2">objekt</span>
                </div>
            </div>
            
            <!-- Page info -->
            <div class="col-md-4 text-center mb-2 mb-md-0">
                <div id="pagination-info">
                    Visar <span id="showing-start">0</span> till 
                    <span id="showing-end">0</span> av 
                    <span id="total-items">0</span> objekt
                </div>
            </div>
            
            <!-- Selected items info -->
            <div class="col-md-4 text-end">
                <div class="selected-info">
                    <span id="selected-count">0</span> objekt valda
                </div>
            </div>
        </div>
        
        <!-- Page navigation -->
        <div class="row mt-2">
            <div class="col-12 d-flex justify-content-center">
                <ul class="pagination mb-0" id="pagination-links">
                    <!-- Pagination links will be inserted here by JS -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Batch Operations Section -->
    <div class="card mt-4 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Batchåtgärder</h5>
        </div>
        <div class="card-body">
            <style>
            .btn-outline-danger:hover, .btn-outline-warning:hover, .btn-outline-secondary:hover {
                color: white !important;
            }
            .btn[style*="007bff"]:hover {
                background: #007bff !important;
                color: white !important;
            }
            </style>
            <div class="row mb-3">
                <!-- First Row: Rea, Raritet, Rekommenderat -->
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-danger w-100" id="batch-toggle-sale" disabled>
                        <i class="fas fa-percentage me-1"></i> Rea
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-warning w-100" id="batch-toggle-rare" disabled>
                        <i class="fas fa-gem me-1"></i> Raritet
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn w-100" id="batch-toggle-recommended" disabled style="border: 1px solid #007bff; color: #007bff; background: white;">
                        <i class="fas fa-star me-1"></i> Rekommenderat
                    </button>
                </div>
            </div>

            <div class="row mb-3">
                <!-- Second Row: Uppdatera pris, Ändra status, Flytta till hylla -->
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-update-price" disabled>
                        <i class="fas fa-tag me-1"></i> Uppdatera pris
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-update-status" disabled>
                        <i class="fas fa-exchange-alt me-1"></i> Ändra status
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-secondary w-100" id="batch-move-shelf" disabled>
                        <i class="fas fa-arrows-alt me-1"></i> Flytta till hylla
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Third Row: Skriv ut, Exportera, Ta bort -->
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-primary w-100" id="print-list-btn">
                        <i class="fas fa-print me-1"></i> Skriv ut lista
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-primary w-100" id="export-csv-btn">
                        <i class="fas fa-file-csv me-1"></i> Exportera till CSV
                    </button>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-outline-danger w-100" id="batch-delete" disabled>
                        <i class="fas fa-trash-alt me-1"></i> Ta bort markerade
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for batch operations -->
<!-- Update Price Modal -->
<div class="modal fade" id="updatePriceModal" tabindex="-1" aria-labelledby="updatePriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePriceModalLabel">Uppdatera pris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update-price-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="new-price" class="form-label">Nytt pris (€)</label>
                        <input type="number" step="0.01" class="form-control" id="new-price" 
                               required min="0.01" max="999999.99">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-update-price">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Ändra status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update-status-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="new-status" class="form-label">Ny status</label>
                        <select class="form-select" id="new-status" required>
                            <?php foreach ($dropdownOptions['statuses'] as $status): ?>
                                <option value="<?= safeEcho($status['status_id']) ?>"><?= safeEcho($status['status_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-update-status">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Move Shelf Modal -->
<div class="modal fade" id="moveShelfModal" tabindex="-1" aria-labelledby="moveShelfModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveShelfModalLabel">Flytta till hylla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="move-shelf-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="new-shelf" class="form-label">Ny hylla</label>
                        <select class="form-select" id="new-shelf" required>
                            <?php foreach ($dropdownOptions['shelves'] as $shelf): ?>
                                <option value="<?= safeEcho($shelf['shelf_id']) ?>"><?= safeEcho($shelf['shelf_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-move-shelf">Flytta</button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Special Price Modal -->
<div class="modal fade" id="toggleSpecialPriceModal" tabindex="-1" aria-labelledby="toggleSpecialPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleSpecialPriceModalLabel">Ändra rea-status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toggle-special-price-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="special-price-action" class="form-label">Rea-status för <span id="special-price-count">0</span> valda produkter</label>
                        <select class="form-select" id="special-price-action" required>
                            <option value="1">Markera som rea</option>
                            <option value="0">Ta bort rea-markering</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-danger" id="confirm-toggle-special-price">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Rare Modal -->
<div class="modal fade" id="toggleRareModal" tabindex="-1" aria-labelledby="toggleRareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleRareModalLabel">Ändra raritetsstatus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toggle-rare-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="rare-action" class="form-label">Raritetsstatus för <span id="rare-count">0</span> valda produkter</label>
                        <select class="form-select" id="rare-action" required>
                            <option value="1">Markera som sällsynt</option>
                            <option value="0">Ta bort sällsynt-markering</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-warning" id="confirm-toggle-rare">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Recommended Modal -->
<div class="modal fade" id="toggleRecommendedModal" tabindex="-1" aria-labelledby="toggleRecommendedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleRecommendedModalLabel">Ändra rekommendationsstatus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toggle-recommended-form">
                    <?= getCSRFTokenField() ?>
                    <div class="mb-3">
                        <label for="recommended-action" class="form-label">Rekommendationsstatus för <span id="recommended-count">0</span> valda produkter</label>
                        <select class="form-select" id="recommended-action" required>
                            <option value="1">Markera som rekommenderad</option>
                            <option value="0">Ta bort rekommendation</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-primary" id="confirm-toggle-recommended">Uppdatera</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Bekräfta borttagning</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Är du säker på att du vill ta bort <span id="delete-count">0</span> valda objekt?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Denna åtgärd kan inte ångras!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Ta bort</button>
            </div>
        </div>
    </div>
</div>