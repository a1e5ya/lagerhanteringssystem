<?php
/**
 * Database Handler for Karis Antikvariat
 * 
 * A unified CRUD (Create, Read, Update, Delete) operations handler
 * for all database tables in the admin interface.
 * 
 * @package KarisInventory
 * @author  Karis Inventory Team
 * @version 1.0
 * @since   2024-01-01
 */

require_once '../init.php';

// Check if user is authenticated with proper permissions
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Set JSON response content type for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
}

// Default response array
$response = [
    'success' => false,
    'message' => 'Ingen åtgärd utfördes'
];

// Check for POST request and validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verify CSRF token for all POST requests
    if (!checkCSRFToken()) {
        $response['message'] = 'Säkerhetstoken ogiltigt.';
        outputResponse($response);
        exit;
    }
    
    // Get and sanitize action
    $action = sanitizeInput($_POST['action'] ?? '', 'string', 50);
    
    try {
        switch ($action) {
            case 'add_category':
                $response = handleAddCategory();
                break;
                
            case 'add_shelf':
                $response = handleAddShelf();
                break;
                
            case 'add_genre':
                $response = handleAddGenre();
                break;
                
            case 'add_language':
                $response = handleAddLanguage();
                break;
                
            case 'add_condition':
                $response = handleAddCondition();
                break;
                
            case 'edit':
                $response = handleEdit();
                break;
                
            case 'delete':
                $response = handleDelete();
                break;
                
            default:
                $response['message'] = 'Okänd åtgärd: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
                break;
        }
    } catch (PDOException $e) {
        $response['message'] = 'Ett databasfel inträffade. Försök igen senare.';
        error_log('Database error in database_handler.php: ' . $e->getMessage());
    } catch (InvalidArgumentException $e) {
        $response['message'] = $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = 'Ett oväntat fel inträffade. Försök igen senare.';
        error_log('Unexpected error in database_handler.php: ' . $e->getMessage());
    }
    
    outputResponse($response);
    exit;
}

