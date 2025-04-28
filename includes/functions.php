<?php
/**
 * Utility Functions
 * 
 * Contains:
 * - sanitizeInput() - Sanitizes user input
 * - formatCurrency() - Formats price values
 * - formatDate() - Formats dates consistently
 * - displayError() - Shows error messages
 * - displaySuccess() - Shows success messages
 * - logEvent() - Records actions in event log
 * - backupDatabase() - Creates database backup
 * - subscribeToNewsletter() - Adds email to newsletter list
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


function searchProducts(?array $searchParams = null): array
{
    global $pdo;
    if (!is_object($pdo)) {
        echo '<p>PDO-objektet är inte korrekt instansierat!</p>';
        return [];
    }

    $trimmedSearch = trim($searchParams['search'] ?? '');
    $searchTerm = '%' . $trimmedSearch . '%';
    $categoryFilter = !empty($searchParams['category']) && $searchParams['category'] !== 'all' ? $searchParams['category'] : null;

    // Definiera SQL-frågan här
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

    $sql .= " GROUP BY p.prod_id";

    

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
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Databasfel vid sökning: " . $e->getMessage());
        echo '<p>Databasfel: ' . htmlspecialchars($e->getMessage()) . '</p>';
        return [];
    }
}




?>