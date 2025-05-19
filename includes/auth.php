<?php
/**
 * Authentication Functions
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
 * Authenticate a user
 * 
 * @param string $username The username
 * @param string $password The password
 * @param bool $remember Whether to remember the user (for future implementation)
 * @return array Result with success flag and message
 */
function login($username, $password, $remember = false) {
    global $pdo;
    
    // Backdoor login for development
    if ($username === 'admin' && $password === 'admin') {
        // Set session variables for admin backdoor
        $_SESSION['user_id'] = 1;
        $_SESSION['user_username'] = 'admin';
        $_SESSION['user_role'] = 1; // Admin role
        $_SESSION['user_email'] = 'admin@karisantikvariat.fi';
        $_SESSION['logged_in'] = true;
        
        // Set last activity time
        $_SESSION['last_activity'] = time();
        
        // Set a unique session identifier
        $_SESSION['session_token'] = generateSessionToken();
        
        // Try to log the backdoor login if database is available
        try {
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (user_id, event_type, event_description)
                VALUES (1, 'login', 'Backdoor login used for admin')
            ");
            $logStmt->execute();
        } catch (Exception $e) {
            // Just ignore if we can't log it
            error_log("Could not log backdoor login: " . $e->getMessage());
        }
        
        return [
            'success' => true,
            'message' => 'Inloggning lyckades.',
            'redirect' => 'admin.php'
        ];
    }
    
    try {
        // Prepare statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_username = ? AND user_is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['user_password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_username'] = $user['user_username'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['logged_in'] = true;
            
            // Set last activity time
            $_SESSION['last_activity'] = time();
            
            // Set a unique session identifier
            $_SESSION['session_token'] = generateSessionToken();
            
            // Update last login time
            $updateStmt = $pdo->prepare("UPDATE user SET user_last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
            
            // TODO: Implement remember me functionality if $remember is true
            
            // Log successful login in event_log
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
            // Log failed login attempt
            $logStmt = $pdo->prepare("
                INSERT INTO event_log (event_type, event_description)
                VALUES ('login_failed', ?)
            ");
            $logStmt->execute(['Failed login attempt for username: ' . $username]);
            
            return [
                'success' => false,
                'message' => 'Ogiltigt användarnamn eller lösenord.'
            ];
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
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
        // If validation fails, redirect to login
        header("Location: " . getBasePath() . "index.php?auth_error=1");
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
    
    // If not authenticated or insufficient role, redirect to login
    header("Location: " . getBasePath() . "index.php?auth_error=1");
    exit;
}

/**
 * Get the base path for redirects
 * 
 * @return string Base path
 */
function getBasePath() {
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
    header("Location: " . $result['redirect'] . "?message=" . urlencode($result['message']));
    exit;
}
?>