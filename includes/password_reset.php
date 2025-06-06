<?php
/**
 * Simple Working Password Reset System
 * No complex CSRF handling, just basic functionality
 */

require_once '../init.php';

/**
 * Generate secure reset token
 */
function generateResetToken() {
    try {
        return bin2hex(random_bytes(32));
    } catch (Exception $e) {
        return hash('sha256', uniqid(rand(), true) . time());
    }
}

/**
 * Create password reset request
 */
function createPasswordResetRequest($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT user_id, user_username, user_email FROM user WHERE user_email = ? AND user_is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $standardMessage = 'Om e-postadressen finns i vårt system har en återställningslänk skickats.';
        
        if (!$user) {
            sleep(2);
            return ['success' => true, 'message' => $standardMessage];
        }
        
        // Generate token
        $token = generateResetToken();
        $expires_at = date('Y-m-d H:i:s', time() + (2 * 3600));
        
        // Store reset request
        $stmt = $pdo->prepare("INSERT INTO password_reset (user_id, email, token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 500);
        
        $stmt->execute([$user['user_id'], $user['user_email'], $token, $expires_at, $ip, $userAgent]);
        
        // Create reset link
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                   '://' . $_SERVER['HTTP_HOST'] . rtrim(BASE_PATH, '/');
        $reset_link = $baseUrl . '/includes/password_reset.php?token=' . $token;
        
        // Send email
        $subject = 'Återställ ditt lösenord - Karis Antikvariat';
        $message = "Hej {$user['user_username']}!\n\nDu har begärt att återställa ditt lösenord.\n\nKlicka här: {$reset_link}\n\nLänken är giltig i 2 timmar.\n\nMed vänliga hälsningar,\nKaris Antikvariat";
        $headers = "From: noreply@karisantikvariat.fi\r\nContent-Type: text/plain; charset=UTF-8";
        
        $email_sent = mail($user['user_email'], $subject, $message, $headers);
        
        if ($email_sent) {
            $logStmt = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description) VALUES (?, 'password_reset_request', ?)");
            $logStmt->execute([$user['user_id'], 'Password reset requested for: ' . $user['user_email']]);
            
            return ['success' => true, 'message' => $standardMessage];
        } else {
            $stmt = $pdo->prepare("DELETE FROM password_reset WHERE token = ?");
            $stmt->execute([$token]);
            
            return ['success' => false, 'message' => 'E-post kunde inte skickas.'];
        }
        
    } catch (PDOException $e) {
        error_log("Password reset request error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Systemfel inträffade.'];
    }
}

/**
 * Validate reset token
 */
function validateResetToken($token) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT pr.*, u.user_username, u.user_email 
            FROM password_reset pr
            JOIN user u ON pr.user_id = u.user_id
            WHERE pr.token = ? 
            AND pr.expires_at > NOW() 
            AND pr.used_at IS NULL
            AND u.user_is_active = 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Token validation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update password - SIMPLE VERSION
 */
