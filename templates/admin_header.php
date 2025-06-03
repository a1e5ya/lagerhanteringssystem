<?php
/**
 * Admin Header Template (Updated with CSRF Protection and Enhanced Debugging)
 * 
 * Contains:
 * - Admin header with navigation tabs
 * - User info and logout button
 * - CSRF protection and security headers
 * - Enhanced CSRF debugging and global setup
 */

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Get current user info if available
$currentUser = function_exists('getSessionUser') ? getSessionUser() : null;

// If user is not logged in or doesn't have proper permissions, redirect
if (!$currentUser || ($currentUser['user_role'] > 2)) {
    // Redirect to home with error using routing system
    header("Location: " . url('index.php', ['auth_error' => 1]));
    exit;
}

$username = $currentUser['user_username'];
$userRole = $currentUser['user_role'];

// Session check URL - using routing system
$sessionCheckUrl = url('includes/session_check.php');

// Generate CSRF token for this page
$csrfToken = generateCSRFToken();
?><!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Admin - Karis Antikvariat'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo asset('images', 'favicon.png'); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo asset('images', 'favicon.ico'); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css', 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css', 'admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css', 'pagination.css'); ?>">
    
    <!-- CSRF Token and Base URL for JavaScript -->
    <script>
        window.CSRF_TOKEN = '<?php echo $csrfToken; ?>';
        window.BASE_URL = '<?php echo rtrim(BASE_PATH, '/'); ?>';
    </script>
    
    <!-- Enhanced CSRF Setup and Debugging Script -->
    <script>
        /**
         * CSRF Debug and Global Setup Script
         * Ensures proper CSRF token handling and provides debugging information
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Check if CSRF token is available
            if (!window.CSRF_TOKEN) {
                console.error('CRITICAL: CSRF_TOKEN is not available globally!');
                console.log('Available global variables:', Object.keys(window).filter(key => key.includes('CSRF') || key.includes('TOKEN')));
            } else {
                console.log('✓ CSRF Token loaded successfully:', window.CSRF_TOKEN.substring(0, 10) + '...');
            }
            
            // Ensure BASE_URL is available
            if (!window.BASE_URL) {
                console.error('CRITICAL: BASE_URL is not available globally!');
            } else {
                console.log('✓ BASE_URL loaded successfully:', window.BASE_URL);
            }
            
            // Global error handler for AJAX requests to catch CSRF issues
            window.addEventListener('unhandledrejection', function(event) {
                if (event.reason && event.reason.message && event.reason.message.includes('403')) {
                    console.error('CSRF Protection Error: Request was blocked (403 Forbidden)');
                    console.log('This is likely due to missing or invalid CSRF token');
                }
            });
            
            // Override jQuery AJAX to automatically include CSRF token
            if (typeof $ !== 'undefined' && $.ajaxSetup) {
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        // Only add CSRF token to POST requests
                        if (settings.type === 'POST') {
                            // Add CSRF token to headers
                            xhr.setRequestHeader('X-CSRF-Token', window.CSRF_TOKEN);
                            
                            // If data is a string (URL-encoded), add CSRF token to it
                            if (typeof settings.data === 'string' && settings.data.indexOf('csrf_token=') === -1) {
                                settings.data = settings.data + (settings.data ? '&' : '') + 'csrf_token=' + encodeURIComponent(window.CSRF_TOKEN);
                            }
                            
                            // If data is a FormData object, add CSRF token to it
                            if (settings.data instanceof FormData && !settings.data.has('csrf_token')) {
                                settings.data.append('csrf_token', window.CSRF_TOKEN);
                            }
                            
                            // If data is an object, add CSRF token to it
                            if (typeof settings.data === 'object' && settings.data !== null && 
                                !(settings.data instanceof FormData) && !settings.data.csrf_token) {
                                settings.data.csrf_token = window.CSRF_TOKEN;
                            }
                        }
                    }
                });
                
                console.log('✓ jQuery AJAX setup configured with CSRF protection');
            }
            
            // Add CSRF token to all existing forms
            const forms = document.querySelectorAll('form');
            let formsUpdated = 0;
            
            forms.forEach(function(form) {
                if (form.method.toLowerCase() === 'post' && !form.querySelector('input[name="csrf_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = window.CSRF_TOKEN;
                    form.appendChild(csrfInput);
                    formsUpdated++;
                }
            });
            
            if (formsUpdated > 0) {
                console.log(`✓ Added CSRF tokens to ${formsUpdated} forms`);
            }
            
            // Set up a global function to manually add CSRF to any request
            window.addCSRFToRequest = function(data) {
                if (data instanceof FormData) {
                    if (!data.has('csrf_token')) {
                        data.append('csrf_token', window.CSRF_TOKEN);
                    }
                } else if (typeof data === 'object' && data !== null) {
                    if (!data.csrf_token) {
                        data.csrf_token = window.CSRF_TOKEN;
                    }
                }
                return data;
            };
            
            // Global function to check if a request has CSRF protection
            window.hasCSRFProtection = function(data, headers) {
                // Check in data
                if (data instanceof FormData && data.has('csrf_token')) return true;
                if (typeof data === 'object' && data !== null && data.csrf_token) return true;
                if (typeof data === 'string' && data.includes('csrf_token=')) return true;
                
                // Check in headers
                if (headers && headers['X-CSRF-Token']) return true;
                
                return false;
            };
            
            console.log('✓ CSRF debugging and global setup completed');
        });
    </script>
    
    <!-- Session check script with CSRF protection -->
    <script>
        // Function to check session status
        function checkSession() {
            fetch('<?php echo $sessionCheckUrl; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    csrf_token: window.CSRF_TOKEN
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        // If session is not valid, redirect to login page
                        window.location.href = BASE_URL + '/index.php?auth_error=2';
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

        // Add CSRF token to all AJAX requests using fetch API override
        document.addEventListener('DOMContentLoaded', function() {
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
                    
                    // If body is JSON, add CSRF token
                    if (options.headers['Content-Type'] === 'application/json' && typeof options.body === 'string') {
                        try {
                            const jsonData = JSON.parse(options.body);
                            if (!jsonData.csrf_token) {
                                jsonData.csrf_token = window.CSRF_TOKEN;
                                options.body = JSON.stringify(jsonData);
                            }
                        } catch (e) {
                            // If parsing fails, leave as is
                        }
                    }
                }
                
                return originalFetch(url, options);
            };

            // Add CSRF token to all forms
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
        });
    </script>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header/Navigation for Admin Pages -->
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo url('index.php'); ?>"><i class="fas fa-book-open me-2"></i>Karis Antikvariat</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-item-link <?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>" 
                               href="<?php echo url('admin.php'); ?>">Lager</a>
                        </li>
                        <?php if ($userRole == 1): // Admin only ?>
                        <li class="nav-item">
                            <a class="nav-item-link <?php echo ($currentPage == 'usermanagement.php') ? 'active' : ''; ?>" 
                               href="<?php echo url('admin/usermanagement.php'); ?>">Användare</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="d-flex align-items-center">
                        <!-- User info and logout button -->
                        <span class="text-light me-3">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($username); ?>
                        </span>
                        <!-- Logout form with CSRF protection -->
                        <form method="POST" action="<?php echo url('index.php'); ?>" class="d-inline">
                            <?php echo getCSRFTokenField(); ?>
                            <input type="hidden" name="logout" value="1">
                            <button type="submit" class="btn btn-outline-light">
                                <i class="fas fa-sign-out-alt me-1"></i>Logga ut
                            </button>
                        </form>
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
                <span class="badge bg-info ms-2">
                    <i class="fas fa-shield-alt me-1"></i>Säker session
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>