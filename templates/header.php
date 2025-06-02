<?php
/**
 * Header Template (Updated with CSRF Protection and Security Headers)
 * 
 * Contains:
 * - Site header with navigation
 * - Language switcher
 * - Login button
 * - CSRF protection
 */

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Get current user info if available and logged in
$currentUser = null;
if ($isLoggedIn && function_exists('getSessionUser')) {
    $currentUser = getSessionUser();
}

// Check for login error
$hasLoginError = isset($_GET['error']);
$loginError = $hasLoginError ? $_GET['error'] : '';

// If there was a login error, we'll show the modal automatically
$showLoginModal = $hasLoginError;

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
    
    <!-- Custom CSS - Using asset helper function -->
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
                <!-- Using url helper function -->
                <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
                    <i class="fas fa-book-open me-2"></i>Karis Antikvariat
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <!-- Using url helper function -->
                            <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" 
                               id="nav-home" 
                               href="<?php echo url('index.php'); ?>">
                                <?php echo $strings['menu_home']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Using url helper function with anchor -->
                            <a class="nav-link" 
                               id="nav-about" 
                               href="<?php echo url('index.php'); ?>#about">
                                <?php echo $strings['menu_about']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Using url helper function with anchor -->
                            <a class="nav-link" 
                               id="nav-browse" 
                               href="<?php echo url('index.php'); ?>#browse">
                                <?php echo $strings['menu_browse']; ?>
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <!-- Language Switcher with CSRF protection -->
                        <div class="language-switcher me-3">
                            <form method="POST" action="<?php echo url(''); ?>" class="d-inline">
                                <?php echo getCSRFTokenField(); ?>
                                <input type="hidden" name="lang" value="sv">
                                <button type="submit" class="btn btn-sm btn-outline-light <?php echo ($language == 'sv') ? 'active' : ''; ?> square-btn">
                                    SV
                                </button>
                            </form>
                            <form method="POST" action="<?php echo url(''); ?>" class="d-inline">
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
                            <form method="POST" action="<?php echo url('index.php'); ?>" class="d-inline">
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
                    <?php if ($hasLoginError): ?>
                    <div id="login-error" class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($loginError); ?>
                    </div>
                    <?php endif; ?>
                    <!-- Using url helper for form action with CSRF protection -->
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

    <!-- Forgot Password Modal with CSRF Protection -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?php echo $strings['reset_password']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4"><?php echo $strings['reset_instructions']; ?></p>
                    <!-- Using url helper for form action with CSRF protection -->
                    <form id="forgot-password-form" method="post" action="<?php echo url('includes/password_reset.php'); ?>">
                        <?php echo getCSRFTokenField(); ?>
                        <div class="mb-3">
                            <label for="recovery-email" class="form-label"><?php echo $strings['email']; ?></label>
                            <input type="email" class="form-control" id="recovery-email" name="email" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $strings['send_reset_link']; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($showLoginModal): ?>
    <!-- Script to show login modal on page load if there's an error -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    </script>
    <?php endif; ?>
    
    <script>
        // Add CSRF protection to all forms and AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to all forms that don't already have it
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                if (form.method.toLowerCase() === 'post' && !form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = window.CSRF_TOKEN;
                    form.appendChild(csrfInput);
                }
            });

            // Override fetch to automatically include CSRF token
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                if (options.method && options.method.toUpperCase() === 'POST') {
                    options.headers = options.headers || {};
                    
                    // Add CSRF token to headers
                    if (!options.headers['X-CSRF-Token']) {
                        options.headers['X-CSRF-Token'] = window.CSRF_TOKEN;
                    }
                    
                    // If body is FormData, add CSRF token to it
                    if (options.body instanceof FormData && !options.body.has('csrf_token')) {
                        options.body.append('csrf_token', window.CSRF_TOKEN);
                    }
                }
                
                return originalFetch(url, options);
            };
        });
    </script>
    
    <?php endif; ?>