<?php
/**
 * Header Template
 * 
 * Contains:
 * - Site header with navigation
 * - Language switcher
 * - Login button
 */

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Get language from session or set default
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
?><!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Karis Antikvariat'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header/Navigation for Public Pages -->
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php"><i class="fas fa-book-open me-2"></i>Karis Antikvariat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" id="nav-home" href="index.php">Hem</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-about" href="index.php#about">Om oss</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-browse" href="index.php#browse">Bläddra böcker</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <!-- Language Switcher -->
                        <div class="language-switcher me-3">
                            <a href="?lang=sv" class="btn btn-sm btn-outline-light <?php echo ($lang == 'sv') ? 'active' : ''; ?> square-btn">SV</a>
                            <a href="?lang=fi" class="btn btn-sm btn-outline-light <?php echo ($lang == 'fi') ? 'active' : ''; ?> square-btn">FI</a>
                        </div>
                        <!-- Login Button -->
                        <a href="#" class="btn btn-outline-light login-btn" id="login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-user me-1"></i> Logga in
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Personalinloggning</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="login-error" class="alert alert-danger <?php echo isset($_GET['error']) ? '' : 'd-none'; ?>" role="alert">
                        <?php echo isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'Ogiltigt användarnamn eller lösenord. Försök igen.'; ?>
                    </div>
                    <form id="login-form" method="post" action="includes/login_process.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Användarnamn</label>
                            <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Lösenord</label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                <label class="form-check-label" for="remember-me">
                                    Kom ihåg mig
                                </label>
                            </div>
                            <a href="#" id="forgot-password" class="text-decoration-none">Glömt lösenord?</a>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Logga in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Återställ lösenord</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Ange din e-postadress så skickar vi instruktioner för att återställa ditt lösenord.</p>
                    <form id="forgot-password-form" method="post" action="includes/password_reset.php">
                        <div class="mb-3">
                            <label for="recovery-email" class="form-label">E-postadress</label>
                            <input type="email" class="form-control" id="recovery-email" name="email" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Skicka återställningslänk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>