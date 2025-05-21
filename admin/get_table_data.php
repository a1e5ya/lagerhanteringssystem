<?php
/**
 * Get Table Data
 * 
 * AJAX endpoint to fetch table data for dynamic updates
 */
require_once '../init.php';

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Get the requested table type
$type = isset($_GET['type']) ? $_GET['type'] : '';

// HTML output
$html = '';

try {
    switch ($type) {
        case 'category':
            $stmt = $pdo->query("SELECT category_id, category_sv_name, category_fi_name FROM category ORDER BY category_id");
            while ($row = $stmt->fetch()) {
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
            $stmt = $pdo->query("SELECT shelf_id, shelf_sv_name, shelf_fi_name FROM shelf ORDER BY shelf_id");
            while ($row = $stmt->fetch()) {
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
            $stmt = $pdo->query("SELECT genre_id, genre_sv_name, genre_fi_name FROM genre ORDER BY genre_id");
            while ($row = $stmt->fetch()) {
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
            $stmt = $pdo->query("SELECT language_id, language_sv_name, language_fi_name FROM language ORDER BY language_id");
            while ($row = $stmt->fetch()) {
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
            $stmt = $pdo->query("SELECT condition_id, condition_sv_name, condition_fi_name, condition_code, condition_description FROM `condition` ORDER BY condition_id");
            while ($row = $stmt->fetch()) {
                $html .= "<tr>
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
            break;
            
        default:
            $html = '<tr><td colspan="6" class="text-center">Invalid table type</td></tr>';
    }
} catch (PDOException $e) {
    $html = '<tr><td colspan="6" class="text-danger">Error loading data: ' . $e->getMessage() . '</td></tr>';
}

// Output the HTML
echo $html;