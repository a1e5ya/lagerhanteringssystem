<?php
/**
 * User Management
 * 
 * Contains:
 * - User administration
 * 
 * Functions:
 * - searchUser()
 * - renderUser()
 * - createUser()
 * - editUser()
 * - changeUserStatus()
 * - removeUser()
 * - renderEditUserForm()
 */

// Include configuration file
require_once '../config/config.php';

// Function definitions
function searchUser($searchTerm = null) {
    global $pdo;
    
    try {
        $query = "SELECT u.*, r.r_name 
                 FROM users u
                 JOIN roles r ON u.u_role_fk = r.r_id";
        
        $params = [];
        
        if (!empty($searchTerm)) {
            $query .= " WHERE u.u_name LIKE ? OR u.u_fname LIKE ? OR u.u_lname LIKE ? OR u.u_email LIKE ?";
            $searchParam = "%$searchTerm%";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        }
        
        $query .= " ORDER BY u.u_name ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in searchUser: " . $e->getMessage());
        return [];
    }
}

function renderUser($users) {
    if (empty($users)) {
        echo '<tr><td colspan="6" class="text-center text-muted py-3">Inga användare hittades.</td></tr>';
        return;
    }
    
    foreach ($users as $user) {
        $statusClass = $user['u_isactive'] ? 'text-success' : 'text-danger';
        $statusText = $user['u_isactive'] ? 'Aktiv' : 'Inaktiv';
        $lastLogin = !empty($user['u_lastlogin']) ? date('Y-m-d H:i', strtotime($user['u_lastlogin'])) : 'Aldrig';
        
        echo '<tr class="clickable-row" data-href="usermanagement.php?tab=edit&user_id=' . $user['u_id'] . '">';
        echo '<td>' . htmlspecialchars($user['u_name']) . '</td>';
        echo '<td>' . htmlspecialchars($user['u_fname'] . ' ' . $user['u_lname']) . '</td>';
        echo '<td>' . htmlspecialchars($user['u_email']) . '</td>';
        echo '<td>' . htmlspecialchars($user['r_name']) . '</td>';
        echo '<td class="' . $statusClass . '">' . $statusText . '</td>';
        echo '<td>' . $lastLogin . '</td>';
        echo '</tr>';
    }
}

