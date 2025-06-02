<?php
/**
 * Table Data Management
 * 
 * Contains:
 * - Interface for managing database tables with Swedish and Finnish names
 * - Accordion interface for better organization
 * - Database backup and restore functionality
 * - Uses unified database_handler.php for CRUD operations
 * - Access restricted to Admin only (role 1)
 */
require_once '../init.php';

// Check if user is authenticated with Admin permissions ONLY
checkAuth(1); // Only Admin (role 1) can access this page

// Check for messages and display them
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show">' . $_SESSION['message'] . 
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show">' . $_SESSION['error'] . 
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['error']);
}
?>

<div id="edit-database">
    <div id="message-container"></div>
    
    <!-- Categories Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="filter-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Kategorier</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="filter-body" style="display: block;">
            <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                <input type="hidden" name="action" value="add_category">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="category_sv_name" placeholder="Ny kategori (Svenska)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="category_fi_name" placeholder="Ny kategori (Finska)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lägg till</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategorinamn (Svenska)</th>
                            <th>Kategorinamn (Finska)</th>
                            <th width="150px">Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Load existing categories
                        try {
                            $stmt = $pdo->query("SELECT category_id, category_sv_name, category_fi_name FROM category ORDER BY category_id");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                    <td>" . safeEcho($row['category_id']) . "</td>
                                    <td>" . safeEcho($row['category_sv_name']) . "</td>
                                    <td>" . safeEcho($row['category_fi_name']) . "</td>
                                    <td>
                                        <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                           data-type=\"category\"
                                           data-id=\"" . safeEcho($row['category_id']) . "\"
                                           data-sv-name=\"" . safeEcho($row['category_sv_name']) . "\"
                                           data-fi-name=\"" . safeEcho($row['category_fi_name']) . "\">Redigera</button>
                                        <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                           data-type=\"category\"
                                           data-id=\"" . safeEcho($row['category_id']) . "\">Ta bort</button>
                                    </td>
                                </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4' class='text-danger'>Error loading categories: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Condition Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="condition-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Skick</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="condition-body" style="display: none;">
            <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                <input type="hidden" name="action" value="add_condition">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="condition_sv_name" placeholder="Nytt skick (Svenska)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="condition_fi_name" placeholder="Nytt skick (Finska)" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="condition_code" placeholder="Kod" required>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-10"></div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lägg till</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Skick (Svenska)</th>
                            <th>Skick (Finska)</th>
                            <th>Kod</th>
                            <th width="150px">Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Load existing conditions
                        try {
                            $stmt = $pdo->query("SELECT condition_id, condition_sv_name, condition_fi_name, condition_code FROM `condition` ORDER BY condition_id");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                    <td>" . safeEcho($row['condition_id']) . "</td>
                                    <td>" . safeEcho($row['condition_sv_name']) . "</td>
                                    <td>" . safeEcho($row['condition_fi_name']) . "</td>
                                    <td>" . safeEcho($row['condition_code']) . "</td>
                                    <td>
                                        <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                           data-type=\"condition\"
                                           data-id=\"" . safeEcho($row['condition_id']) . "\"
                                           data-sv-name=\"" . safeEcho($row['condition_sv_name']) . "\"
                                           data-fi-name=\"" . safeEcho($row['condition_fi_name']) . "\"
                                           data-code=\"" . safeEcho($row['condition_code']) . "\">Redigera</button>
                                        <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                           data-type=\"condition\"
                                           data-id=\"" . safeEcho($row['condition_id']) . "\">Ta bort</button>
                                    </td>
                                </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5' class='text-danger'>Error loading conditions: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Shelf Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="shelf-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Hyllplatser</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="shelf-body" style="display: none;">
            <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                <input type="hidden" name="action" value="add_shelf">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="shelf_sv_name" placeholder="Ny hyllplats (Svenska)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="shelf_fi_name" placeholder="Ny hyllplats (Finska)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lägg till</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hyllnamn (Svenska)</th>
                            <th>Hyllnamn (Finska)</th>
                            <th width="150px">Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Load existing shelves
                        try {
                            $stmt = $pdo->query("SELECT shelf_id, shelf_sv_name, shelf_fi_name FROM shelf ORDER BY shelf_id");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                    <td>" . safeEcho($row['shelf_id']) . "</td>
                                    <td>" . safeEcho($row['shelf_sv_name']) . "</td>
                                    <td>" . safeEcho($row['shelf_fi_name']) . "</td>
                                    <td>
                                        <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                           data-type=\"shelf\"
                                           data-id=\"" . safeEcho($row['shelf_id']) . "\"
                                           data-sv-name=\"" . safeEcho($row['shelf_sv_name']) . "\"
                                           data-fi-name=\"" . safeEcho($row['shelf_fi_name']) . "\">Redigera</button>
                                        <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                           data-type=\"shelf\"
                                           data-id=\"" . safeEcho($row['shelf_id']) . "\">Ta bort</button>
                                    </td>
                                </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4' class='text-danger'>Error loading shelves: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Genres Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="genre-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Genrer</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="genre-body" style="display: none;">
            <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                <input type="hidden" name="action" value="add_genre">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="genre_sv_name" placeholder="Ny genre (Svenska)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="genre_fi_name" placeholder="Ny genre (Finska)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lägg till</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Genrenamn (Svenska)</th>
                            <th>Genrenamn (Finska)</th>
                            <th width="150px">Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Load existing genres
                        try {
                            $stmt = $pdo->query("SELECT genre_id, genre_sv_name, genre_fi_name FROM genre ORDER BY genre_id");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                    <td>" . safeEcho($row['genre_id']) . "</td>
                                    <td>" . safeEcho($row['genre_sv_name']) . "</td>
                                    <td>" . safeEcho($row['genre_fi_name']) . "</td>
                                    <td>
                                        <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                           data-type=\"genre\"
                                           data-id=\"" . safeEcho($row['genre_id']) . "\"
                                           data-sv-name=\"" . safeEcho($row['genre_sv_name']) . "\"
                                           data-fi-name=\"" . safeEcho($row['genre_fi_name']) . "\">Redigera</button>
                                        <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                           data-type=\"genre\"
                                           data-id=\"" . safeEcho($row['genre_id']) . "\">Ta bort</button>
                                    </td>
                                </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4' class='text-danger'>Error loading genres: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Languages Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="language-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Språk</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="language-body" style="display: none;">
            <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                <input type="hidden" name="action" value="add_language">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="language_sv_name" placeholder="Nytt språk (Svenska)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="language_fi_name" placeholder="Nytt språk (Finska)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lägg till</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Språknamn (Svenska)</th>
                            <th>Språknamn (Finska)</th>
                            <th width="150px">Åtgärder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Load existing languages
                        try {
                            $stmt = $pdo->query("SELECT language_id, language_sv_name, language_fi_name FROM language ORDER BY language_id");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                    <td>" . safeEcho($row['language_id']) . "</td>
                                    <td>" . safeEcho($row['language_sv_name']) . "</td>
                                    <td>" . safeEcho($row['language_fi_name']) . "</td>
                                    <td>
                                        <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                           data-type=\"language\"
                                           data-id=\"" . safeEcho($row['language_id']) . "\"
                                           data-sv-name=\"" . safeEcho($row['language_sv_name']) . "\"
                                           data-fi-name=\"" . safeEcho($row['language_fi_name']) . "\">Redigera</button>
                                        <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                           data-type=\"language\"
                                           data-id=\"" . safeEcho($row['language_id']) . "\">Ta bort</button>
                                    </td>
                                </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='4' class='text-danger'>Error loading languages: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Database Backup Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="backup-header" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Databas Backup</h5>
                <i class="fas fa-chevron-up toggle-icon" style="transition: transform 0.3s; font-size: 1.2rem;"></i>
            </div>
        </div>
        <div class="card-body" id="backup-body" style="display: none;">
            <!-- Create Backup Button -->
            <div class="mb-4">
                <button type="button" class="btn btn-success" id="create-backup-btn">
                    <i class="fas fa-download"></i> Skapa Backup
                </button>
            </div>

            <!-- Backup List -->
<div class="table-responsive">
    <table class="table table-sm" id="backup-table">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Tid</th>
                <th>Storlek</th>
                <th>Produkt Kvantitet</th>
                <th width="250px">Åtgärder</th>
            </tr>
        </thead>
        <tbody id="backup-list">
            <!-- Backup list will be loaded here -->
        </tbody>
    </table>
</div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Redigera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <input type="hidden" name="type" id="edit-type">
                        
                        <div class="mb-3">
                            <label for="edit-sv-name" class="form-label">Namn (Svenska)</label>
                            <input type="text" class="form-control" name="sv_name" id="edit-sv-name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-fi-name" class="form-label">Namn (Finska)</label>
                            <input type="text" class="form-control" name="fi_name" id="edit-fi-name" required>
                        </div>
                        
                        <!-- Condition-specific fields -->
                        <div id="condition-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="edit-code" class="form-label">Kod</label>
                                <input type="text" class="form-control" name="code" id="edit-code">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="button" class="btn btn-primary" id="saveEdit">Spara</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hide Confirm Modal -->
    <div class="modal fade" id="hideModal" tabindex="-1" role="dialog" aria-labelledby="hideModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hideModalLabel">Bekräfta Döljning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Är du säker på att du vill dölja denna backup?
                    </p>
                    <p><strong>Viktigt att veta:</strong></p>
                    <ul>
                        <li>Backup-filen kommer <strong>inte</strong> att raderas från servern</li>
                        <li>Backup-filen kommer att vara dold från denna lista</li>
                        <li>För att återställa en dold backup krävs hjälp från systemadministratör</li>
                        <li>Alla backup-filer och metadata bevaras säkert</li>
                    </ul>
                    <p><strong>Backup att dölja:</strong> <span id="hide-filename"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="button" class="btn btn-warning" id="confirmHide">Dölj</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">Bekräfta Återställning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Är du säker på att du vill återställa databasen från denna backup?
                    </p>
                    <p>Detta kommer att:</p>
                    <ul>
                        <li>Skapa en automatisk backup av den nuvarande databasen</li>
                        <li>Återställa databasen till det valda tillståndet</li>
                        <li>Denna åtgärd kan inte ångras</li>
                    </ul>
                    <p><strong>Backup att återställa:</strong> <span id="restore-filename"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="button" class="btn btn-warning" id="confirmRestore">Återställ</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.toggle-icon {
    transition: transform 0.3s;
    font-size: 1.2rem;
}
.toggle-icon.rotated {
    transform: rotate(180deg);
}
.backup-hidden {
    opacity: 0.5;
    text-decoration: line-through;
}
</style>

<script>
// Wait for document ready
$(document).ready(function() {
    // Initialize message container
    var messageContainer = $('#message-container');
    
    // Function to show message
    function showMessage(message, type) {
        var alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                     message +
                     '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                     '</div>');
        
        messageContainer.append(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            alert.alert('close');
        }, 5000);
    }
    
    // Function to fetch table data and update tbody
    function fetchTableData(type, tableElement) {
        $.ajax({
            url: '<?php echo url('admin/get_table_data.php'); ?>',
            type: 'GET',
            data: { type: type },
            success: function(data) {
                if (data) {
                    tableElement.html(data);
                }
            },
            error: function() {
                showMessage('Kunde inte uppdatera tabelldata.', 'warning');
            }
        });
    }
    
    // Function to load backup list
function loadBackupList() {
    $.ajax({
        url: '<?php echo url('admin/backup_handler.php'); ?>',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var backupList = $('#backup-list');
                backupList.empty();
                
                if (response.backups.length === 0) {
                    backupList.append('<tr><td colspan="5" class="text-center text-muted">Inga backups hittades</td></tr>');
                } else {
                    response.backups.forEach(function(backup) {
                        var rowClass = backup.hidden ? 'backup-hidden' : '';
                        var hideText = backup.hidden ? 'Visa' : 'Dölj';
                        var hideAction = backup.hidden ? 'show' : 'hide';
                        
                        var row = '<tr class="' + rowClass + '">' +
                            '<td>' + backup.date + '</td>' +
                            '<td>' + backup.time + '</td>' +
                            '<td>' + backup.size + '</td>' +
                            '<td>' + backup.product_count + '</td>' +
                            '<td>' +
                                '<button class="btn btn-sm btn-success download-backup-btn me-1" data-filename="' + backup.filename + '" title="Ladda ner backup">' +
                                    '<i class="fas fa-download"></i> Ladda ner' +
                                '</button>' +
                                '<button class="btn btn-sm btn-primary restore-btn me-1" data-filename="' + backup.filename + '" title="Återställ från backup">Återställ</button>' +
                                '<button class="btn btn-sm btn-secondary hide-btn" data-filename="' + backup.filename + '" data-action="' + hideAction + '" title="Dölj backup">' + hideText + '</button>' +
                            '</td>' +
                        '</tr>';
                        backupList.append(row);
                    });
                }
            } else {
                showMessage('Kunde inte ladda backup-lista: ' + response.message, 'danger');
            }
        },
        error: function() {
            showMessage('Fel vid laddning av backup-lista.', 'danger');
        }
    });
}
    
    // Collapse advanced filters by default
    $('#filter-body').hide();
    $('#condition-body').hide();
    $('#shelf-body').hide(); 
    $('#genre-body').hide();
    $('#language-body').hide();
    $('#backup-body').hide();
    $('.toggle-icon').addClass('rotated');
    
    // Toggle section handlers
    $('#filter-header, #condition-header, #shelf-header, #genre-header, #language-header, #backup-header').off('click').on('click', function() {
        const headerId = $(this).attr('id');
        const bodyId = headerId.replace('-header', '-body');
        const filterBody = $('#' + bodyId);
        const toggleIcon = $(this).find('.toggle-icon');
        
        if (filterBody.is(':visible')) {
            filterBody.slideUp();
            toggleIcon.addClass('rotated');
        } else {
            filterBody.slideDown();
            toggleIcon.removeClass('rotated');
            
            // Load backup list when backup section is opened
            if (bodyId === 'backup-body') {
                loadBackupList();
            }
        }
    });
    
    // Handle add forms with AJAX using event delegation
    $(document).off('submit', '.ajax-form');
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();
        var formType = formData.split('action=')[1].split('&')[0].replace('add_', '');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    form[0].reset();
                    var tableBody = form.closest('.card-body').find('tbody');
                    fetchTableData(formType, tableBody);
                } else {
                    showMessage(response.message || 'Ett fel inträffade.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showMessage('Ett fel inträffade vid behandling av din begäran.', 'danger');
                console.error('Ajax error:', error);
            }
        });
    });
    
    // Handle edit button clicks using event delegation
    $(document).off('click', '.edit-btn');
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        var svName = $(this).data('sv-name');
        var fiName = $(this).data('fi-name');
        
        // Fill the modal form
        $('#edit-id').val(id);
        $('#edit-type').val(type);
        $('#edit-sv-name').val(svName);
        $('#edit-fi-name').val(fiName);
        
        // Set modal title
        var title = 'Redigera ';
        switch(type) {
            case 'category': title += 'kategori'; break;
            case 'shelf': title += 'hyllplats'; break;
            case 'genre': title += 'genre'; break;
            case 'language': title += 'språk'; break;
            case 'condition': title += 'skick'; break;
            default: title += 'objekt';
        }
        $('#editModalLabel').text(title);
        
        // Show condition-specific fields if needed
        if (type === 'condition') {
            $('#condition-fields').show();
            $('#edit-code').val($(this).data('code'));
        } else {
            $('#condition-fields').hide();
        }
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    });
    
    // Handle delete button clicks using event delegation
    $(document).off('click', '.delete-btn');
    $(document).on('click', '.delete-btn', function() {
        var button = $(this);
        var id = button.data('id');
        var type = button.data('type');
        
        // Translate type to Swedish for confirmation message
        var typeName = '';
        switch(type) {
            case 'category': typeName = 'kategori'; break;
            case 'shelf': typeName = 'hyllplats'; break;
            case 'genre': typeName = 'genre'; break;
            case 'language': typeName = 'språk'; break;
            case 'condition': typeName = 'skick'; break;
            default: typeName = 'objekt';
        }
        
        // Confirm deletion
        if (!confirm('Är du säker på att du vill ta bort denna ' + typeName + '?')) {
            return;
        }
        
        // Send delete request
        $.ajax({
            url: '<?php echo url('admin/database_handler.php'); ?>',
            type: 'POST',
            data: {
                action: 'delete',
                id: id,
                type: type
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    button.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showMessage(response.message || 'Ett fel inträffade.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showMessage('Ett fel inträffade vid borttagning.', 'danger');
                console.error('Ajax error:', error);
            }
        });
    });
    
    // Handle save edit button click
    $('#saveEdit').off('click').on('click', function() {
        var form = $('#editForm');
        var formData = form.serialize();
        var type = $('#edit-type').val();
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    
                    // Force remove all modal artifacts
                    setTimeout(function() {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                        $('body').css('overflow', '');
                        $(document).off('keydown.bs.modal');
                        
                        document.body.style.overflow = 'auto';
                        document.documentElement.style.overflow = 'auto';
                    }, 300);
                    
                    showMessage(response.message, 'success');
                    
                    // Find the table to update based on the item type
                    var tableBody;
                    switch(type) {
                        case 'category': 
                            tableBody = $('#filter-body').find('tbody');
                            break;
                        case 'shelf': 
                            tableBody = $('#shelf-body').find('tbody');
                            break;
                        case 'genre': 
                            tableBody = $('#genre-body').find('tbody');
                            break;
                        case 'language': 
                            tableBody = $('#language-body').find('tbody');
                            break;
                        case 'condition': 
                            tableBody = $('#condition-body').find('tbody');
                            break;
                    }
                    
                    // Fetch updated table data
                    if (tableBody) {
                        fetchTableData(type, tableBody);
                    }
                } else {
                    showMessage(response.message || 'Ett fel inträffade.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showMessage('Ett fel inträffade vid uppdatering.', 'danger');
                console.error('Ajax error:', error);
            }
        });
    });
    
    // Modal cleanup
    $('#editModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        $('body').css('overflow', '');
        $(document).off('keydown.bs.modal');
        
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
    });
    


