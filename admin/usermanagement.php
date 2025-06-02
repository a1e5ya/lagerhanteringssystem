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



// Set page title
$pageTitle = "Användarhantering - Karis Antikvariat";


// Include initialization file
require_once '../init.php';
require_once '../templates/admin_header.php';

// Check if user is authenticated and has admin permissions
// Only Admin (1) role can access this page
checkAuth(1); // 1 or lower (Admin only) role required

// Function definitions
function searchUser($searchTerm = null) {
    global $pdo;
    
    try {
        // Updated query to match your actual database schema
        $query = "SELECT * FROM `user`";
        
        $params = [];
        
        if (!empty($searchTerm)) {
            $query .= " WHERE user_username LIKE ? OR user_email LIKE ?";
            $searchParam = "%$searchTerm%";
            $params = [$searchParam, $searchParam];
        }
        
        $query .= " ORDER BY user_username ASC";
        
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
        $statusClass = $user['user_is_active'] ? 'text-success' : 'text-danger';
        $statusText = $user['user_is_active'] ? 'Aktiv' : 'Inaktiv';
        $lastLogin = !empty($user['user_last_login']) ? date('Y-m-d H:i', strtotime($user['user_last_login'])) : 'Aldrig';
        
        // Map role numbers to names
        $roleName = 'Gäst';
        switch ($user['user_role']) {
            case 1:
                $roleName = 'Admin';
                break;
            case 2:
                $roleName = 'Redaktör';
                break;
        }
        
        echo '<tr class="clickable-row" data-href="usermanagement.php?tab=edit&user_id=' . $user['user_id'] . '">';
        echo '<td>' . htmlspecialchars($user['user_username']) . '</td>';
        // Since we don't have first/last name in the schema, just show username
        echo '<td>' . htmlspecialchars($user['user_username']) . '</td>';
        echo '<td>' . htmlspecialchars($user['user_email']) . '</td>';
        echo '<td>' . htmlspecialchars($roleName) . '</td>';
        echo '<td class="' . $statusClass . '">' . $statusText . '</td>';
        echo '<td>' . $lastLogin . '</td>';
        echo '</tr>';
    }
}

function createUser($userData) {
    global $pdo;
    
    // Validate input data
    if (empty($userData['username']) || empty($userData['email']) || 
        empty($userData['password']) || empty($userData['password_confirm'])) {
        return ['success' => false, 'error' => 'Alla fält måste fyllas i.'];
    }
    
    if ($userData['password'] !== $userData['password_confirm']) {
        return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
    }
    
    // Check if username or email already exists
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE user_username = ? OR user_email = ?");
        $stmt->execute([$userData['username'], $userData['email']]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Användarnamn eller e-post finns redan.'];
        }
        
        // Hash password
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO user (user_username, user_password_hash, user_email, user_role, user_is_active, user_created_at) 
            VALUES (?, ?, ?, ?, 1, NOW())
        ");
        
        $stmt->execute([
            $userData['username'],
            $passwordHash,
            $userData['email'],
            $userData['role']
        ]);
        
        // Log the creation in event_log
        $userId = $pdo->lastInsertId();
        $currentUser = getSessionUser();
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (?, 'create_user', ?)
        ");
        $logStmt->execute([
            $currentUser['user_id'],
            'User created: ' . $userData['username']
        ]);
        
        return [
            'success' => true, 
            'message' => 'Användaren skapades framgångsrikt.',
            'user_id' => $userId
        ];
        
    } catch (PDOException $e) {
        error_log("Error in createUser: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte skapa användaren.'];
    }
}

