<?php
/**
 * Admin Dashboard
 * 
 * Contains:
 * - Main admin dashboard
 * - Authentication check
 */
session_start();

define('BASE_PATH', __DIR__);
// Include necessary files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_functions.php';
require_once 'includes/auth.php';
require_once 'includes/ui.php';

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
                    <a class="nav-link" data-tab="tabledatamanagement">Redigera databas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-tab="lists">Listor</a>
                </li>
            </ul>
            <div id="tabs-content" class="tab-content border border-top-0 p-4 bg-white">
                <!-- Initial content will be loaded here -->
            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/ui-components.js"></script>
<script src="assets/js/forms.js"></script>
<script src="assets/js/ajax.js"></script>
<script src="assets/js/data-operations.js"></script>
<script src="assets/js/admin.js"></script>
    
    <?php
    // Include admin footer
    include_once 'templates/admin_footer.php';
    ?>