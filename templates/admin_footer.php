<?php
/**
 * Admin Footer Template (Updated with JS Loader)
 * 
 * Contains:
 * - Simple admin footer
 * - Centralized JavaScript loading for admin functionality
 */
?>

<!-- Footer for Admin Pages -->
<footer class="footer text-white py-4 mt-auto">
    <div class="container">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. Alla rättigheter förbehållna.</p>
        </div>
    </div>
</footer>

<!-- Load JavaScript using the centralized loader -->
<?php 
// Include the JS loader if not already included
require_once __DIR__ . '/../includes/js_loader.php';

// For admin pages, load all admin JS files
echo loadAdminJavaScript();
?>

</body>
</html>