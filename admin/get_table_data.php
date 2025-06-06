<?php
/**
 * Get Table Data
 * 
 * AJAX endpoint to fetch table data for dynamic updates with enhanced security validation
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

require_once '../init.php';

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Get and validate the requested table type
$type = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
if (strlen($type) > 40) $type = substr($type, 0, 40);

// Validate table type against allowed values for security
$allowedTypes = ['category', 'shelf', 'genre', 'language', 'condition'];
if (!in_array($type, $allowedTypes)) {
    echo '<tr><td colspan="6" class="text-center">Invalid table type</td></tr>';
    exit;
}

// HTML output
$html = '';

try {
    switch ($type) {
        case 'category':
            $stmt = $pdo->prepare("SELECT category_id, category_sv_name, category_fi_name FROM category ORDER BY category_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= "<tr>
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
            break;
            
        case 'shelf':
            $stmt = $pdo->prepare("SELECT shelf_id, shelf_sv_name, shelf_fi_name FROM shelf ORDER BY shelf_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= "<tr>
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
            break;
            
        case 'genre':
            $stmt = $pdo->prepare("SELECT genre_id, genre_sv_name, genre_fi_name FROM genre ORDER BY genre_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= "<tr>
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
            break;
            
        case 'language':
            $stmt = $pdo->prepare("SELECT language_id, language_sv_name, language_fi_name FROM language ORDER BY language_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= "<tr>
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
            break;
            
        case 'condition':
            $stmt = $pdo->prepare("SELECT condition_id, condition_sv_name, condition_fi_name, condition_code FROM `condition` ORDER BY condition_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= "<tr>
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
            break;
    }
} catch (PDOException $e) {
    // Log the error for debugging but don't expose details to users
    error_log('Database error in get_table_data.php: ' . $e->getMessage());
    
    // Show generic error to user
    $html = '<tr><td colspan="6" class="text-danger">Ett fel inträffade vid hämtning av data</td></tr>';
}

// Output the HTML
echo $html;
?>