// Create backup button click with auto-download
$('#create-backup-btn').off('click').on('click', function() {
    var button = $(this);
    var originalText = button.html();
    
    // Disable button and show loading state
    button.prop('disabled', true);
    button.html('<i class="fas fa-spinner fa-spin"></i> Skapar backup...');
    
    $.ajax({
        url: '<?php echo url('admin/backup_handler.php'); ?>',
        type: 'POST',
        data: { action: 'create' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showMessage(response.message, 'success');
                loadBackupList(); // Reload the backup list
                
                // Trigger automatic download if download_url is provided
                if (response.download_url) {
                    // Create a temporary link and trigger download
                    var downloadLink = document.createElement('a');
                    downloadLink.href = response.download_url;
                    downloadLink.download = response.filename || 'backup.sql';
                    downloadLink.style.display = 'none';
                    
                    // Add to document, click, and remove
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                    
                    // Show additional success message about download
                    setTimeout(function() {
                        showMessage('Backup-fil har laddats ner till din dator.', 'info');
                    }, 1000);
                }
            } else {
                showMessage('Fel vid skapande av backup: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Backup creation error:', error);
            showMessage('Ett fel inträffade vid skapande av backup.', 'danger');
        },
        complete: function() {
            // Re-enable button
            button.prop('disabled', false);
            button.html(originalText);
        }
    });
});

