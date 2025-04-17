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
require_once '../config/config.php'; // Adjust the path as necessary
?>



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