<?php
/**
 * Footer Template
 * 
 * Contains:
 * - Footer with contact info
 * - Newsletter subscription
 * - Copyright information
 */
?>

<!-- Footer for Public Pages -->
<footer class="footer text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <h5>Karis Antikvariat</h5>
                <address class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i> Köpmansgatan 12, 10300 Karis<br>
                    <i class="fas fa-phone-alt me-2"></i> 040-8719706<br>
                    <i class="fas fa-envelope me-2"></i> karisantikvariat@gmail.com<br>
                    <a href="https://www.facebook.com/antikvariatkaris" class="text-white me-3" target="_blank">
                        <i class="fab fa-facebook-f fa-lg me-2"></i>@antikvariatkaris
                    </a><br>
                    <i class="fas fa-building me-2"></i> FO: 3302825-3
                </address>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h5>Öppettider</h5>
                <ul class="list-unstyled">
                    <li>Tisdag - Fredag: 10:00 - 17:00</li>
                    <li>Lördag: 10:00 - 15:00</li>
                    <li>Söndag - Måndag: Stängt</li>
                </ul>
                <h5 class="mt-3">Leverans</h5>
                <p>Vi levererar via Posti enligt deras prislistor. Vi levererar även utomlands.</p>
                
            </div>
            <div class="col-md-4">
                <h5>Prenumerera på vårt nyhetsbrev</h5>
                <p>Få information om nya böcker och specialerbjudanden.</p>
                <form id="newsletter-form" method="post" action="newsletter.php">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Din e-postadress" aria-label="Din e-postadress" id="newsletter-email" required>
                    </div>
                    <button class="btn btn-light" type="submit">Prenumerera</button>
                </form>
            </div>
        </div>
        <hr class="my-3 bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. Alla rättigheter förbehållna.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<script>
    // Make rows clickable
    document.addEventListener('DOMContentLoaded', function() {
        const clickableRows = document.querySelectorAll('.clickable-row');
        clickableRows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = this.dataset.href;
            });
        });
    });
</script>

</body>
</html>