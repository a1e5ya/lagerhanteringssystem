<?php
/**
 * Table Data Management
 * 
 * Contains:
 * - Interface for managing database tables
 * 
 * Functions:
 * - render()
 * - addTableData()
 * - editTableData() -> inside of edit_item.php
 * - deleteTableData()  -> inside delete_item.php atm
 */
require_once '../config/config.php'; 


// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // category insertion
    if (isset($_POST['action']) && $_POST['action'] === 'add_category') {
        $category_name = trim($_POST['category_name']);
        if ($category_name) {
            try {
                $stmt = $pdo->prepare("INSERT INTO category (category_name) VALUES (?)");
                $stmt->execute([$category_name]);
                $_SESSION['message'] = "Category added successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Please provide a category name.";
        }
    }

    // shelf insertion
    if (isset($_POST['action']) && $_POST['action'] === 'add_shelf') {
        $shelf_name = trim($_POST['shelf_name']);
        if ($shelf_name) {
            try {
                $stmt = $pdo->prepare("INSERT INTO shelf (shelf_name) VALUES (?)");
                $stmt->execute([$shelf_name]);
                $_SESSION['message'] = "Shelf added successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Please provide a shelf name.";
        }
    }

    //  genre insertion
    if (isset($_POST['action']) && $_POST['action'] === 'add_genre') {
        $genre_name = trim($_POST['genre_name']);
        if ($genre_name) {
            try {
                $stmt = $pdo->prepare("INSERT INTO genre (genre_name) VALUES (?)");
                $stmt->execute([$genre_name]);
                $_SESSION['message'] = "Genre added successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Please provide a genre name.";
        }
    }

    // redirect & refresh page after inserting a new category,shelf or genre
    header('Location: /prog23/lagerhanteringssystem/admin.php?tab=tabledatamanagement'); 
    exit();
}

?>

<div id="edit-database">
    <!-- Categories Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Kategorier</h5>
        </div>
        <div class="card-body">
            <form id="category-form" action="admin/tabledatamanagement.php" method="POST">
                <div class="d-flex mb-3">
                    <input type="text" class="form-control me-2 flex-grow-1" name="category_name" placeholder="Ny kategori" required>
                    <button class="btn btn-primary w-25" type="submit" name="action" value="add_category">Lägg till</button>
                </div>
            </form>
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
                        <?php
                        // Load existing categories
                        try {
                            $stmt = $pdo->query("SELECT category_id, category_name FROM category");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                <td>{$row['category_id']}</td>
                                <td>{$row['category_name']}</td>
                                <td>
                                    <a href=\"#\" class=\"edit-item btn btn-outline-primary btn-sm\" 
                                       data-id=\"" . $row['category_id'] . "\" data-type=\"category\" 
                                       data-name=\"" . htmlspecialchars($row['category_name']) . "\">Redigera</a>

                                            <a href='javascript:void(0);' class='btn btn-outline-danger btn-sm delete-item' 
                                            data-id=\"" . $row['category_id'] . "\" 
                                            data-type=\"category\">Ta bort</a>
                                        </td>
                                    </tr>";
                            }
                            
                        } catch (PDOException $e) {
                            echo "Error loading categories: " . $e->getMessage();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Shelf Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Hyllplatser</h5>
        </div>
        <div class="card-body">
        <form id="shelf-form" action="admin/tabledatamanagement.php" method="POST">
                <div class="d-flex mb-3">
                    <input type="text" class="form-control me-2 flex-grow-1" name="shelf_name" placeholder="Ny hyllplats" required>
                    <button class="btn btn-primary w-25" type="submit" name="action" value="add_shelf">Lägg till</button>
                </div>
            </form>
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
                        <?php
                        // Load existing shelves
                        try {
                            $stmt = $pdo->query("SELECT shelf_id, shelf_name FROM shelf");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                        <td>{$row['shelf_id']}</td>
                                        <td>{$row['shelf_name']}</td>
                                        <td>
                                            <a href=\"#\" class=\"edit-item btn btn-outline-primary btn-sm\" 
                                       data-id=\"" . $row['shelf_id'] . "\" data-type=\"shelf\" 
                                       data-name=\"" . htmlspecialchars($row['shelf_name']) . "\">Redigera</a>

                                            <a href='javascript:void(0);' class='btn btn-outline-danger btn-sm delete-item' 
                                            data-id=\"" . $row['shelf_id'] . "\" 
                                            data-type=\"shelf\">Ta bort</a>
                                        </td>
                                    </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "Error loading shelves: " . $e->getMessage();
                        }
                        ?>
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
        <form id="genre-form" action="admin/tabledatamanagement.php" method="POST">
                <div class="d-flex mb-3">
                    <input type="text" class="form-control me-2 flex-grow-1" name="genre_name" placeholder="Ny genre" required>
                    <button class="btn btn-primary w-25" type="submit" name="action" value="add_genre">Lägg till</button>
                </div>
            </form>
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
                        <?php
                        // Load existing genres
                        try {
                            $stmt = $pdo->query("SELECT genre_id, genre_name FROM genre");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>
                                        <td>{$row['genre_id']}</td>
                                        <td>{$row['genre_name']}</td>
                                        <td>
                                        <a href=\"#\" class=\"edit-item btn btn-outline-primary btn-sm\" 
                                       data-id=\"" . $row['genre_id'] . "\" data-type=\"genre\" 
                                       data-name=\"" . htmlspecialchars($row['genre_name']) . "\">Redigera</a>

                                            <a href='javascript:void(0);' class='btn btn-outline-danger btn-sm delete-item' 
                                            data-id=\"" . $row['genre_id'] . "\" 
                                            data-type=\"genre\">Ta bort</a>
                                        </td>
                                    </tr>";

                            }
                        } catch (PDOException $e) {
                            echo "Error loading genres: " . $e->getMessage();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- edit item modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-item-form">
          <input type="hidden" id="edit-item-id" name="id">
          <input type="hidden" id="edit-item-type" name="type">
          <div class="mb-3">
            <label for="edit-item-name" class="form-label">Namn</label>
            <input type="text" class="form-control" id="edit-item-name" name="name" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tillbaka</button>
        <button type="button" class="btn btn-primary" id="save-edit">Spara</button>
      </div>
    </div>
  </div>
</div>
</div>

