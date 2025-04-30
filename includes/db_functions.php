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
 * Hämta produkter med specialpris (rea), inklusive författarnamn.
 *
 * @param PDO $pdo Databasanslutning
 * @param int $limit Antal produkter att hämta
 * @return array Returnerar en lista av produkter
 */
function getSpecialPriceProducts(PDO $pdo, int $limit = 3): array {
    $sql = "SELECT 
                p.prod_id, 
                p.title, 
                p.price, 
                /*p.image_url,*/
                CONCAT(a.first_name, ' ', a.last_name) AS author_name
            FROM product p
            LEFT JOIN product_author pa ON p.prod_id = pa.product_id
            LEFT JOIN author a ON pa.author_id = a.author_id
            WHERE p.special_price = 1
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
?>