<?php
/**
 * Footer Template
 * 
 * Contains:
 * - Footer with contact info
 * - Newsletter subscription
 * - Copyright information
 */

// Load language strings if not already loaded
if (!isset($strings)) {
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    $strings = loadLanguageStrings($language);
}
?>

<!-- Footer for Public Pages -->

<footer class="footer text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5>Karis Antikvariat</h5>
                <address class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i> <?php echo $strings['address']; ?><br>
                    <i class="fas fa-phone-alt me-2"></i> 040-8719706<br>
                    <i class="fas fa-envelope me-2"></i> karisantikvariat@gmail.com<br>
                    <a href="https://www.facebook.com/antikvariatkaris" class="text-white me-3" target="_blank">
                        <i class="fab fa-facebook-f fa-lg me-2"></i>@antikvariatkaris
                    </a><br>
                    <i class="fas fa-building me-2"></i> FO: 3302825-3
                </address>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h5><?php echo $strings['opening_hours']; ?></h5>
                <ul class="list-unstyled">
                    <li><?php echo $strings['tuesday_friday']; ?>: 10:00 - 17:00</li>
                    <li><?php echo $strings['saturday']; ?>: 10:00 - 15:00</li>
                    <li><?php echo $strings['sunday_monday']; ?>: <?php echo $strings['closed']; ?></li>
                </ul>
                <h5 class="mt-3"><?php echo $strings['delivery']; ?></h5>
                <p><?php echo $strings['delivery_info']; ?></p>
                
            </div>
            <div class="col-md-4">
                <h5><?php echo $strings['newsletter']; ?></h5>
                <p><?php echo $strings['newsletter_desc']; ?></p>
                <form id="newsletter-form" method="post" action="newsletter.php">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="<?php echo $strings['your_email']; ?>" aria-label="<?php echo $strings['your_email']; ?>" id="newsletter-email" required>
                    </div>
                    <button class="btn btn-light" type="submit"><?php echo $strings['subscribe']; ?></button>
                </form>
            </div>
        </div>
        <hr class="my-3 bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. <?php echo $strings['all_rights_reserved']; ?></p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<script>
    // Forgot password link functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Handle forgot password link
        const forgotPasswordLink = document.getElementById('forgot-password');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Hide login modal
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                if (loginModal) {
                    loginModal.hide();
                }
                // Show forgot password modal
                const forgotPasswordModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                forgotPasswordModal.show();
            });
        }
        
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function(event) {
                if (!event.target.closest('a')) {
                    window.location.href = this.dataset.href;
                }
            });
        });
    });
</script>

</body>
</html>