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
 * - editTableData()
 * - deleteTableData()
 */
require_once '../config/config.php'; // Adjust the path as necessary

// Debugging output

?>

<div id="edit-database">
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
                            <th>Åtgärder</th>
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