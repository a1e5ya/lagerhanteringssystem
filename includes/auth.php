<?php
/**
 * Authentication Functions - SECURE VERSION (Backdoor Removed)
 * 
 * Contains:
 * - login() - Authenticates users
 * - logout() - Ends user session
 * - checkAuth() - Verifies authentication/authorization
 * - getSessionUser() - Gets user from session
 * - validateSession() - Validates session is still active
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define session constants
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('SESSION_NAME', 'KA_SESSION');


/**
 * Simple Brute Force Protection - Core Implementation Only
 * 
 * Add this to your existing auth.php file (replace the login function)
 */

/**
 * Simple brute force protection functionsgit 
 * No complex dashboard needed - just core protection
 */

/**
 * Check if IP has too many recent failed attempts
 */
function checkBruteForce($username = null) {
    global $pdo;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $timeWindow = 15; // 15 minutes
    $maxAttemptsIP = 10; // Max 10 attempts per IP
    $maxAttemptsUser = 5; // Max 5 attempts per username
    $lockoutTime = 30; // 30 minutes lockout
    
    try {
        // Check IP-based attempts
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempts, 
                   MAX(attempt_time) as last_attempt,
                   MAX(blocked_until) as blocked_until
            FROM login_attempts 
            WHERE ip_address = ? 
            AND success = 0 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$ip, $timeWindow]);
        $ipData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if IP is currently blocked
        if ($ipData['blocked_until'] && strtotime($ipData['blocked_until']) > time()) {
            $remainingTime = strtotime($ipData['blocked_until']) - time();
            return [
                'blocked' => true,
                'message' => 'För många inloggningsförsök från din IP. Försök igen om ' . 
                           ceil($remainingTime / 60) . ' minuter.',
                'remaining' => $remainingTime
            ];
        }
        
        // Check if IP should be blocked
        if ($ipData['attempts'] >= $maxAttemptsIP) {
            return [
                'blocked' => true,
                'message' => 'För många inloggningsförsök. Försök igen om ' . $lockoutTime . ' minuter.',
                'remaining' => $lockoutTime * 60
            ];
        }
        
        // Check username-based attempts if provided
        if ($username) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as attempts,
                       MAX(blocked_until) as blocked_until
                FROM login_attempts 
                WHERE username = ? 
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)
            ");
            $stmt->execute([$username, $timeWindow]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if user is currently blocked
            if ($userData['blocked_until'] && strtotime($userData['blocked_until']) > time()) {
                $remainingTime = strtotime($userData['blocked_until']) - time();
                return [
                    'blocked' => true,
                    'message' => 'Detta konto är tillfälligt låst. Försök igen om ' . 
                               ceil($remainingTime / 60) . ' minuter.',
                    'remaining' => $remainingTime
                ];
            }
            
            // Check if user should be blocked
            if ($userData['attempts'] >= $maxAttemptsUser) {
                return [
                    'blocked' => true,
                    'message' => 'För många inloggningsförsök för detta konto. Försök igen om ' . $lockoutTime . ' minuter.',
                    'remaining' => $lockoutTime * 60
                ];
            }
        }
        
        // Progressive delay based on attempts
        $delay = 0;
        if ($ipData['attempts'] >= 3) {
            $delay = min(($ipData['attempts'] - 2) * 2, 10); // Max 10 second delay
        }
        
        return [
            'blocked' => false,
            'delay' => $delay,
            'attempts_ip' => $ipData['attempts'],
            'attempts_user' => $userData['attempts'] ?? 0
        ];
        
    } catch (PDOException $e) {
        error_log("Brute force check error: " . $e->getMessage());
        return ['blocked' => false, 'delay' => 0];
    }
}

/**
 * Record login attempt
 */
