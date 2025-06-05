<?php
/**
 * Header Template (MINIMAL UPDATE - ONLY PASSWORD RESET ADDED)
 * 
 * Your existing design is preserved - ONLY added password reset modal
 */

// Set security headers for ALL pages
setSecurityHeaders();

// Handle language switching for ALL pages with CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
    checkCSRFToken();
    $language = in_array($_POST['lang'], ['sv', 'fi']) ? $_POST['lang'] : 'sv';
    $_SESSION['language'] = $language;
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle logout with CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    checkCSRFToken();
    $result = logout();
    header('Location: ' . url($result['redirect'], ['message' => urlencode($result['message'])]));
    exit;
}

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Get current user info if available and logged in
$currentUser = null;
if ($isLoggedIn && function_exists('getSessionUser')) {
    $currentUser = getSessionUser();
}

// Check for messages from URL parameters
$successMessage = $_GET['success'] ?? null;
$errorMessage = $_GET['error'] ?? null;
$showLoginModal = isset($_GET['auth_error']);

// Generate CSRF token for this page
$csrfToken = generateCSRFToken();
?><!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Karis Antikvariat'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css', 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css', 'pagination.css'); ?>">
    
    <!-- CSRF Token and Base URL for JavaScript -->
    <script>
        window.CSRF_TOKEN = '<?php echo $csrfToken; ?>';
        window.BASE_URL = '<?php echo rtrim(BASE_PATH, '/'); ?>';
    </script>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header/Navigation for Public Pages -->
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
                    <i class="fas fa-book-open me-2"></i>Karis Antikvariat
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" 
                               id="nav-home" 
                               href="<?php echo url('index.php'); ?>">
                                <?php echo $strings['menu_home']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" 
                               id="nav-about" 
                               href="<?php echo url('index.php'); ?>#about">
                                <?php echo $strings['menu_about']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" 
                               id="nav-browse" 
                               href="<?php echo url('index.php'); ?>#browse">
                                <?php echo $strings['menu_browse']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'sale.php') ? 'active' : ''; ?>" 
                               href="<?php echo url('sale.php'); ?>">
                                <i class="fas fa-tags me-1"></i><?php echo $strings['sale'] ?? 'Rea'; ?>
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <!-- Language Switcher with CSRF protection -->
                        <div class="language-switcher me-3">
                            <form method="POST" action="" class="d-inline">
                                <?php echo getCSRFTokenField(); ?>
                                <input type="hidden" name="lang" value="sv">
                                <button type="submit" class="btn btn-sm btn-outline-light <?php echo ($language == 'sv') ? 'active' : ''; ?> square-btn">
                                    SV
                                </button>
                            </form>
                            <form method="POST" action="" class="d-inline">
                                <?php echo getCSRFTokenField(); ?>
                                <input type="hidden" name="lang" value="fi">
                                <button type="submit" class="btn btn-sm btn-outline-light <?php echo ($language == 'fi') ? 'active' : ''; ?> square-btn">
                                    FI
                                </button>
                            </form>
                        </div>
                        
                        <?php if ($isLoggedIn): ?>
                            <!-- Admin Button (only when logged in) -->
                            <?php if ($currentUser && isset($currentUser['user_username'])): ?>
                            <a href="<?php echo url('admin.php'); ?>" class="btn btn-outline-light me-2">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($currentUser['user_username']); ?>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Logout Button with CSRF protection -->
                            <form method="POST" action="" class="d-inline">
                                <?php echo getCSRFTokenField(); ?>
                                <input type="hidden" name="logout" value="1">
                                <button type="submit" class="btn btn-outline-light">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logga ut
                                </button>
                            </form>
                        <?php else: ?>
                            <!-- Login Button -->
                            <a href="#" class="btn btn-outline-light login-btn" id="login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-user me-1"></i> <?php echo $strings['login']; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Global Messages -->
    <?php if ($successMessage): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$isLoggedIn): ?>
    <!-- Login Modal with CSRF Protection -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title"><?php echo $strings['staff_login']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="login-form" method="post" action="<?php echo url('includes/login_process.php'); ?>">
                        <?php echo getCSRFTokenField(); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label"><?php echo $strings['username']; ?></label>
                            <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo $strings['password']; ?></label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    <?php echo $strings['remember_me'] ?? 'Kom ihåg mig'; ?>
                                </label>
                            </div>
                            <a href="#" id="forgot-password" class="text-decoration-none"><?php echo $strings['forgot_password']; ?></a>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $strings['login_button']; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal - NEW ADDITION -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i><?php echo $strings['reset_password']; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $strings['reset_instructions']; ?>
                    </p>
                    
                    <form method="post" action="<?php echo url('includes/password_reset.php'); ?>">
                        <?php echo getCSRFTokenField(); ?>
                        <input type="hidden" name="action" value="request">
                        
                        <div class="mb-3">
                            <label for="recovery-email" class="form-label">
                                <i class="fas fa-envelope me-1"></i><?php echo $strings['email']; ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input type="email" class="form-control" id="recovery-email" name="email" required 
                                       placeholder="din@email.com" autocomplete="email">
                            </div>
                            <div class="form-text">
                                Vi skickar en återställningslänk till denna e-postadress.
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i><?php echo $strings['send_reset_link']; ?>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Dina uppgifter är säkra och skyddade.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Stäng
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="back-to-login">
                        <i class="fas fa-arrow-left me-1"></i>Tillbaka till inloggning
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showLoginModal): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    </script>
    <?php endif; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password reset functionality - NEW ADDITION
            document.getElementById('forgot-password').addEventListener('click', function(e) {
                e.preventDefault();
                
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                const forgotModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                
                if (loginModal) {
                    loginModal.hide();
                }
                
                setTimeout(() => {
                    forgotModal.show();
                }, 300);
            });
            
            // Back to login button - NEW ADDITION
            document.getElementById('back-to-login').addEventListener('click', function() {
                const forgotModal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                
                forgotModal.hide();
                setTimeout(() => {
                    loginModal.show();
                }, 300);
            });
            
            // Auto-dismiss global alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 8000);
        });
    </script>
    
    <?php endif; ?>