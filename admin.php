<?php
/**
 * Admin Dashboard
 * 
 * Contains:
 * - Main admin dashboard
 * - Authentication check
 */

 
// Include initialization file
require_once 'init.php';

// Check if user is authenticated and has admin or editor permissions
// Only Admin (1) or Editor (2) roles can access this page
checkAuth(2); // 2 or lower (Admin or Editor) role required

// Get current user info
$currentUser = getSessionUser();

// Page title
$pageTitle = "Lagerhanteringssystem - Karis Antikvariat";

// Include admin header
include_once 'templates/admin_header.php';
?>

    <?php
    // Check and display success message
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']); // Clear the message after displaying it
    }
    ?>

    <!-- Main Content Container -->
    <div class="container my-4">
    <div id="message-container" style="display:none;"></div>
        <!-- Inventory System -->
        <div id="inventory-system">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="inventory-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-tab="search">Sök</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-tab="addproduct">Lägg till produkt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-tab="addauthor">Lägg till författare</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-tab="lists">Listor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-tab="productlog">Produktlogg</a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] <= 1): ?>
                <li class="nav-item">
                    <a class="nav-link" data-tab="tabledatamanagement">Redigera databas</a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] <= 1): ?>
                <li class="nav-item">
                    <a class="nav-link" data-tab="newsletter">Nyhetsbrev</a>
                </li>
                <?php endif; ?>
            </ul>
            <div id="tabs-content" class="tab-content border border-top-0 p-4 bg-white">
                <!-- Initial content will be loaded here -->
            </div>
        </div>
    </div>

    <?php
    // Include admin footer (which will include JS files)
    include_once 'templates/admin_footer.php';
    ?>