/**
 * Handle adding a new category
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleAddCategory() {
    global $pdo;
    
    $sv_name = sanitizeInput($_POST['category_sv_name'] ?? '', 'string', 100);
    $fi_name = sanitizeInput($_POST['category_fi_name'] ?? '', 'string', 100);
    
    if (empty($sv_name) || empty($fi_name)) {
        throw new InvalidArgumentException('Både svenska och finska namn krävs.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO category (category_sv_name, category_fi_name) VALUES (:sv_name, :fi_name)");
    $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
    $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Kategori tillagd!',
            'id' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Kunde inte lägga till kategori.');
    }
}

/**
 * Handle adding a new shelf
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleAddShelf() {
    global $pdo;
    
    $sv_name = sanitizeInput($_POST['shelf_sv_name'] ?? '', 'string', 100);
    $fi_name = sanitizeInput($_POST['shelf_fi_name'] ?? '', 'string', 100);
    
    if (empty($sv_name) || empty($fi_name)) {
        throw new InvalidArgumentException('Både svenska och finska namn krävs.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO shelf (shelf_sv_name, shelf_fi_name) VALUES (:sv_name, :fi_name)");
    $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
    $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Hyllplats tillagd!',
            'id' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Kunde inte lägga till hyllplats.');
    }
}

/**
 * Handle adding a new genre
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleAddGenre() {
    global $pdo;
    
    $sv_name = sanitizeInput($_POST['genre_sv_name'] ?? '', 'string', 100);
    $fi_name = sanitizeInput($_POST['genre_fi_name'] ?? '', 'string', 100);
    
    if (empty($sv_name) || empty($fi_name)) {
        throw new InvalidArgumentException('Både svenska och finska namn krävs.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO genre (genre_sv_name, genre_fi_name) VALUES (:sv_name, :fi_name)");
    $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
    $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Genre tillagd!',
            'id' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Kunde inte lägga till genre.');
    }
}

/**
 * Handle adding a new language
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleAddLanguage() {
    global $pdo;
    
    $sv_name = sanitizeInput($_POST['language_sv_name'] ?? '', 'string', 100);
    $fi_name = sanitizeInput($_POST['language_fi_name'] ?? '', 'string', 100);
    
    if (empty($sv_name) || empty($fi_name)) {
        throw new InvalidArgumentException('Både svenska och finska namn krävs.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO language (language_sv_name, language_fi_name) VALUES (:sv_name, :fi_name)");
    $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
    $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Språk tillagt!',
            'id' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Kunde inte lägga till språk.');
    }
}

/**
 * Handle adding a new condition
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleAddCondition() {
    global $pdo;
    
    $sv_name = sanitizeInput($_POST['condition_sv_name'] ?? '', 'string', 100);
    $fi_name = sanitizeInput($_POST['condition_fi_name'] ?? '', 'string', 100);
    $code = sanitizeInput($_POST['condition_code'] ?? '', 'string', 10);
    $description = sanitizeInput($_POST['condition_description'] ?? '', 'string', 500);
    
    if (empty($sv_name) || empty($fi_name) || empty($code)) {
        throw new InvalidArgumentException('Både svenska och finska namn samt kod krävs.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO `condition` (condition_sv_name, condition_fi_name, condition_code, condition_description) VALUES (:sv_name, :fi_name, :code, :description)");
    $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
    $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
    $stmt->bindValue(':code', $code, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Skick tillagt!',
            'id' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Kunde inte lägga till skick.');
    }
}

/**
 * Handle editing existing records
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleEdit() {
    global $pdo;
    
    $id = sanitizeInput($_POST['id'] ?? '', 'int', null, ['min' => 1]);
    $type = sanitizeInput($_POST['type'] ?? '', 'string', 20);
    
    if ($id <= 0 || empty($type)) {
        throw new InvalidArgumentException('Ogiltiga värden. ID och typ krävs.');
    }
    
    // Validate type against allowed values
    $allowedTypes = ['author', 'category', 'shelf', 'genre', 'language', 'condition'];
    if (!in_array($type, $allowedTypes)) {
        throw new InvalidArgumentException('Ogiltig typ angiven.');
    }
    
    $result = false;
    $message = '';
    
    // Handle author separately since it has different structure
    if ($type === 'author') {
        $author_name = sanitizeInput($_POST['author_name'] ?? '', 'string', 255);
        
        if (empty($author_name)) {
            throw new InvalidArgumentException('Författarnamn krävs.');
        }
        
        $stmt = $pdo->prepare("UPDATE author SET author_name = :author_name WHERE author_id = :id");
        $stmt->bindValue(':author_name', $author_name, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        $message = 'Författare uppdaterad!';
    } else {
        // Handle other types with sv/fi names
        $sv_name = sanitizeInput($_POST['sv_name'] ?? '', 'string', 100);
        $fi_name = sanitizeInput($_POST['fi_name'] ?? '', 'string', 100);
        
        if (empty($sv_name) || empty($fi_name)) {
            throw new InvalidArgumentException('Ogiltiga värden. Svenska och finska namn krävs.');
        }
        
        switch ($type) {
            case 'category':
                $stmt = $pdo->prepare("UPDATE category SET category_sv_name = :sv_name, category_fi_name = :fi_name WHERE category_id = :id");
                $message = 'Kategori uppdaterad!';
                break;
                
            case 'shelf':
                $stmt = $pdo->prepare("UPDATE shelf SET shelf_sv_name = :sv_name, shelf_fi_name = :fi_name WHERE shelf_id = :id");
                $message = 'Hyllplats uppdaterad!';
                break;
                
            case 'genre':
                $stmt = $pdo->prepare("UPDATE genre SET genre_sv_name = :sv_name, genre_fi_name = :fi_name WHERE genre_id = :id");
                $message = 'Genre uppdaterad!';
                break;
                
            case 'language':
                $stmt = $pdo->prepare("UPDATE language SET language_sv_name = :sv_name, language_fi_name = :fi_name WHERE language_id = :id");
                $message = 'Språk uppdaterat!';
                break;
                
            case 'condition':
                $code = sanitizeInput($_POST['code'] ?? '', 'string', 10);
                $description = sanitizeInput($_POST['description'] ?? '', 'string', 500);
                
                $stmt = $pdo->prepare("UPDATE `condition` SET condition_sv_name = :sv_name, condition_fi_name = :fi_name, condition_code = :code, condition_description = :description WHERE condition_id = :id");
                $stmt->bindValue(':code', $code, PDO::PARAM_STR);
                $stmt->bindValue(':description', $description, PDO::PARAM_STR);
                $message = 'Skick uppdaterat!';
                break;
        }
        
        $stmt->bindValue(':sv_name', $sv_name, PDO::PARAM_STR);
        $stmt->bindValue(':fi_name', $fi_name, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
    }
    
    if ($result) {
        return [
            'success' => true,
            'message' => $message,
            'id' => $id,
            'type' => $type
        ];
    } else {
        throw new Exception('Kunde inte uppdatera data.');
    }
}

/**
 * Handle deleting records
 * 
 * @return array Response array with success status and message
 * @throws InvalidArgumentException If validation fails
 */