function recordLoginAttempt($username, $success) {
    global $pdo;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    try {
        // Record the attempt
        $stmt = $pdo->prepare("
            INSERT INTO login_attempts (ip_address, username, success, user_agent, attempt_time) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$ip, $username, $success ? 1 : 0, $userAgent]);
        
        // If failed attempt, check if we need to block
        if (!$success) {
            $lockoutTime = 30; // 30 minutes
            $blockUntil = date('Y-m-d H:i:s', time() + ($lockoutTime * 60));
            
            // Check if IP should be blocked (10+ attempts in 15 minutes)
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as attempts
                FROM login_attempts 
                WHERE ip_address = ? 
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $stmt->execute([$ip]);
            $ipAttempts = $stmt->fetchColumn();
            
            if ($ipAttempts >= 10) {
                $stmt = $pdo->prepare("
                    UPDATE login_attempts 
                    SET blocked_until = ? 
                    WHERE ip_address = ? 
                    AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                ");
                $stmt->execute([$blockUntil, $ip]);
            }
            
            // Check if username should be blocked (5+ attempts in 15 minutes)
            if ($username) {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as attempts
                    FROM login_attempts 
                    WHERE username = ? 
                    AND success = 0 
                    AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                ");
                $stmt->execute([$username]);
                $userAttempts = $stmt->fetchColumn();
                
                if ($userAttempts >= 5) {
                    $stmt = $pdo->prepare("
                        UPDATE login_attempts 
                        SET blocked_until = ? 
                        WHERE username = ? 
                        AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                    ");
                    $stmt->execute([$blockUntil, $username]);
                }
            }
        } else {
            // On successful login, clear any blocks for this IP/user
            $stmt = $pdo->prepare("
                UPDATE login_attempts 
                SET blocked_until = NULL 
                WHERE ip_address = ? OR username = ?
            ");
            $stmt->execute([$ip, $username]);
        }
        
        // Cleanup old records occasionally (5% chance)
        if (rand(1, 100) <= 5) {
            $stmt = $pdo->prepare("
                DELETE FROM login_attempts 
                WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stmt->execute();
        }
        
    } catch (PDOException $e) {
        error_log("Record login attempt error: " . $e->getMessage());
    }
}

/**
 * Enhanced login function with simple brute force protection
 */
function login($username, $password, $remember = false) {
    global $pdo;
    
    // Check for brute force attempts
    $bruteCheck = checkBruteForce($username);
    
    if ($bruteCheck['blocked']) {
        recordLoginAttempt($username, false);
        return [
            'success' => false,
            'message' => $bruteCheck['message'],
            'blocked' => true
        ];
    }
    
    // Apply progressive delay if needed
    if (isset($bruteCheck['delay']) && $bruteCheck['delay'] > 0) {
        sleep($bruteCheck['delay']);
    }
    
    try {
        // Normal login process
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_username = ? AND user_is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['user_password_hash'])) {
            // Record successful attempt
            recordLoginAttempt($username, true);
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_username'] = $user['user_username'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            $_SESSION['session_token'] = generateSessionToken();
            
            // Update last login time
            $updateStmt = $pdo->prepare("UPDATE user SET user_last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
            
            // Log successful login
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description)
                VALUES (?, 'login', ?)
            ");
            $logStmt->execute([
                $user['user_id'],
                'Successful login for user: ' . $user['user_username']
            ]);
            
            return [
                'success' => true,
                'message' => 'Inloggning lyckades.',
                'redirect' => 'admin.php'
            ];
        } else {
            // Record failed attempt
            recordLoginAttempt($username, false);
            
            // Log failed attempt
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (event_type, event_description)
                VALUES ('login_failed', ?)
            ");
            $logStmt->execute(['Failed login attempt for username: ' . $username]);
            
            // Give user feedback about remaining attempts
            $remainingIP = max(0, 10 - ($bruteCheck['attempts_ip'] ?? 0));
            $remainingUser = max(0, 5 - ($bruteCheck['attempts_user'] ?? 0));
            
            $message = 'Ogiltigt användarnamn eller lösenord.';
            
            if (($bruteCheck['attempts_ip'] ?? 0) >= 3 || ($bruteCheck['attempts_user'] ?? 0) >= 2) {
                $minRemaining = min($remainingIP, $remainingUser);
                if ($minRemaining > 0) {
                    $message .= " Du har $minRemaining försök kvar.";
                }
            }
            
            return [
                'success' => false,
                'message' => $message
            ];
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        recordLoginAttempt($username, false);
        
        return [
            'success' => false,
            'message' => 'Ett systemfel inträffade. Försök igen senare.'
        ];
    }
}


/**
 * Generate a unique session token
 * 
 * @return string Unique session token
 */
function generateSessionToken() {
    return md5(uniqid(rand(), true));
}

/**
 * Validate if the session is still active
 * 
 * @return bool Whether session is valid
 */
function validateSession() {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Check if session has a token
    if (!isset($_SESSION['session_token'])) {
        return false;
    }
    
    // Check for session timeout
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    
    // Check if session has expired
    $current_time = time();
    if (($current_time - $_SESSION['last_activity']) > SESSION_LIFETIME) {
        logout();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = $current_time;
    
    return true;
}

/**
 * Log out the current user
 * 
 * @return array Result with success flag and message
 */
function logout() {
    // Log the logout if a user is logged in
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        
        try {
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description)
                VALUES (?, 'logout', ?)
            ");
            $logStmt->execute([
                $_SESSION['user_id'],
                'User logged out: ' . $_SESSION['user_username']
            ]);
        } catch (PDOException $e) {
            error_log("Logout logging error: " . $e->getMessage());
        }
    }
    
    // Clear all session variables
    $_SESSION = [];
    
    // If a session cookie is used, destroy it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            '/',  // Ensure the cookie path is root
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    return [
        'success' => true,
        'message' => 'Du har loggats ut.',
        'redirect' => 'index.php'
    ];
}

/**
 * Check if user is authenticated and has required role
 * 
 * @param int|null $requiredRole The minimum role level required (1=Admin, 2=Editor, 3=Guest)
 * @return bool Whether user is authenticated with required role
 */
function checkAuth($requiredRole = null) {
    // First validate the session
    if (!validateSession()) {
        // If validation fails, redirect to login using routing system
        header("Location: " . Routes::url('index.php', ['auth_error' => 1]));
        exit;
    }
    
    // If no role is required, simply check if logged in
    if ($requiredRole === null) {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Check if user is logged in and has at least the required role level
    // Lower numbers have higher privileges: 1=Admin, 2=Editor, 3=Guest
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_role'])) {
        // If user's role is less than or equal to required role, grant access
        // (lower number = higher privilege)
        if ($_SESSION['user_role'] <= $requiredRole) {
            return true;
        }
    }
    
    // If not authenticated or insufficient role, redirect to login using routing system
    header("Location: " . Routes::url('index.php', ['auth_error' => 1]));
    exit;
}

/**
 * Get the base path for redirects
 * 
 * @return string Base path
 */
function getAuthBasePath() {
    $currentPath = $_SERVER['PHP_SELF'];
    $inAdminDir = (strpos($currentPath, '/admin/') !== false);
    return $inAdminDir ? '../' : '';
}

/**
 * Get the current session user data
 * 
 * @return array|null User data or null if not logged in
 */
function getSessionUser() {
    // First validate the session
    if (!validateSession()) {
        return null;
    }
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return [
            'user_id' => $_SESSION['user_id'],
            'user_username' => $_SESSION['user_username'],
            'user_role' => $_SESSION['user_role'],
            'user_email' => $_SESSION['user_email']
        ];
    }
    
    return null;
}

// Handle logout requests
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $result = logout();
    // Use routing system for redirect
    header("Location: " . Routes::url($result['redirect'], ['message' => urlencode($result['message'])]));
    exit;
}
?>