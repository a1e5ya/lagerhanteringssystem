<?php
/**
 * Table Data Management
 * 
 * Contains:
 * - Interface for managing database tables with Swedish and Finnish names
 * - Accordion interface for better organization
 * - Uses unified database_handler.php for CRUD operations
 */
require_once '../init.php';

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

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
        <div class="card-header bg-light" id="headingCategories" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="false">
            <h5 class="mb-0">Kategorier</h5>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div id="collapseCategories" class="collapse" aria-labelledby="headingCategories">
            <div class="card-body">
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
    </div>

    <!-- Condition Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="headingConditions" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseConditions" aria-expanded="false">
            <h5 class="mb-0">Skick</h5>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div id="collapseConditions" class="collapse" aria-labelledby="headingConditions">
            <div class="card-body">
                <form class="ajax-form mb-3" action="<?php echo url('admin/database_handler.php'); ?>" method="POST">
                    <input type="hidden" name="action" value="add_condition">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="condition_sv_name" placeholder="Nytt skick (Svenska)" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="condition_fi_name" placeholder="Nytt skick (Finska)" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="condition_code" placeholder="Kod" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="condition_description" placeholder="Beskrivning">
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
                                <th>Skick (Svenska)</th>
                                <th>Skick (Finska)</th>
                                <th>Kod</th>
                                <th>Beskrivning</th>
                                <th width="150px">Åtgärder</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Load existing conditions
                            try {
                                $stmt = $pdo->query("SELECT condition_id, condition_sv_name, condition_fi_name, condition_code, condition_description FROM `condition` ORDER BY condition_id");
                                while ($row = $stmt->fetch()) {
                                    echo "<tr>
                                        <td>" . safeEcho($row['condition_id']) . "</td>
                                        <td>" . safeEcho($row['condition_sv_name']) . "</td>
                                        <td>" . safeEcho($row['condition_fi_name']) . "</td>
                                        <td>" . safeEcho($row['condition_code']) . "</td>
                                        <td>" . safeEcho($row['condition_description']) . "</td>
                                        <td>
                                            <button class=\"edit-btn btn btn-outline-primary btn-sm\"
                                               data-type=\"condition\"
                                               data-id=\"" . safeEcho($row['condition_id']) . "\"
                                               data-sv-name=\"" . safeEcho($row['condition_sv_name']) . "\"
                                               data-fi-name=\"" . safeEcho($row['condition_fi_name']) . "\"
                                               data-code=\"" . safeEcho($row['condition_code']) . "\"
                                               data-description=\"" . safeEcho($row['condition_description']) . "\">Redigera</button>
                                            <button class=\"delete-btn btn btn-outline-danger btn-sm\"
                                               data-type=\"condition\"
                                               data-id=\"" . safeEcho($row['condition_id']) . "\">Ta bort</button>
                                        </td>
                                    </tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='6' class='text-danger'>Error loading conditions: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Redigera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Beskrivning</label>
                                <textarea class="form-control" name="description" id="edit-description" rows="3"></textarea>
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
</div>

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
    
// Clear any existing event handlers to prevent duplicates
$('.card-header').off('click');
$('.collapse').off('show.bs.collapse hide.bs.collapse');

// Re-attach event handlers - this is key for fixing the toggle issue
$('.card-header').on('click', function() {
    // The icon rotation will be handled automatically by the collapse events below
    // DO NOT toggle class here - just let the click trigger the Bootstrap collapse
});

// These handlers will properly sync the icon rotation with the collapse state
$('.collapse').on('show.bs.collapse', function() {
    $(this).prev('.card-header').find('i').addClass('fa-rotate-180');
}).on('hide.bs.collapse', function() {
    $(this).prev('.card-header').find('i').removeClass('fa-rotate-180');
});
    
    // Handle add forms with AJAX
    $('.ajax-form').on('submit', function(e) {
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
                    // Show success message
                    showMessage(response.message, 'success');
                    
                    // Reset the form
                    form[0].reset();
                    
                    // Get the table to update
                    var tableBody = form.closest('.card-body').find('tbody');
                    
                    // Fetch updated table data
                    fetchTableData(formType, tableBody);
                } else {
                    // Show error message
                    showMessage(response.message || 'Ett fel inträffade.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                showMessage('Ett fel inträffade vid behandling av din begäran.', 'danger');
                console.error('Ajax error:', error);
            }
        });
    });
    
    // Function to fetch table data
    function fetchTableData(type, tableElement) {
        $.ajax({
            url: '<?php echo url('admin/get_table_data.php'); ?>',
            type: 'GET',
            data: { type: type },
            success: function(data) {
                if (data) {
                    tableElement.html(data);
                    // Re-attach event handlers to new buttons
                    attachEventHandlers(tableElement);
                }
            },
            error: function() {
                showMessage('Kunde inte uppdatera tabelldata.', 'warning');
            }
        });
    }
    
    // Function to attach event handlers to buttons
    function attachEventHandlers(container) {
        // Edit button handler
        container.find('.edit-btn').on('click', function() {
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
                $('#edit-description').val($(this).data('description'));
            } else {
                $('#condition-fields').hide();
            }
            
            // Show the modal
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
        
        // Delete button handler
        container.find('.delete-btn').on('click', function() {
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
                        // Show success message
                        showMessage(response.message, 'success');
                        
                        // Remove row with animation
                        button.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        // Show error message
                        showMessage(response.message || 'Ett fel inträffade.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    showMessage('Ett fel inträffade vid borttagning.', 'danger');
                    console.error('Ajax error:', error);
                }
            });
        });
    }
    
    // Handle edit button clicks
    $('.edit-btn').on('click', function() {
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
            $('#edit-description').val($(this).data('description'));
        } else {
            $('#condition-fields').hide();
        }
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    });
    
    // Handle save edit button click
    $('#saveEdit').on('click', function() {
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
                    // Hide the modal
                    var editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                    editModal.hide();
                    
                    // Show success message
                    showMessage(response.message, 'success');
                    
                    // Find the table to update based on the item type
                    var tableBody;
                    switch(type) {
                        case 'category': 
                            tableBody = $('#collapseCategories').find('tbody');
                            break;
                        case 'shelf': 
                            tableBody = $('#collapseShelves').find('tbody');
                            break;
                        case 'genre': 
                            tableBody = $('#collapseGenres').find('tbody');
                            break;
                        case 'language': 
                            tableBody = $('#collapseLanguages').find('tbody');
                            break;
                        case 'condition': 
                            tableBody = $('#collapseConditions').find('tbody');
                            break;
                    }
                    
                    // Fetch updated table data
                    if (tableBody) {
                        fetchTableData(type, tableBody);
                    }
                } else {
                    // Show error message
                    showMessage(response.message || 'Ett fel inträffade.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                showMessage('Ett fel inträffade vid uppdatering.', 'danger');
                console.error('Ajax error:', error);
            }
        });
    });
    
    // Attach initial event handlers
    attachEventHandlers($('tbody'));
});
</script>

    <!-- Shelf Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="headingShelves" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseShelves" aria-expanded="false">
            <h5 class="mb-0">Hyllplatser</h5>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div id="collapseShelves" class="collapse" aria-labelledby="headingShelves">
            <div class="card-body">
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
    </div>

    <!-- Genres Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="headingGenres" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseGenres" aria-expanded="false">
            <h5 class="mb-0">Genrer</h5>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div id="collapseGenres" class="collapse" aria-labelledby="headingGenres">
            <div class="card-body">
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
    </div>

    <!-- Languages Section -->
    <div class="card mb-3">
        <div class="card-header bg-light" id="headingLanguages" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseLanguages" aria-expanded="false">
            <h5 class="mb-0">Språk</h5>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div id="collapseLanguages" class="collapse" aria-labelledby="headingLanguages">
            <div class="card-body">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Force remove any existing click handlers on all card headers to prevent conflicts
    $('.card-header').off('click');
    
    // Add our custom click handler to all card headers
    $('.card-header').on('click', function(e) {
        // Get the target element ID from data-bs-target attribute
        var targetId = $(this).attr('data-bs-target');
        var collapseElement = $(targetId);
        
        // Toggle the collapse manually
        if (collapseElement.hasClass('show')) {
            // If it's open, close it
            collapseElement.collapse('hide');
            $(this).find('i').removeClass('fa-rotate-180');
        } else {
            // If it's closed, open it
            collapseElement.collapse('show');
            $(this).find('i').addClass('fa-rotate-180');
        }
        
        // Prevent default action to avoid conflicts with Bootstrap's built-in handling
        e.preventDefault();
        return false;
    });
    
    // Force remove existing bootstrap collapse event handlers
    $('.collapse').off('show.bs.collapse hide.bs.collapse');
    
    // Re-add collapse event handlers that will update icon rotation
    $('.collapse').on('show.bs.collapse', function() {
        $(this).prev('.card-header').find('i').addClass('fa-rotate-180');
    }).on('hide.bs.collapse', function() {
        $(this).prev('.card-header').find('i').removeClass('fa-rotate-180');
    });
    
    console.log('Dropdown toggle fix script loaded and applied!');
});
</script>