function handleDelete() {
    global $pdo;
    
    $id = sanitizeInput($_POST['id'] ?? '', 'int', null, ['min' => 1]);
    $type = sanitizeInput($_POST['type'] ?? '', 'string', 20);
    
    if ($id <= 0 || empty($type)) {
        throw new InvalidArgumentException('Ogiltiga värden. ID och typ krävs.');
    }
    
    // Validate type against allowed values
    $allowedTypes = ['author', 'category', 'shelf', 'genre', 'language', 'condition'];
    if (!in_array($type, $allowedTypes)) {
        throw new InvalidArgumentException('Ogiltig typ angiven.');
    }
    
    // Check for dependencies before deleting
    $dependencies = checkDependencies($type, $id, $pdo);
    if ($dependencies['hasDependencies']) {
        throw new InvalidArgumentException($dependencies['message']);
    }
    
    $result = false;
    $message = '';
    
    // Proceed with deletion
    switch ($type) {
        case 'author':
            $stmt = $pdo->prepare("DELETE FROM author WHERE author_id = :id");
            $message = 'Författare har tagits bort!';
            break;
            
        case 'category':
            $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = :id");
            $message = 'Kategori har tagits bort!';
            break;
            
        case 'shelf':
            $stmt = $pdo->prepare("DELETE FROM shelf WHERE shelf_id = :id");
            $message = 'Hyllplats har tagits bort!';
            break;
            
        case 'genre':
            $stmt = $pdo->prepare("DELETE FROM genre WHERE genre_id = :id");
            $message = 'Genre har tagits bort!';
            break;
            
        case 'language':
            $stmt = $pdo->prepare("DELETE FROM language WHERE language_id = :id");
            $message = 'Språk har tagits bort!';
            break;
            
        case 'condition':
            $stmt = $pdo->prepare("DELETE FROM `condition` WHERE condition_id = :id");
            $message = 'Skick har tagits bort!';
            break;
    }
    
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    
    if ($result) {
        return [
            'success' => true,
            'message' => $message,
            'id' => $id,
            'type' => $type
        ];
    } else {
        throw new Exception('Kunde inte ta bort data.');
    }
}

/**
 * Output response in appropriate format
 * 
 * @param array $response Response array to output
 */
function outputResponse($response) {
    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode($response);
        return;
    }
    
    // For form submissions, set session message and redirect
    if ($response['success']) {
        $_SESSION['message'] = $response['message'];
    } else {
        $_SESSION['error'] = $response['message'];
    }
    
    // Redirect back to the admin page
    header('Location: ' . url('admin.php', ['tab' => 'tabledatamanagement']));
}

/**
 * Check for dependencies before deletion
 * 
 * @param string $type The type of item to check
 * @param int $id The ID of the item
 * @param PDO $pdo Database connection
 * @return array Dependencies check result with hasDependencies and message keys
 */
function checkDependencies($type, $id, $pdo) {
    $result = [
        'hasDependencies' => false,
        'message' => ''
    ];
    
    // Validate type against allowed values for security
    $allowedTypes = ['author', 'category', 'shelf', 'genre', 'language', 'condition'];
    if (!in_array($type, $allowedTypes)) {
        $result['hasDependencies'] = true;
        $result['message'] = 'Ogiltig typ för beroendekontroll.';
        return $result;
    }
    
    try {
        $count = 0;
        
        switch ($type) {
            case 'author':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_author WHERE author_id = :id");
                break;
                
            case 'category':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE category_id = :id");
                break;
                
            case 'shelf':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE shelf_id = :id");
                break;
                
            case 'genre':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_genre WHERE genre_id = :id");
                break;
                
            case 'language':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE language_id = :id");
                break;
                
            case 'condition':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE condition_id = :id");
                break;
        }
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $result['hasDependencies'] = true;
            
            if ($type === 'author') {
                $result['message'] = 'Kan inte ta bort eftersom författaren används av ' . $count . ' produkt(er).';
            } elseif (in_array($type, ['language', 'condition'])) {
                $result['message'] = 'Kan inte ta bort eftersom det används av ' . $count . ' produkt(er).';
            } else {
                $result['message'] = 'Kan inte ta bort eftersom den används av ' . $count . ' produkt(er).';
            }
        }
        
    } catch (PDOException $e) {
        error_log('Error checking dependencies: ' . $e->getMessage());
        $result['hasDependencies'] = true; // Assume dependencies on error to be safe
        $result['message'] = 'Fel vid kontroll av beroenden. För säkerhets skull tilläts inte borttagningen.';
    }
    
    return $result;
}
?>