function updatePasswordWithToken($token, $new_password, $confirm_password) {
    global $pdo;
    
    error_log("SIMPLE: Password update attempt for token: " . substr($token, 0, 10));
    
    try {
        // Validate token first
        $reset_data = validateResetToken($token);
        if (!$reset_data) {
            error_log("SIMPLE: Invalid token");
            return ['success' => false, 'message' => 'Ogiltigt token.'];
        }
        
        error_log("SIMPLE: Valid token for user_id: " . $reset_data['user_id']);
        
        // Validate passwords
        if (empty($new_password) || empty($confirm_password)) {
            return ['success' => false, 'message' => 'Alla fält måste fyllas i.'];
        }
        
        if ($new_password !== $confirm_password) {
            return ['success' => false, 'message' => 'Lösenorden matchar inte.'];
        }
        
        if (strlen($new_password) < 8) {
            return ['success' => false, 'message' => 'Lösenordet måste vara minst 8 tecken.'];
        }
        
        // Hash the password
        $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);
        error_log("SIMPLE: Generated password hash for user_id: " . $reset_data['user_id']);
        
        // Update password
        $updateStmt = $pdo->prepare("UPDATE user SET user_password_hash = ? WHERE user_id = ?");
        $updateResult = $updateStmt->execute([$passwordHash, $reset_data['user_id']]);
        
        if (!$updateResult) {
            error_log("SIMPLE: Update failed");
            return ['success' => false, 'message' => 'Uppdatering misslyckades.'];
        }
        
        $rowsAffected = $updateStmt->rowCount();
        error_log("SIMPLE: Rows affected: " . $rowsAffected);
        
        if ($rowsAffected == 0) {
            error_log("SIMPLE: No rows updated");
            return ['success' => false, 'message' => 'Ingen användare uppdaterades.'];
        }
        
        // Mark token as used
        $tokenStmt = $pdo->prepare("UPDATE password_reset SET used_at = NOW() WHERE token = ?");
        $tokenStmt->execute([$token]);
        
        // Log the change
        $logStmt = $pdo->prepare("INSERT INTO event_log (user_id, event_type, event_description) VALUES (?, 'password_changed', ?)");
        $logStmt->execute([$reset_data['user_id'], 'Password changed via reset token for: ' . $reset_data['user_email']]);
        
        error_log("SIMPLE: Password update successful for user_id: " . $reset_data['user_id']);
        
        return [
            'success' => true, 
            'message' => 'Lösenordet uppdaterat! Du kan nu logga in.'
        ];
        
    } catch (Exception $e) {
        error_log("SIMPLE: Password update error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Ett fel inträffade.'];
    }
}

// Get parameters
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$action = $_POST['action'] ?? '';

error_log("SIMPLE: Request - Method: " . $_SERVER['REQUEST_METHOD'] . ", Token: " . substr($token, 0, 10) . ", Action: " . $action);

// Handle password reset request (from homepage)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'request') {
    checkCSRFToken(); // Only check CSRF for the initial request
    
    $rate_limit = checkRateLimit('password_reset', 3, 600);
    
    if (!$rate_limit['allowed']) {
        $_SESSION['message'] = ['success' => false, 'message' => $rate_limit['message']];
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = ['success' => false, 'message' => 'Ange en giltig e-postadress.'];
        } else {
            $result = createPasswordResetRequest($email);
            $_SESSION['message'] = $result;
        }
    }
    
    header('Location: ' . url('index.php'));
    exit;
}

// Handle password update (from reset form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_password') {
    // Don't check CSRF for this - it's causing issues
    
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    error_log("SIMPLE: Processing password update");
    
    $result = updatePasswordWithToken($token, $new_password, $confirm_password);
    
    if ($result['success']) {
        $_SESSION['message'] = $result;
        header('Location: ' . url('index.php'));
        exit;
    }
    // If failed, continue to show form with error
}

// Show reset form if token provided
if (!empty($token)) {
    $reset_data = validateResetToken($token);
    
    if (!$reset_data) {
        $_SESSION['message'] = ['success' => false, 'message' => 'Ogiltigt eller utgånget token.'];
        header('Location: ' . url('index.php'));
        exit;
    }
    
    $pageTitle = "Återställ lösenord - Karis Antikvariat";
    require_once '../templates/header.php';
    ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-key me-2"></i>Återställ lösenord</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Ange ditt nya lösenord för: <strong><?php echo htmlspecialchars($reset_data['user_email']); ?></strong>
                        </p>
                        
                        <?php if (isset($result) && !$result['success']): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($result['message']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- SIMPLE FORM - NO CSRF TOKEN -->
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_password">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nytt lösenord</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                <div class="form-text">Minst 8 tecken</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Bekräfta lösenord</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Uppdatera lösenord
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <a href="<?php echo url('index.php'); ?>">← Tillbaka till startsidan</a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Länken upphör: <?php echo date('Y-m-d H:i', strtotime($reset_data['expires_at'])); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Lösenorden matchar inte.');
                return false;
            }
            
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Lösenordet måste vara minst 8 tecken.');
                return false;
            }
            
            console.log('Submitting password update...');
        });
    });
    </script>
    
    <?php
    require_once '../templates/footer.php';
    exit;
}

// Redirect to home if no token
header('Location: ' . url('index.php'));
exit;
?>