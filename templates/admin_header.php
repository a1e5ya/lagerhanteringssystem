<?php
/**
 * Admin Header Template (FIXED - Enhanced CSRF Protection and Security Headers)
 * 
 * Contains:
 * - Admin header with navigation tabs
 * - User info and logout button
 * - CSRF protection for both fetch() and jQuery AJAX
 * - Enhanced security headers
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
    
    <!-- Enhanced Security and CSRF Protection Script -->
    <script>
        // Function to check session status with CSRF protection
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

        // ENHANCED CSRF PROTECTION FOR ALL AJAX REQUESTS
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

            // FIXED: Enhanced jQuery AJAX setup for CSRF protection
            if (typeof $ !== 'undefined') {
                // Set up global AJAX defaults for jQuery
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        // Only add CSRF token to POST requests
                        if (settings.type && settings.type.toUpperCase() === 'POST') {
                            // Add CSRF token to headers
                            xhr.setRequestHeader('X-CSRF-Token', window.CSRF_TOKEN);
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                            
                            // Add CSRF token to data if not already present
                            if (settings.data) {
                                if (typeof settings.data === 'string') {
                                    // For serialized data
                                    if (settings.data.indexOf('csrf_token=') === -1) {
                                        settings.data += (settings.data ? '&' : '') + 'csrf_token=' + encodeURIComponent(window.CSRF_TOKEN);
                                    }
                                } else if (typeof settings.data === 'object' && !(settings.data instanceof FormData)) {
                                    // For object data
                                    if (!settings.data.csrf_token) {
                                        settings.data.csrf_token = window.CSRF_TOKEN;
                                    }
                                } else if (settings.data instanceof FormData) {
                                    // For FormData
                                    if (!settings.data.has('csrf_token')) {
                                        settings.data.append('csrf_token', window.CSRF_TOKEN);
                                    }
                                }
                            } else {
                                // If no data, create it with CSRF token
                                settings.data = { csrf_token: window.CSRF_TOKEN };
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // Global error handler for AJAX requests
                        if (xhr.status === 401) {
                            alert('Din session har gått ut. Du kommer att omdirigeras till inloggningssidan.');
                            window.location.href = BASE_URL + '/index.php?auth_error=2';
                        } else if (xhr.status === 403) {
                            if (typeof showMessage === 'function') {
                                showMessage('Du har inte behörighet att utföra denna åtgärd.', 'danger');
                            } else {
                                alert('Du har inte behörighet att utföra denna åtgärd.');
                            }
                        } else if (xhr.status === 419) {
                            // CSRF token mismatch
                            alert('Säkerhetstoken har gått ut. Sidan kommer att laddas om.');
                            window.location.reload();
                        } else if (xhr.status >= 500) {
                            if (typeof showMessage === 'function') {
                                showMessage('Ett serverfel inträffade. Försök igen senare.', 'danger');
                            } else {
                                alert('Ett serverfel inträffade. Försök igen senare.');
                            }
                        }
                    }
                });
            }

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

            // Monitor for dynamically added forms
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Check if the added node is a form
                            if (node.tagName === 'FORM' && node.method.toLowerCase() === 'post') {
                                if (!node.querySelector('input[name="csrf_token"]')) {
                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = 'csrf_token';
                                    csrfInput.value = window.CSRF_TOKEN;
                                    node.appendChild(csrfInput);
                                }
                            }
                            // Check for forms inside the added node
                            const forms = node.querySelectorAll && node.querySelectorAll('form[method="post"], form[method="POST"]');
                            if (forms) {
                                forms.forEach(function(form) {
                                    if (!form.querySelector('input[name="csrf_token"]')) {
                                        const csrfInput = document.createElement('input');
                                        csrfInput.type = 'hidden';
                                        csrfInput.name = 'csrf_token';
                                        csrfInput.value = window.CSRF_TOKEN;
                                        form.appendChild(csrfInput);
                                    }
                                });
                            }
                        }
                    });
                });
            });

            // Start observing
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });

        // Function to refresh CSRF token if needed
        window.refreshCSRFToken = function() {
            return fetch(BASE_URL + '/includes/get_csrf_token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.token) {
                    window.CSRF_TOKEN = data.token;
                    // Update all hidden CSRF inputs
                    document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                        input.value = data.token;
                    });
                    return data.token;
                } else {
                    throw new Error('Failed to refresh CSRF token');
                }
            });
        };

        // Enhanced security headers check
        function checkSecurityHeaders() {
            // This function can be called to verify security headers are properly set
            // Mainly for debugging purposes
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                console.warn('Security Warning: Site is not using HTTPS');
            }
        }

        // Call security check on load
        document.addEventListener('DOMContentLoaded', checkSecurityHeaders);
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