function editUser($userId, $userData) {
    global $pdo;
    
    // Validate input data
    if (empty($userData['username']) || empty($userData['email'])) {
        return ['success' => false, 'error' => 'Alla obligatoriska fält måste fyllas i.'];
    }
    
    try {
        // Get current logged-in user
        $currentUser = getSessionUser();
        
        // Check if user is trying to deactivate themselves
        $isActive = (isset($userData['active']) && $userData['active'] == 1) ? 1 : 0;
        if ($currentUser['user_id'] == $userId && $isActive == 0) {
            return [
                'success' => false, 
                'error' => 'Du kan inte inaktivera ditt eget konto.'
            ];
        }
        
        // Check if username or email already exists for another user
        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE (user_username = ? OR user_email = ?) AND user_id != ?");
        $stmt->execute([$userData['username'], $userData['email'], $userId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Användarnamn eller e-post används redan av en annan användare.'];
        }
        
        // Get original user data for logging changes
        $getOriginalStmt = $pdo->prepare("SELECT user_username, user_role, user_is_active FROM user WHERE user_id = ?");
        $getOriginalStmt->execute([$userId]);
        $originalUser = $getOriginalStmt->fetch(PDO::FETCH_ASSOC);
        
        // If trying to deactivate the last admin, prevent it
        if ($originalUser['user_role'] == 1 && $isActive == 0) {
            $activeAdminStmt = $pdo->prepare("
                SELECT COUNT(*) as admin_count 
                FROM user 
                WHERE user_role = 1 AND user_is_active = 1 AND user_id != ?
            ");
            $activeAdminStmt->execute([$userId]);
            $adminResult = $activeAdminStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminResult['admin_count'] < 1) {
                return [
                    'success' => false, 
                    'error' => 'Kan inte inaktivera den sista aktiva administratören.'
                ];
            }
        }
        
        // Update user basic info
        $query = "UPDATE user SET 
                  user_username = ?, 
                  user_email = ?, 
                  user_role = ?, 
                  user_is_active = ?";
        
        $params = [
            $userData['username'],
            $userData['email'],
            $userData['role'],
            $isActive
        ];
        
        // If password is provided, update it too
        if (!empty($userData['password']) && !empty($userData['password_confirm'])) {
            if ($userData['password'] !== $userData['password_confirm']) {
                return ['success' => false, 'error' => 'Lösenorden matchar inte.'];
            }
            
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
            $query .= ", user_password_hash = ?";
            $params[] = $passwordHash;
        }
        
        $query .= " WHERE user_id = ?";
        $params[] = $userId;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        // Log the change
        $changesDesc = [];
        
        if ($originalUser['user_username'] != $userData['username']) {
            $changesDesc[] = "username changed from '{$originalUser['user_username']}' to '{$userData['username']}'";
        }
        
        if ($originalUser['user_role'] != $userData['role']) {
            $roleBefore = ($originalUser['user_role'] == 1) ? 'Admin' : (($originalUser['user_role'] == 2) ? 'Redaktör' : 'Gäst');
            $roleAfter = ($userData['role'] == 1) ? 'Admin' : (($userData['role'] == 2) ? 'Redaktör' : 'Gäst');
            $changesDesc[] = "role changed from '{$roleBefore}' to '{$roleAfter}'";
        }
        
        if ($originalUser['user_is_active'] != $isActive) {
            $statusBefore = $originalUser['user_is_active'] ? 'active' : 'inactive';
            $statusAfter = $isActive ? 'active' : 'inactive';
            $changesDesc[] = "status changed from '{$statusBefore}' to '{$statusAfter}'";
        }
        
        if (!empty($userData['password'])) {
            $changesDesc[] = "password was updated";
        }
        
        $changesText = !empty($changesDesc) ? implode(', ', $changesDesc) : 'no changes made';
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (?, 'update_user', ?)
        ");
        $logStmt->execute([
            $currentUser['user_id'],
            "User updated: {$userData['username']} - $changesText"
        ]);
        
        return ['success' => true, 'message' => 'Användaren uppdaterades framgångsrikt.'];
        
    } catch (PDOException $e) {
        error_log("Error in editUser: " . $e->getMessage());
        return ['success' => false, 'error' => 'Databasfel: Kunde inte uppdatera användaren.'];
    }
}

function changeUserStatus($userId, $status) {
    global $pdo;
    
    try {
        // Get current logged-in user
        $currentUser = getSessionUser();
        
        // Prevent user from deactivating themselves
        if ($currentUser['user_id'] == $userId && $status == 0) {
            return [
                'success' => false, 
                'error' => 'Du kan inte inaktivera ditt eget konto.'
            ];
        }
        
        // Get original user data for logging
        $getOriginalStmt = $pdo->prepare("SELECT user_username, user_role FROM user WHERE user_id = ?");
        $getOriginalStmt->execute([$userId]);
        $userData = $getOriginalStmt->fetch(PDO::FETCH_ASSOC);
        
        // If trying to deactivate the last admin, prevent it
        if ($userData['user_role'] == 1 && $status == 0) {
            $activeAdminStmt = $pdo->prepare("
                SELECT COUNT(*) as admin_count 
                FROM user 
                WHERE user_role = 1 AND user_is_active = 1 AND user_id != ?
            ");
            $activeAdminStmt->execute([$userId]);
            $adminResult = $activeAdminStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminResult['admin_count'] < 1) {
                return [
                    'success' => false, 
                    'error' => 'Kan inte inaktivera den sista aktiva administratören.'
                ];
            }
        }
        
        $stmt = $pdo->prepare("UPDATE user SET user_is_active = ? WHERE user_id = ?");
        $stmt->execute([$status, $userId]);
        
        // Log the status change
        $statusText = $status ? 'activated' : 'deactivated';
        
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (?, 'update_user_status', ?)
        ");
        $logStmt->execute([
            $currentUser['user_id'],
            "User $statusText: {$userData['user_username']}"
        ]);
        
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
        // Get current logged-in user
        $currentUser = getSessionUser();
        
        // Prevent user from deleting themselves
        if ($currentUser['user_id'] == $userId) {
            return [
                'success' => false, 
                'error' => 'Du kan inte ta bort ditt eget konto.'
            ];
        }
        
        // First, get username for logging
        $getUserStmt = $pdo->prepare("SELECT user_username, user_role FROM user WHERE user_id = ?");
        $getUserStmt->execute([$userId]);
        $userData = $getUserStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            return ['success' => false, 'error' => 'Användaren kunde inte hittas.'];
        }
        
        // Check if this is the last admin user
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as admin_count 
            FROM user 
            WHERE user_role = 1 AND user_is_active = 1 AND user_id != ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If this would be deleting the last admin, don't allow it
        if ($result['admin_count'] < 1 && $userData['user_role'] == 1) {
            return [
                'success' => false, 
                'error' => 'Kan inte ta bort den sista administratören.'
            ];
        }
        
        // Log the deletion before actually deleting
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (?, 'delete_user', ?)
        ");
        $logStmt->execute([
            $currentUser['user_id'],
            "User deleted: {$userData['user_username']}"
        ]);
        
        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
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


/**
 * Enhanced renderEditUserForm function
 * Add this modification to your usermanagement.php
 */

function renderEditUserForm($userId = null) {
    global $pdo;
    
    $userData = null;
    $formAction = url('admin/usermanagement.php', ['tab' => 'add']);
    $submitButtonText = "Lägg till användare";
    $formTitle = "Lägg till ny användare";
    $currentUser = getSessionUser();
    $isCurrentUser = false;
    
    if ($userId) {
        // Fetch user data if editing
        try {
            $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $formAction = url('admin/usermanagement.php', ['tab' => 'edit', 'user_id' => $userId]);
                $submitButtonText = "Spara ändringar";
                $formTitle = "Redigera användare: " . htmlspecialchars($userData['user_username']);
                $isCurrentUser = ($currentUser['user_id'] == $userId);
            }
        } catch (PDOException $e) {
            error_log("Error fetching user data: " . $e->getMessage());
        }
    }
    
    // Define available roles
    $roles = [
        ['r_id' => 1, 'r_name' => 'Admin'],
        ['r_id' => 2, 'r_name' => 'Redaktör'],
        ['r_id' => 3, 'r_name' => 'Gäst']
    ];
    
    // Check if this is the last admin
    $isLastAdmin = false;
    if ($userData && $userData['user_role'] == 1) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as admin_count 
            FROM user 
            WHERE user_role = 1 AND user_is_active = 1 AND user_id != ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $isLastAdmin = ($result['admin_count'] < 1);
    }
    
    ?>
    <div class="card-body">
        <?php if ($isCurrentUser): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Observera:</strong> Du redigerar ditt eget konto. Du kan inte inaktivera eller ta bort ditt eget konto av säkerhetsskäl.
        </div>
        <?php endif; ?>
        
        <?php if ($isLastAdmin): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Varning:</strong> Detta är den sista aktiva administratören. Kontot kan inte inaktiveras eller tas bort.
        </div>
        <?php endif; ?>
        
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
                            value="<?php echo htmlspecialchars($userData['user_username'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Roll</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select id="role" name="role" class="form-select">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['r_id']; ?>" 
                                    <?php echo (isset($userData['user_role']) && $userData['user_role'] == $role['r_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['r_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="umail" class="form-label">E-post</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="umail" name="umail" 
                            value="<?php echo htmlspecialchars($userData['user_email'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                <?php if ($userId): ?>
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="active" name="active" 
                            <?php echo (isset($userData['user_is_active']) && $userData['user_is_active'] == 1) ? 'checked' : ''; ?>
                            <?php echo ($isCurrentUser || $isLastAdmin) ? 'disabled' : ''; ?>>
                        <label class="form-check-label" for="active">
                            Aktivt konto
                            <?php if ($isCurrentUser): ?>
                                <small class="text-muted">(kan inte ändras för eget konto)</small>
                            <?php elseif ($isLastAdmin): ?>
                                <small class="text-muted">(sista admin kan inte inaktiveras)</small>
                            <?php endif; ?>
                        </label>
                    </div>
                <?php endif; ?>
                </div>
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
                <button type="button" class="btn btn-danger" 
                        data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                        <?php echo ($isCurrentUser || $isLastAdmin) ? 'disabled' : ''; ?>>
                    <i class="fas fa-trash-alt me-2"></i>Ta bort användare
                    <?php if ($isCurrentUser): ?>
                        <small>(inte tillgängligt för eget konto)</small>
                    <?php elseif ($isLastAdmin): ?>
                        <small>(sista admin kan inte tas bort)</small>
                    <?php endif; ?>
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
    
    <?php if ($userId && !$isCurrentUser && !$isLastAdmin): ?>
    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Bekräfta borttagning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Är du säker på att du vill ta bort användaren <strong><?php echo htmlspecialchars($userData['user_username'] ?? ''); ?></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Denna åtgärd kan inte ångras!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tillbaka</button>
                    <form action="<?php echo url('admin/usermanagement.php', ['tab' => 'edit', 'user_id' => $userId]); ?>" method="POST">
                        <input type="hidden" name="delete_user_id" value="<?php echo $userId; ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger">Ta bort</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- User Activity Log Section -->
    <?php if ($userId): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Användarens aktivitetslogg</h5>
        </div>
        <div class="card-body">
            <!-- Your existing activity log code here -->
        </div>
    </div>
    <?php endif; ?>
    
    <?php
}


// Process form submissions
$error = null;
$success = null;

// Handle user creation
if (isset($_POST['create_user'])) {
    $userData = [
        'username' => filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_SPECIAL_CHARS),
        'email' => filter_input(INPUT_POST, 'umail', FILTER_SANITIZE_EMAIL),
        'password' => $_POST['upass'] ?? '',
        'password_confirm' => $_POST['upassrpt'] ?? '',
        'role' => filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT) ?: 3 // Default to role 3 if invalid
    
    ];
    
    $result = createUser($userData);
    
    if ($result['success']) {
        $success = $result['message'];
        // Redirect to prevent form resubmission
        header("Location: " . url('admin/usermanagement.php', ['tab' => 'list', 'success' => urlencode($success)]));
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
            'username' => filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'umail', FILTER_SANITIZE_EMAIL),
            'password' => $_POST['upass'] ?? '',
            'password_confirm' => $_POST['upassrpt'] ?? '',
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
        <a class="user-nav-link <?php echo $tab === 'list' ? 'active' : ''; ?>" 
   href="<?php echo url('admin/usermanagement.php', ['tab' => 'list']); ?>">Användarlista</a>
    </li>
    <li class="nav-item">
        <a class="user-nav-link <?php echo $tab === 'add' ? 'active' : ''; ?>" 
   href="<?php echo url('admin/usermanagement.php', ['tab' => 'add']); ?>">Lägg till användare</a>
    </li>
    <?php if ($tab === 'edit' && $userId): ?>
    <li class="nav-item">
        <a class="user-nav-link active" href="#">Redigera användare</a>
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
                    <form method="GET" action="<?php echo url('admin/usermanagement.php'); ?>" class="d-flex">
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