function createUser($userData) {
    global $pdo;
    
    // Validate input data
    if (empty($userData['uname']) || empty($userData['ufname']) || 
        empty($userData['ulname']) || empty($userData['umail']) || 
        empty($userData['upass']) || empty($userData['upassrpt'])) {
        return ['success' => false, 'error' => 'Alla fält måste fyllas i.'];
    }
    
    if ($userData['upass'] !== $userData['upassrpt']) {
        return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
    }
    
    // Check if username or email already exists
    try {
        $stmt = $pdo->prepare("SELECT u_id FROM users WHERE u_name = ? OR u_email = ?");
        $stmt->execute([$userData['uname'], $userData['umail']]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Användarnamn eller e-post finns redan.'];
        }
        
        // Hash password
        $passwordHash = password_hash($userData['upass'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (u_name, u_fname, u_lname, u_email, u_password, u_role_fk, u_isactive, u_created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        
        $stmt->execute([
            $userData['uname'],
            $userData['ufname'],
            $userData['ulname'],
            $userData['umail'],
            $passwordHash,
            $userData['role']
        ]);
        
        return [
            'success' => true, 
            'message' => 'Användaren skapades framgångsrikt.',
            'user_id' => $pdo->lastInsertId()
        ];
        
    } catch (PDOException $e) {
        error_log("Error in createUser: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte skapa användaren.'];
    }
}

function editUser($userId, $userData) {
    global $pdo;
    
    // Validate input data
    if (empty($userData['uname']) || empty($userData['ufname']) || 
        empty($userData['ulname']) || empty($userData['umail'])) {
        return ['success' => false, 'error' => 'Alla obligatoriska fält måste fyllas i.'];
    }
    
    try {
        // Check if username or email already exists for another user
        $stmt = $pdo->prepare("SELECT u_id FROM users WHERE (u_name = ? OR u_email = ?) AND u_id != ?");
        $stmt->execute([$userData['uname'], $userData['umail'], $userId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Användarnamn eller e-post används redan av en annan användare.'];
        }
        
        // Update user basic info
        $query = "UPDATE users SET 
                  u_name = ?, 
                  u_fname = ?, 
                  u_lname = ?, 
                  u_email = ?, 
                  u_role_fk = ?, 
                  u_isactive = ?";
        
        $params = [
            $userData['uname'],
            $userData['ufname'],
            $userData['ulname'],
            $userData['umail'],
            $userData['role'],
            isset($userData['active']) ? 1 : 0
        ];
        
        // If password is provided, update it too
        if (!empty($userData['upass']) && !empty($userData['upassrpt'])) {
            if ($userData['upass'] !== $userData['upassrpt']) {
                return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
            }
            
            $passwordHash = password_hash($userData['upass'], PASSWORD_DEFAULT);
            $query .= ", u_password = ?";
            $params[] = $passwordHash;
        }
        
        $query .= " WHERE u_id = ?";
        $params[] = $userId;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        return ['success' => true, 'message' => 'Användaren uppdaterades framgångsrikt.'];
        
    } catch (PDOException $e) {
        error_log("Error in editUser: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte uppdatera användaren.'];
    }
}

function changeUserStatus($userId, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET u_isactive = ? WHERE u_id = ?");
        $stmt->execute([$status, $userId]);
        
        return [
            'success' => true, 
            'message' => 'Användarens status har ändrats till ' . ($status ? 'aktiv' : 'inaktiv') . '.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error in changeUserStatus: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte ändra användarens status.'];
    }
}

function removeUser($userId) {
    global $pdo;
    
    try {
        // First, check if this is the last admin user
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as admin_count 
            FROM users 
            WHERE u_role_fk = 1 AND u_isactive = 1
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("SELECT u_role_fk FROM users WHERE u_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If this is the last admin, don't allow deletion
        if ($result['admin_count'] <= 1 && $user['u_role_fk'] == 1) {
            return [
                'success' => false, 
                'error' => 'Kan inte ta bort den sista administratören.'
            ];
        }
        
        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE u_id = ?");
        $stmt->execute([$userId]);
        
        return [
            'success' => true, 
            'message' => 'Användaren har tagits bort.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error in removeUser: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte ta bort användaren.'];
    }
}

function renderEditUserForm($userId = null) {
    global $pdo;
    
    $userData = null;
    $formAction = "?tab=add";
    $submitButtonText = "Lägg till användare";
    $formTitle = "Lägg till ny användare";
    
    if ($userId) {
        // Fetch user data if editing
        try {
            $stmt = $pdo->prepare("
                SELECT u.*, r.r_name 
                FROM users u
                JOIN roles r ON u.u_role_fk = r.r_id
                WHERE u.u_id = ?
            ");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $formAction = "?tab=edit&user_id=" . $userId;
                $submitButtonText = "Spara ändringar";
                $formTitle = "Redigera användare: " . htmlspecialchars($userData['u_name']);
            }
        } catch (PDOException $e) {
            error_log("Error fetching user data: " . $e->getMessage());
        }
    }
    
    // Fetch all available roles
    $roles = [];
    try {
        $stmt = $pdo->query("SELECT r_id, r_name FROM roles ORDER BY r_level ASC");
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching roles: " . $e->getMessage());
    }
    
    // Render the form
    ?>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-user-<?php echo $userId ? 'edit' : 'plus'; ?> me-2"></i><?php echo $formTitle; ?></h4>
        </div>
        <div class="card-body">
            <form action="<?php echo $formAction; ?>" method="POST">
                <?php if ($userId): ?>
                    <input type="hidden" name="edit_user_id" value="<?php echo $userId; ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="uname" class="form-label">Användarnamn</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="uname" name="uname" 
                                value="<?php echo htmlspecialchars($userData['u_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Roll</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select id="role" name="role" class="form-select">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['r_id']; ?>" 
                                        <?php echo (isset($userData['u_role_fk']) && $userData['u_role_fk'] == $role['r_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['r_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="ufname" class="form-label">Förnamn</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" id="ufname" name="ufname" 
                                value="<?php echo htmlspecialchars($userData['u_fname'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="ulname" class="form-label">Efternamn</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" id="ulname" name="ulname" 
                                value="<?php echo htmlspecialchars($userData['u_lname'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="umail" class="form-label">E-post</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="umail" name="umail" 
                                value="<?php echo htmlspecialchars($userData['u_email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <?php if ($userId): ?>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                <?php echo (isset($userData['u_isactive']) && $userData['u_isactive'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="active">Aktivt konto</label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <hr>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="upass" class="form-label"><?php echo $userId ? 'Nytt lösenord' : 'Lösenord'; ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="upass" name="upass" <?php echo $userId ? '' : 'required'; ?>>
                        </div>
                        <div class="form-text">
                            Lösenord måste vara minst 8 tecken med 1 stor bokstav och 1 specialtecken.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="upassrpt" class="form-label">Bekräfta lösenord</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="upassrpt" name="upassrpt" <?php echo $userId ? '' : 'required'; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                    <?php if ($userId): ?>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        <i class="fas fa-trash-alt me-2"></i>Ta bort användare
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm(this.form)">
                        <i class="fas fa-eraser me-2"></i>Rensa
                    </button>
                    <?php endif; ?>
                    
                    <button type="submit" name="<?php echo $userId ? 'update_user' : 'create_user'; ?>" class="btn btn-primary">
                        <i class="fas fa-<?php echo $userId ? 'save' : 'user-plus'; ?> me-2"></i><?php echo $submitButtonText; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($userId): ?>
    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Bekräfta borttagning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Är du säker på att du vill ta bort användaren <strong><?php echo htmlspecialchars($userData['u_name'] ?? ''); ?></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Denna åtgärd kan inte ångras!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tillbaka</button>
                    <form action="?tab=edit&user_id=<?php echo $userId; ?>" method="POST">
                        <input type="hidden" name="delete_user_id" value="<?php echo $userId; ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger">Ta bort</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif;
}

// Simple authentication placeholder
function checkAuth($role = null) {
    // Placeholder implementation until real auth check is implemented
    return true;
}

// Check if user is authenticated and has admin permissions
$requireRole = 1; // Admin role
checkAuth($requireRole);

// Process form submissions
$error = null;
$success = null;

// Handle user creation
if (isset($_POST['create_user'])) {
    $userData = [
        'uname' => filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_SPECIAL_CHARS),
        'ufname' => filter_input(INPUT_POST, 'ufname', FILTER_SANITIZE_SPECIAL_CHARS),
        'ulname' => filter_input(INPUT_POST, 'ulname', FILTER_SANITIZE_SPECIAL_CHARS),
        'umail' => filter_input(INPUT_POST, 'umail', FILTER_SANITIZE_EMAIL),
        'upass' => $_POST['upass'] ?? '',
        'upassrpt' => $_POST['upassrpt'] ?? '',
        'role' => filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT) ?: 3 // Default to role 3 if invalid
    ];
    
    $result = createUser($userData);
    
    if ($result['success']) {
        $success = $result['message'];
        // Redirect to prevent form resubmission
        header("Location: usermanagement.php?tab=list&success=" . urlencode($success));
        exit;
    } else {
        $error = $result['error'];
    }
}

// Handle user update
if (isset($_POST['update_user']) && isset($_POST['edit_user_id'])) {
    $userId = filter_input(INPUT_POST, 'edit_user_id', FILTER_VALIDATE_INT);
    
    if ($userId) {
        $userData = [
            'uname' => filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_SPECIAL_CHARS),
            'ufname' => filter_input(INPUT_POST, 'ufname', FILTER_SANITIZE_SPECIAL_CHARS),
            'ulname' => filter_input(INPUT_POST, 'ulname', FILTER_SANITIZE_SPECIAL_CHARS),
            'umail' => filter_input(INPUT_POST, 'umail', FILTER_SANITIZE_EMAIL),
            'upass' => $_POST['upass'] ?? '',
            'upassrpt' => $_POST['upassrpt'] ?? '',
            'role' => filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT) ?: 3,
            'active' => isset($_POST['active']) ? 1 : 0
        ];
        
        $result = editUser($userId, $userData);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['error'];
        }
    }
}

// Handle user deletion
if (isset($_POST['delete_user']) && isset($_POST['delete_user_id'])) {
    $userId = filter_input(INPUT_POST, 'delete_user_id', FILTER_VALIDATE_INT);
    
    if ($userId) {
        $result = removeUser($userId);
        
        if ($result['success']) {
            $success = $result['message'];
            // Redirect to user list after deletion
            header("Location: usermanagement.php?tab=list&success=" . urlencode($success));
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

// Get current tab from URL parameter
$tab = $_GET['tab'] ?? 'list';
$userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

// Get success message from URL if redirected
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Include your existing header here
require_once '../templates/admin_header.php';
?>

<!-- Main Content Container -->
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Användarhantering</h2>

    </div>
    
    <!-- Messages -->
    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="userManagementTabs">
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'list' ? 'active' : ''; ?>" href="?tab=list">Användarlista</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab === 'add' ? 'active' : ''; ?>" href="?tab=add">Lägg till användare</a>
        </li>
        <?php if ($tab === 'edit' && $userId): ?>
        <li class="nav-item">
            <a class="nav-link active" href="#">Redigera användare</a>
        </li>
        <?php endif; ?>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content border border-top-0 p-4 bg-white">
        <?php if ($tab === 'list'): ?>
        <!-- User List Tab -->
        <div class="tab-pane fade show active" id="user-list">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="tab" value="list">
                        <input type="text" class="form-control me-2" name="search" 
                               placeholder="Sök efter användare..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">Sök</button>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Användarnamn</th>
                            <th>Namn</th>
                            <th>E-post</th>
                            <th>Roll</th>
                            <th>Status</th>
                            <th>Senaste inloggning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $searchTerm = $_GET['search'] ?? null;
                        $users = searchUser($searchTerm);
                        renderUser($users);
                        ?>
                    </tbody>
                </table>
            </div>
            
            <p class="text-muted mt-3">
                <i class="fas fa-info-circle me-2"></i>Klicka på en rad för att redigera användaren.
            </p>
        </div>
        
        <?php elseif ($tab === 'add'): ?>
        <!-- Add User Tab -->
        <div class="tab-pane fade show active" id="add-user">
            <?php renderEditUserForm(); ?>
        </div>
        
        <?php elseif ($tab === 'edit' && $userId): ?>
        <!-- Edit User Tab -->
        <div class="tab-pane fade show active" id="edit-user">
            <?php renderEditUserForm($userId); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // JavaScript to make table rows clickable
    document.addEventListener('DOMContentLoaded', function() {
        const clickableRows = document.querySelectorAll('.clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = this.dataset.href;
            });
        });
        
        // Function to reset form fields
        window.resetForm = function(form) {
            form.reset();
        };
    });
</script>

<?php
// Include your existing footer here
require_once '../templates/admin_footer.php';
?>