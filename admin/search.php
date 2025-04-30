<?php
/**
 * Search Products
 * 
 * Contains:
 * - Product search functionality
 * 
 * Functions:
 * - searchProducts()
 * - renderProducts()
 * - changeProductSaleStatus()
 */

?>
<?php

/*Hämtar alla kategorier från databasen och returnerar dem som en lista i sidans dropdown.*/
function getCategories($pdo) {
// Förbereder en SQL-fråga för att hämta kategori-ID och namn från tabellen "category"
    $stmt = $pdo->prepare("SELECT category_id, category_name FROM category ORDER BY category_name ASC");
// Kör SQL-frågan
    $stmt->execute();
// Hämtar och returnerar alla kategorier som en associerad array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*Söker produkter baserat på användarens sökparametrar (t.ex. titel, författare eller kategori).*/
function searchProducts(?array $searchParams = null): array
{
    global $pdo; //Används för att tillgång till den globala PDO-instansen (databasanslutningen).
// Kontrollera om PDO-objektet är korrekt instansierat
    if (!is_object($pdo)) {
        echo '<p>PDO-objektet är inte korrekt instansierat!</p>';
        return [];
    }

// Rensar och förbereder söksträngen
    $trimmedSearch = trim($searchParams['search'] ?? '');
    $searchTerm = '%' . $trimmedSearch . '%';

// Hämta kategori om det finns som filter
    $categoryFilter = !empty($searchParams['category']) && $searchParams['category'] !== 'all' ? $searchParams['category'] : null;

// Skapa SQL-frågan för att söka produkter
    $sql = "SELECT
                p.prod_id,
                p.title,
                GROUP_CONCAT(DISTINCT a.first_name SEPARATOR ', ') AS first_names,
                GROUP_CONCAT(DISTINCT a.last_name SEPARATOR ', ') AS last_names,                
                cat.category_name,
                GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ', ') AS genre_names,
                con.condition_name,
                p.price
            FROM product p
            LEFT JOIN
                product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN
                author a ON pa.author_id = a.author_id
            JOIN
                category cat ON p.category_id = cat.category_id
            JOIN
                product_genre pg ON p.prod_id = pg.product_id
            JOIN
                genre g ON pg.genre_id = g.genre_id
            JOIN
                `condition` con ON p.condition_id = con.condition_id
            WHERE
                (p.title LIKE :searchTerm1 OR
                a.first_name LIKE :searchTerm2 OR
                a.last_name LIKE :searchTerm3 OR
                cat.category_name LIKE :searchTerm4)";

    // Lägg till kategori-filter om det finns
    if ($categoryFilter) {
        $sql .= " AND p.category_id = :categoryId";
    }

// Avsluta SQL-satsen med gruppning av produkter
    $sql .= " GROUP BY p.prod_id";

    
// Försök att köra SQL-frågan och hämta resultatet
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':searchTerm1', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':searchTerm2', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':searchTerm3', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':searchTerm4', $searchTerm, PDO::PARAM_STR);

        // Bind kategori-parametern om det finns
        if ($categoryFilter) {
            $stmt->bindParam(':categoryId', $categoryFilter, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
// Returnera alla matchande produkter som objekt
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Databasfel vid sökning: " . $e->getMessage());
        echo '<p>Databasfel: ' . htmlspecialchars($e->getMessage()) . '</p>';
        return [];
    }
}

function renderIndexProducts(array $products): string
{
    ob_start();

    if (!empty($products)) {
        foreach ($products as $product) {
            ?>
            <tr class="clickable-row" data-href="singleproduct.php?id=<?= htmlspecialchars($product->prod_id) ?>">
                <td data-label="Titel"><?= htmlspecialchars($product->title) ?></td>
                <td data-label="Författare/Artist">
                    <?php
                    if (!empty($product->first_names) && !empty($product->last_names)) {
                        echo htmlspecialchars($product->first_names . ' ' . $product->last_names);
                    } elseif (!empty($product->first_names)) {
                        echo htmlspecialchars($product->first_names);
                    } elseif (!empty($product->last_names)) {
                        echo htmlspecialchars($product->last_names);
                    } else {
                        echo 'Okänd författare';
                    }
                    ?>
                </td>
                <td data-label="Kategori"><?= htmlspecialchars($product->category_name) ?></td>
                <td data-label="Genre"><?= htmlspecialchars($product->genre_names) ?></td>
                <td data-label="Skick"><?= htmlspecialchars($product->condition_name) ?></td>
                <td data-label="Pris"><?= htmlspecialchars(number_format($product->price, 2, ',', ' ')) . ' €' ?></td>
                <td><a class="btn btn-success d-block d-md-none" href="singleproduct.php?id=<?= htmlspecialchars($product->prod_id) ?>">Visa detaljer</a></td>
            </tr>
            <?php
        }
    } else {
        if (isset($_GET['search']) || (isset($_GET['category']) && $_GET['category'] !== 'all')) {
            echo '<tr><td colspan="7">Inga produkter hittades som matchar din sökning.</td></tr>';
        } else {
            echo '<tr><td colspan="7">Använd sökfältet ovan för att söka efter produkter.</td></tr>';
        }
    }

    return ob_get_clean();
}


?>


 <!-- Search Tab
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
                 <!-- Categories will be populated dynamically
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
                 
             </tbody>
         </table>
     </div>
 </div>-->