// Optional: Add manual download buttons for existing backups
$(document).off('click', '.download-backup-btn');
$(document).on('click', '.download-backup-btn', function() {
    var filename = $(this).data('filename');
    var downloadUrl = '<?php echo url('admin/backup_handler.php'); ?>?action=download&filename=' + encodeURIComponent(filename);
    
    // Create temporary link and trigger download
    var downloadLink = document.createElement('a');
    downloadLink.href = downloadUrl;
    downloadLink.download = filename;
    downloadLink.style.display = 'none';
    
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    
    showMessage('Laddar ner: ' + filename, 'info');
});
    
    // Handle restore button clicks using event delegation
    $(document).off('click', '.restore-btn');
    $(document).on('click', '.restore-btn', function() {
        var filename = $(this).data('filename');
        $('#restore-filename').text(filename);
        
        // Store filename for confirmation
        $('#confirmRestore').data('filename', filename);
        
        // Show confirmation modal
        var restoreModal = new bootstrap.Modal(document.getElementById('restoreModal'));
        restoreModal.show();
    });
    
    // Handle confirm restore button click
    $('#confirmRestore').off('click').on('click', function() {
        var filename = $(this).data('filename');
        var button = $(this);
        var originalText = button.html();
        
        // Disable button and show loading state
        button.prop('disabled', true);
        button.html('Återställer...');
        
        $.ajax({
            url: '<?php echo url('admin/backup_handler.php'); ?>',
            type: 'POST',
            data: { 
                action: 'restore',
                filename: filename
            },
            dataType: 'json',
            success: function(response) {
                $('#restoreModal').modal('hide');
                
                if (response.success) {
                    showMessage(response.message, 'success');
                    loadBackupList(); // Reload the backup list
                    
                    // Optionally reload the page to reflect database changes
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('Fel vid återställning: ' + response.message, 'danger');
                }
            },
            error: function() {
                $('#restoreModal').modal('hide');
                showMessage('Ett fel inträffade vid återställning av databasen.', 'danger');
            },
            complete: function() {
                // Re-enable button
                button.prop('disabled', false);
                button.html(originalText);
            }
        });
    });
    
    // Handle hide/show backup button clicks using event delegation
    $(document).off('click', '.hide-btn');
    $(document).on('click', '.hide-btn', function() {
        var filename = $(this).data('filename');
        var action = $(this).data('action');
        
        if (action === 'hide') {
            // Show confirmation modal for hiding
            $('#hide-filename').text(filename);
            $('#confirmHide').data('filename', filename);
            
            var hideModal = new bootstrap.Modal(document.getElementById('hideModal'));
            hideModal.show();
        } else {
            // Direct show action (though this shouldn't happen since hidden backups aren't shown)
            performHideAction(filename, action);
        }
    });
    
    // Handle confirm hide button click
    $('#confirmHide').off('click').on('click', function() {
        var filename = $(this).data('filename');
        
        $('#hideModal').modal('hide');
        performHideAction(filename, 'hide');
    });
    
    // Function to perform hide/show action
    function performHideAction(filename, action) {
        $.ajax({
            url: '<?php echo url('admin/backup_handler.php'); ?>',
            type: 'POST',
            data: { 
                action: action,
                filename: filename
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    
                    if (action === 'hide') {
                        // Remove the row from the table since it's now hidden
                        $('button[data-filename="' + filename + '"]').closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                } else {
                    showMessage('Fel: ' + response.message, 'danger');
                }
            },
            error: function() {
                showMessage('Ett fel inträffade vid uppdatering av backup-status.', 'danger');
            }
        });
    }
    
    // Cleanup hide modal
    $('#hideModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        $('body').css('overflow', '');
        $(document).off('keydown.bs.modal');
        
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
    });
});
</script>