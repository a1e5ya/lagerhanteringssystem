<?php
/**
 * Admin Header Template
 * 
 * Contains:
 * - Admin header with navigation tabs
 * - User info and logout button
 */

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Get current user info if available
$currentUser = function_exists('getSessionUser') ? getSessionUser() : ['user_username' => 'Admin'];
$username = isset($currentUser['user_username']) ? $currentUser['user_username'] : 'Admin';
?><!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin - Karis Antikvariat'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <!-- Header/Navigation for Admin Pages -->
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php"><i class="fas fa-book-open me-2"></i>Karis Antikvariat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>" href="admin.php">Lager</a>
                        </li>
                        <?php if (isset($currentUser['user_role']) && $currentUser['user_role'] == 1): // Admin only ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'admin/usermanagement.php') ? 'active' : ''; ?>" href="admin/usermanagement.php">Användare</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="d-flex">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($username); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-1"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="?logout=1">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logga ut
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Login Modal (included but normally shouldn't show for logged-in users) -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Personalinloggning</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="login-error" class="alert alert-danger d-none" role="alert">
                        Ogiltigt användarnamn eller lösenord. Försök igen.
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
    
    <?php if (isset($pageTitle) && $currentPage == 'admin.php'): ?>
    <!-- Admin Page Title -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Lagerhanteringssystem</h1>
            <div>
                <span class="badge bg-secondary">
                    <i class="fas fa-calendar me-1"></i><?php echo date('Y-m-d'); ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>