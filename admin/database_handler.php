<?php
/**
 * Database Handler for Karis Antikvariat
 * 
 * A unified CRUD (Create, Read, Update, Delete) operations handler
 * for all database tables in the admin interface.
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

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get action
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    try {
        switch ($action) {
            case 'add_category':
                $sv_name = trim($_POST['category_sv_name'] ?? '');
                $fi_name = trim($_POST['category_fi_name'] ?? '');
                
                if (empty($sv_name) || empty($fi_name)) {
                    $response['message'] = 'Både svenska och finska namn krävs.';
                    break;
                }
                
                $stmt = $pdo->prepare("INSERT INTO category (category_sv_name, category_fi_name) VALUES (?, ?)");
                $result = $stmt->execute([$sv_name, $fi_name]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Kategori tillagd!',
                        'id' => $pdo->lastInsertId()
                    ];
                } else {
                    $response['message'] = 'Kunde inte lägga till kategori.';
                }
                break;
                
            case 'add_shelf':
                $sv_name = trim($_POST['shelf_sv_name'] ?? '');
                $fi_name = trim($_POST['shelf_fi_name'] ?? '');
                
                if (empty($sv_name) || empty($fi_name)) {
                    $response['message'] = 'Både svenska och finska namn krävs.';
                    break;
                }
                
                $stmt = $pdo->prepare("INSERT INTO shelf (shelf_sv_name, shelf_fi_name) VALUES (?, ?)");
                $result = $stmt->execute([$sv_name, $fi_name]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Hyllplats tillagd!',
                        'id' => $pdo->lastInsertId()
                    ];
                } else {
                    $response['message'] = 'Kunde inte lägga till hyllplats.';
                }
                break;
                
            case 'add_genre':
                $sv_name = trim($_POST['genre_sv_name'] ?? '');
                $fi_name = trim($_POST['genre_fi_name'] ?? '');
                
                if (empty($sv_name) || empty($fi_name)) {
                    $response['message'] = 'Både svenska och finska namn krävs.';
                    break;
                }
                
                $stmt = $pdo->prepare("INSERT INTO genre (genre_sv_name, genre_fi_name) VALUES (?, ?)");
                $result = $stmt->execute([$sv_name, $fi_name]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Genre tillagd!',
                        'id' => $pdo->lastInsertId()
                    ];
                } else {
                    $response['message'] = 'Kunde inte lägga till genre.';
                }
                break;
                
            case 'add_language':
                $sv_name = trim($_POST['language_sv_name'] ?? '');
                $fi_name = trim($_POST['language_fi_name'] ?? '');
                
                if (empty($sv_name) || empty($fi_name)) {
                    $response['message'] = 'Både svenska och finska namn krävs.';
                    break;
                }
                
                $stmt = $pdo->prepare("INSERT INTO language (language_sv_name, language_fi_name) VALUES (?, ?)");
                $result = $stmt->execute([$sv_name, $fi_name]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Språk tillagt!',
                        'id' => $pdo->lastInsertId()
                    ];
                } else {
                    $response['message'] = 'Kunde inte lägga till språk.';
                }
                break;
                
            case 'add_condition':
                $sv_name = trim($_POST['condition_sv_name'] ?? '');
                $fi_name = trim($_POST['condition_fi_name'] ?? '');
                $code = trim($_POST['condition_code'] ?? '');
                $description = trim($_POST['condition_description'] ?? '');
                
                if (empty($sv_name) || empty($fi_name) || empty($code)) {
                    $response['message'] = 'Både svenska och finska namn samt kod krävs.';
                    break;
                }
                
                $stmt = $pdo->prepare("INSERT INTO `condition` (condition_sv_name, condition_fi_name, condition_code, condition_description) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$sv_name, $fi_name, $code, $description]);
                
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Skick tillagt!',
                        'id' => $pdo->lastInsertId()
                    ];
                } else {
                    $response['message'] = 'Kunde inte lägga till skick.';
                }
                break;
                
            case 'edit':
                $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
                $type = isset($_POST['type']) ? $_POST['type'] : '';

                if ($id <= 0 || empty($type)) {
                    $response['message'] = 'Ogiltiga värden. ID och typ krävs.';
                    break;
                }

                // Handle author separately since it has different structure
                if ($type === 'author') {
                    $author_name = trim($_POST['author_name'] ?? '');
                    
                    if (empty($author_name)) {
                        $response['message'] = 'Författarnamn krävs.';
                        break;
                    }
                    
                    $stmt = $pdo->prepare("UPDATE author SET author_name = ? WHERE author_id = ?");
                    $result = $stmt->execute([$author_name, $id]);
                    $message = 'Författare uppdaterad!';
                } else {
                    // Handle other types with sv/fi names
                    $sv_name = trim($_POST['sv_name'] ?? '');
                    $fi_name = trim($_POST['fi_name'] ?? '');
                    
                    if (empty($sv_name) || empty($fi_name)) {
                        $response['message'] = 'Ogiltiga värden. Svenska och finska namn krävs.';
                        break;
                    }
                    
                    switch ($type) {
                        case 'category':
                            $stmt = $pdo->prepare("UPDATE category SET category_sv_name = ?, category_fi_name = ? WHERE category_id = ?");
                            $result = $stmt->execute([$sv_name, $fi_name, $id]);
                            $message = 'Kategori uppdaterad!';
                            break;
                            
                        case 'shelf':
                            $stmt = $pdo->prepare("UPDATE shelf SET shelf_sv_name = ?, shelf_fi_name = ? WHERE shelf_id = ?");
                            $result = $stmt->execute([$sv_name, $fi_name, $id]);
                            $message = 'Hyllplats uppdaterad!';
                            break;
                            
                        case 'genre':
                            $stmt = $pdo->prepare("UPDATE genre SET genre_sv_name = ?, genre_fi_name = ? WHERE genre_id = ?");
                            $result = $stmt->execute([$sv_name, $fi_name, $id]);
                            $message = 'Genre uppdaterad!';
                            break;
                            
                        case 'language':
                            $stmt = $pdo->prepare("UPDATE language SET language_sv_name = ?, language_fi_name = ? WHERE language_id = ?");
                            $result = $stmt->execute([$sv_name, $fi_name, $id]);
                            $message = 'Språk uppdaterat!';
                            break;
                            
                        case 'condition':
                            $code = trim($_POST['code'] ?? '');
                            $description = trim($_POST['description'] ?? '');
                            
                            $stmt = $pdo->prepare("UPDATE `condition` SET condition_sv_name = ?, condition_fi_name = ?, condition_code = ?, condition_description = ? WHERE condition_id = ?");
                            $result = $stmt->execute([$sv_name, $fi_name, $code, $description, $id]);
                            $message = 'Skick uppdaterat!';
                            break;
                            
                        default:
                            $response['message'] = 'Ogiltig typ: ' . $type;
                            break;
                    }
                }
                
                if (isset($result) && $result) {
                    $response = [
                        'success' => true,
                        'message' => $message,
                        'id' => $id,
                        'type' => $type
                    ];
                } else if (!isset($result)) {
                    // No $result variable was set
                    $response['message'] = 'Ogiltig typ angiven.';
                } else {
                    $response['message'] = 'Kunde inte uppdatera data.';
                }
                break;
                
            case 'delete':
                $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
                $type = isset($_POST['type']) ? $_POST['type'] : '';
                
                if ($id <= 0 || empty($type)) {
                    $response['message'] = 'Ogiltiga värden. ID och typ krävs.';
                    break;
                }
                
                // Check for dependencies before deleting
                $dependencies = checkDependencies($type, $id, $pdo);
                if ($dependencies['hasDependencies']) {
                    $response['message'] = $dependencies['message'];
                    break;
                }
                
                // If no dependencies, proceed with deletion
                switch ($type) {
                    case 'author':
                        $stmt = $pdo->prepare("DELETE FROM author WHERE author_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Författare har tagits bort!';
                        break;
                        
                    case 'category':
                        $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Kategori har tagits bort!';
                        break;
                        
                    case 'shelf':
                        $stmt = $pdo->prepare("DELETE FROM shelf WHERE shelf_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Hyllplats har tagits bort!';
                        break;
                        
                    case 'genre':
                        $stmt = $pdo->prepare("DELETE FROM genre WHERE genre_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Genre har tagits bort!';
                        break;
                        
                    case 'language':
                        $stmt = $pdo->prepare("DELETE FROM language WHERE language_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Språk har tagits bort!';
                        break;
                        
                    case 'condition':
                        $stmt = $pdo->prepare("DELETE FROM `condition` WHERE condition_id = ?");
                        $result = $stmt->execute([$id]);
                        $message = 'Skick har tagits bort!';
                        break;
                        
                    default:
                        $response['message'] = 'Ogiltig typ: ' . $type;
                        break;
                }
                
                if (isset($result) && $result) {
                    $response = [
                        'success' => true,
                        'message' => $message,
                        'id' => $id,
                        'type' => $type
                    ];
                } else if (!isset($result)) {
                    // No $result variable was set
                    $response['message'] = 'Ogiltig typ angiven.';
                } else {
                    $response['message'] = 'Kunde inte ta bort data.';
                }
                break;
                
            default:
                $response['message'] = 'Okänd åtgärd: ' . $action;
                break;
        }
    } catch (PDOException $e) {
        $response['message'] = 'Databasfel: ' . $e->getMessage();
        error_log('Database error in database_handler.php: ' . $e->getMessage());
    }
    
    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode($response);
        exit;
    }
    
    // For form submissions, set session message and redirect
    if ($response['success']) {
        $_SESSION['message'] = $response['message'];
    } else {
        $_SESSION['error'] = $response['message'];
    }
    
    // Redirect back to the admin page
    header('Location: ' . url('admin.php', ['tab' => 'tabledatamanagement']));
    exit;
}

/**
 * Function to check for dependencies before deletion
 * 
 * @param string $type The type of item
 * @param int $id The ID of the item
 * @param PDO $pdo Database connection
 * @return array Dependencies check result
 */
function checkDependencies($type, $id, $pdo) {
    $result = [
        'hasDependencies' => false,
        'message' => ''
    ];
    
    try {
        switch ($type) {
            case 'author':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_author WHERE author_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom författaren används av ' . $count . ' produkt(er).';
                }
                break;
                
            case 'category':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE category_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom den används av ' . $count . ' produkt(er).';
                }
                break;
                
            case 'shelf':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE shelf_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom den används av ' . $count . ' produkt(er).';
                }
                break;
                
            case 'genre':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_genre WHERE genre_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom den används av ' . $count . ' produkt(er).';
                }
                break;
                
            case 'language':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE language_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom det används av ' . $count . ' produkt(er).';
                }
                break;
                
            case 'condition':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM product WHERE condition_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $result['hasDependencies'] = true;
                    $result['message'] = 'Kan inte ta bort eftersom det används av ' . $count . ' produkt(er).';
                }
                break;
        }
    } catch (PDOException $e) {
        error_log('Error checking dependencies: ' . $e->getMessage());
        $result['hasDependencies'] = true; // Assume dependencies on error to be safe
        $result['message'] = 'Fel vid kontroll av beroenden. För säkerhets skull tilläts inte borttagningen.';
    }
    
    return $result;
}