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
$currentUser = function_exists('getSessionUser') ? getSessionUser() : null;

// If user is not logged in or doesn't have proper permissions, redirect
if (!$currentUser || ($currentUser['user_role'] > 2)) {
    // Redirect to home with error
    header("Location: index.php?auth_error=1");
    exit;
}

$username = $currentUser['user_username'];
$userRole = $currentUser['user_role'];

// Session check URL - using absolute path
$sessionCheckUrl = "/prog23/lagerhanteringssystem/includes/session_check.php";
?><!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin - Karis Antikvariat'; ?></title>
    <link rel="icon" type="image/png" href="/prog23/lagerhanteringssystem/assets/images/favicon.png">
    <link rel="icon" type="image/x-icon" href="/prog23/lagerhanteringssystem/assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/prog23/lagerhanteringssystem/assets/css/styles.css">
    <link rel="stylesheet" href="/prog23/lagerhanteringssystem/assets/css/admin.css">
    <link rel="stylesheet" href="/prog23/lagerhanteringssystem/assets/css/pagination.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Session check script -->
    <script>
        // Function to check session status
        function checkSession() {
            fetch('<?php echo $sessionCheckUrl; ?>')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        // If session is not valid, redirect to login page
                        window.location.href = '/prog23/lagerhanteringssystem/index.php?auth_error=2';
                    }
                })
                .catch(error => {
                    console.error('Session check error:', error);
                });
        }
        
        // Check session every 30 seconds
        setInterval(checkSession, 30000);
        
        // Also check when page gains focus (user switches back to this tab)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                checkSession();
            }
        });
    </script>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header/Navigation for Admin Pages -->
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="/prog23/lagerhanteringssystem/index.php"><i class="fas fa-book-open me-2"></i>Karis Antikvariat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-item-link <?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>" href="/prog23/lagerhanteringssystem/admin.php">Lager</a>
                        </li>
                        <?php if ($userRole == 1): // Admin only ?>
                        <li class="nav-item">
                            <a class="nav-item-link <?php echo ($currentPage == 'usermanagement.php') ? 'active' : ''; ?>" href="/prog23/lagerhanteringssystem/admin/usermanagement.php">Användare</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    </ul>
                    <div class="d-flex align-items-center">
                        <!-- User info and logout button -->
                        <span class="text-light me-3">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($username); ?>
                        </span>
                        <a class="btn btn-outline-light" href="/prog23/lagerhanteringssystem/index.php?logout=1">
                            <i class="fas fa-sign-out-alt me-1"></i>Logga ut
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <?php if (isset($pageTitle) && $currentPage == 'admin.php'): ?>
    <!-- Admin Page Title -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Lagerhanteringssystem</h2>
            <div>
                <span class="badge bg-secondary">
                    <i class="fas fa-calendar me-1"></i><?php echo date('Y-m-d'); ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>