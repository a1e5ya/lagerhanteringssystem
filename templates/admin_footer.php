<?php
/**
 * Admin Footer Template
 * 
 * Contains:
 * - Simple admin footer
 * - JavaScript resources for admin functionality
 */
?>

<!-- Footer for Admin Pages -->
<footer class="footer text-white py-4 mt-5">
    <div class="container">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. Alla rättigheter förbehållna.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (needed for some admin functionality) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom Admin JS -->
<script src="<?php echo BASE_URL; ?>assets/js/admin.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/validation.js"></script>

</body>
</html>