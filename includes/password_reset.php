<?php
/**
 * Fixed Password Reset Handler
 * Addresses multiple issues in the current implementation
 */

require_once '../init.php';
require_once '../includes/Mailer.php';

// Load email configuration
$email_config = require_once '../config/email_config.php';

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
    global $pdo, $email_config;
    
    try {
        // Check if user exists and is active
        $stmt = $pdo->prepare("
            SELECT user_id, user_username, user_email 
            FROM user 
            WHERE user_email = ? AND user_is_active = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Always show same message for security
        $standardMessage = 'Om e-postadressen finns i vårt system har en återställningslänk skickats.';
        
        if (!$user) {
            sleep(2); // Mimic email sending time
            return [
                'success' => true,
                'message' => $standardMessage
            ];
        }
        
        // Check for recent reset requests (prevent spam)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM password_reset 
            WHERE user_id = ? 
            AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            AND used_at IS NULL
        ");
        $stmt->execute([$user['user_id']]);
        $recentRequests = $stmt->fetchColumn();
        
        if ($recentRequests >= 3) {
            return [
                'success' => false,
                'message' => 'För många återställningsförsök. Vänta 15 minuter innan du försöker igen.'
            ];
        }
        
        // Generate secure token
        $token = generateResetToken();
        $expires_at = date('Y-m-d H:i:s', time() + (2 * 3600)); // 2 hours
        
        // Store reset request
        $stmt = $pdo->prepare("
            INSERT INTO password_reset (user_id, email, token, expires_at, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt->execute([
            $user['user_id'],
            $user['user_email'],
            $token,
            $expires_at,
            $ip,
            $userAgent
        ]);
        
        // Create reset link - FIXED: Use proper domain/base path
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                   '://' . $_SERVER['HTTP_HOST'] . rtrim(BASE_PATH, '/');
        $reset_link = $baseUrl . '/includes/password_reset.php?action=reset&token=' . $token;
        
        // Send email
        $mailer = new Mailer($email_config);
        $email_result = $mailer->sendPasswordReset(
            $user['user_email'],
            $user['user_username'],
            $reset_link,
            $_SESSION['language'] ?? 'sv'
        );
        
        if ($email_result['success']) {
            // Log the request
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description)
                VALUES (?, 'password_reset_request', ?)
            ");
            $logStmt->execute([
                $user['user_id'],
                'Password reset requested for: ' . $user['user_email']
            ]);
            
            return [
                'success' => true,
                'message' => $standardMessage
            ];
        } else {
            // Email failed - remove token
            $stmt = $pdo->prepare("DELETE FROM password_reset WHERE token = ?");
            $stmt->execute([$token]);
            
            return [
                'success' => false,
                'message' => 'E-postmeddelandet kunde inte skickas. Försök igen senare.'
            ];
        }
        
    } catch (PDOException $e) {
        error_log("Password reset request error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Ett systemfel inträffade. Försök igen senare.'
        ];
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
 * Update password with token - FIXED VERSION
 */
function updatePasswordWithToken($token, $new_password) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Validate token
        $reset_data = validateResetToken($token);
        
        if (!$reset_data) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Ogiltigt eller utgånget återställningstoken.'
            ];
        }
        
        // Validate password
        if (strlen($new_password) < 8) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Lösenordet måste vara minst 8 tecken långt.'
            ];
        }
        
        // Hash password
        $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $pdo->prepare("UPDATE user SET user_password_hash = ? WHERE user_id = ?");
        $result = $stmt->execute([$passwordHash, $reset_data['user_id']]);
        
        if (!$result || $stmt->rowCount() == 0) {
            $pdo->rollBack();
            error_log("Password reset: Failed to update password for user_id " . $reset_data['user_id']);
            return [
                'success' => false,
                'message' => 'Fel vid uppdatering av lösenordet.'
            ];
        }
        
        // Mark token as used
        $stmt = $pdo->prepare("UPDATE password_reset SET used_at = NOW() WHERE token = ?");
        $stmt->execute([$token]);
        
        // Log the change
        $logStmt = $pdo->prepare("
            INSERT INTO event_log (user_id, event_type, event_description)
            VALUES (?, 'password_changed', ?)
        ");
        $logStmt->execute([
            $reset_data['user_id'],
            'Password changed via reset token for: ' . $reset_data['user_email']
        ]);
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Ditt lösenord har uppdaterats framgångsrikt. Du kan nu logga in med ditt nya lösenord.'
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Password update error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Ett fel inträffade vid uppdatering av lösenordet. Försök igen.'
        ];
    }
}

// Handle requests
$result = null;
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

// CSRF validation for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        checkCSRFToken();
    } catch (Exception $e) {
        error_log("CSRF validation failed in password reset: " . $e->getMessage());
        $_SESSION['message'] = [
            'success' => false,
            'message' => 'Säkerhetsvalidering misslyckades. Försök igen.'
        ];
        header('Location: ' . url('index.php'));
        exit;
    }
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'request') {
    // Rate limiting
    $rate_limit = checkRateLimit('password_reset', 3, 600);
    
    if (!$rate_limit['allowed']) {
        $result = [
            'success' => false,
            'message' => $rate_limit['message']
        ];
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = [
                'success' => false,
                'message' => 'Ange en giltig e-postadress.'
            ];
        } else {
            $result = createPasswordResetRequest($email);
        }
    }
    
    // Store message in session for display after redirect
    $_SESSION['message'] = $result;
    
    // Redirect back to homepage/login page
    header('Location: ' . url('index.php'));
    exit;
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $result = [
            'success' => false,
            'message' => 'Alla fält måste fyllas i.'
        ];
    } elseif ($new_password !== $confirm_password) {
        $result = [
            'success' => false,
            'message' => 'Lösenorden matchar inte.'
        ];
    } else {
        $result = updatePasswordWithToken($token, $new_password);
    }
    
    if ($result['success']) {
        // Store success message in session
        $_SESSION['message'] = $result;
        header('Location: ' . url('index.php'));
        exit;
    }
}

// Display reset form if valid token
if ($action === 'reset' && !empty($token)) {
    $reset_data = validateResetToken($token);
    
    if (!$reset_data) {
        $_SESSION['message'] = [
            'success' => false,
            'message' => 'Ogiltigt eller utgånget återställningstoken.'
        ];
        header('Location: ' . url('index.php'));
        exit;
    }
    
    // Include header
    $pageTitle = "Återställ lösenord - Karis Antikvariat";
    require_once '../templates/header.php';
    ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🔑 Återställ lösenord</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Ange ditt nya lösenord för: <strong><?php echo htmlspecialchars($reset_data['user_email']); ?></strong>
                        </p>
                        
                        <?php if ($result && !$result['success']): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($result['message']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="password-reset-form">
                            <?php echo getCSRFTokenField(); ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nytt lösenord</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                <div class="form-text">Minst 8 tecken</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Bekräfta nytt lösenord</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    💾 Uppdatera lösenord
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <a href="<?php echo url('index.php'); ?>">← Tillbaka till startsidan</a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>⏰ Länken upphör: <?php echo date('Y-m-d H:i', strtotime($reset_data['expires_at'])); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Client-side password validation
        document.getElementById('password-reset-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Check if passwords match
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Lösenorden matchar inte.');
                return false;
            }
            
            // Check password length
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Lösenordet måste vara minst 8 tecken långt.');
                return false;
            }
        });
    });
    </script>
    
    <?php
    require_once '../templates/footer.php';
    exit;
}

// If no valid action, redirect to home
header('Location: ' . url('index.php'));
exit;
?>