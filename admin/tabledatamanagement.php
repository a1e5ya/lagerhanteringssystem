<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Table Data Management
 * 
 * Contains:
 * - Interface for managing database tables
 * 
 * Functions:
 * - render()
 * - addTableData()
 * - editTableData()
 * - deleteTableData()  -> inside delete_item.php atm
 */
require_once '../config/config.php'; // Adjust the path as necessary


// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle category insertion
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

    // Handle shelf insertion
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

    // Handle genre insertion
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

    // Redirect back to the form page to show messages
    header('Location: http://localhost/prog23/lagerhanteringssystem/admin.php?tab=tabledatamanagement'); 
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
                    <input type="text" class="form-control me-2" name="category_name" placeholder="Ny kategori" required>
                    <button class="btn btn-primary" type="submit" name="action" value="add_category">Lägg till</button>
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
                                            <a href='edit_category.php?id={$row['category_id']}' class='btn btn-warning btn-sm'>Redigera</a>
                                            <a href='?action=delete&id={$row['category_id']}&type=category' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this category?\");'>Ta bort</a>
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

    <!-- Shelf Locations Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Hyllplatser</h5>
        </div>
        <div class="card-body">
        <form id="shelf-form" action="admin/tabledatamanagement.php" method="POST">
                <div class="d-flex mb-3">
                    <input type="text" class="form-control me-2" name="shelf_name" placeholder="Ny hyllplats" required>
                    <button class="btn btn-primary" type="submit" name="action" value="add_shelf">Lägg till</button>
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
                                            <a href='edit_shelf.php?id={$row['shelf_id']}' class='btn btn-warning btn-sm'>Redigera</a>
                                            <a href='?action=delete&id={$row['shelf_id']}&type=shelf' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this shelf?\");'>Ta bort</a>
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
                    <input type="text" class="form-control me-2" name="genre_name" placeholder="Ny genre" required>
                    <button class="btn btn-primary" type="submit" name="action" value="add_genre">Lägg till</button>
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
                                            <a href='edit_genre.php?id={$row['genre_id']}' class='btn btn-warning btn-sm'>Redigera</a>
                                            <a href='?action=delete&id={$row['genre_id']}&type=genre' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this genre?\");'>Ta bort</a>
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
</div>

