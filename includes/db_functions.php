<?php
/**
 * Database Functions
 * 
 * Contains:
 * - selectTableData() - Generic database query
 * - addTableData() - Generic database insert
 * - editTableData() - Generic database update
 * - deleteTableData() - Generic database delete
 */

/**
 * Gets products with special prices (on sale)
 *
 * @param PDO $pdo Database connection
 * @param int $limit Number of products to retrieve
 * @return array Returns a list of products
 */
function getSpecialPriceProducts(PDO $pdo, int $limit = 3): array {
    // Get language from session or set default
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    
    // Determine which field to use based on language
    $categoryNameField = ($language === 'fi') ? 'cat.category_fi_name' : 'cat.category_sv_name';
    
    // Updated SQL that works with the new database structure
    // The author table now has a single author_name field instead of first_name and last_name
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                p.price,
                p.image,
                a.author_name
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            LEFT JOIN category cat ON p.category_id = cat.category_id
            WHERE p.special_price = 1
            AND p.status = 1
            GROUP BY p.prod_id
            LIMIT :limit";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error in getSpecialPriceProducts: " . $e->getMessage());
        return []; // Return empty array instead of throwing error
    }
}