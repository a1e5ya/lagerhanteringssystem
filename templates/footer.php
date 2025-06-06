<?php
/**
 * Footer Template (Updated with Newsletter Modal)
 * 
 * Contains:
 * - Footer with contact info
 * - Newsletter subscription modal trigger
 * - Copyright information
 * - Centralized JavaScript loading
 */

// Load language strings if not already loaded
if (!isset($strings)) {
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    // Validate language parameter
    $language = in_array($language, ['sv', 'fi']) ? $language : 'sv';
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
                    <i class="fas fa-map-marker-alt me-2"></i><?php echo $strings['address']; ?><br>
                    <i class="fas fa-phone-alt me-2"></i>+358 40 871 9706<br>
                    <i class="fas fa-envelope me-2"></i>karisantikvariat@gmail.com<br>
                    <a href="https://www.facebook.com/antikvariatkaris" class="text-white me-3" target="_blank">
                        <i class="fab fa-facebook-f fa-lg me-2"></i>@antikvariatkaris
                    </a><br>
                    <i class="fas fa-building me-2"></i>FO: 3302825-3
                </address>

              <!-- Privacy Policy Link -->
                <div class="mt-3">
                    <a href="<?php echo url('policy.php'); ?>" class="text-white text-decoration-none">
                        <i class="fas fa-shield-alt me-2"></i><?php echo $strings['privacy_policy'] ?? 'Registerbeskrivning'; ?>
                    </a>
                </div>

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
                
                <!-- Newsletter Button to Open Modal -->
                <button class="btn btn-light" type="button" id="newsletter-modal-trigger">
                    <i class="fas fa-envelope me-2"></i>
                    <?php echo $strings['subscribe']; ?>
                </button>
            </div>
        </div>
        <hr class="my-3 bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. <?php echo $strings['all_rights_reserved']; ?></p>
        </div>
    </div>
</footer>

<!-- Include Newsletter Modal -->
<?php include 'newsletter_modal.php'; ?>

<!-- Load JavaScript using the centralized loader -->
<?php 
// Include the JS loader if not already included
require_once __DIR__ . '/../includes/js_loader.php';

// For public pages
echo loadPublicJavaScript();

// You could also load specific files if needed:
// echo loadCustomJavaScript(['specific-file-name']);
?>

<script>
    // Newsletter modal trigger and language detection
    document.addEventListener('DOMContentLoaded', function() {
        // Handle newsletter modal trigger
        const newsletterTrigger = document.getElementById('newsletter-modal-trigger');
        if (newsletterTrigger) {
            newsletterTrigger.addEventListener('click', function() {
                // Better language detection
                let currentLanguage = 'sv'; // default
                
                // Check URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('lang') === 'fi') {
                    currentLanguage = 'fi';
                } else if (urlParams.get('lang') === 'sv') {
                    currentLanguage = 'sv';
                } else {
                    // Check HTML lang attribute
                    const htmlLang = document.documentElement.lang;
                    if (htmlLang && htmlLang.startsWith('fi')) {
                        currentLanguage = 'fi';
                    } else if (htmlLang && htmlLang.startsWith('sv')) {
                        currentLanguage = 'sv';
                    } else {
                        // Check body class or other indicators
                        if (document.body.classList.contains('lang-fi')) {
                            currentLanguage = 'fi';
                        }
                        // If no specific indicators, check the page content language
                        // This is a fallback method
                        const pageText = document.body.innerText.toLowerCase();
                        if (pageText.includes('tervetuloa') || pageText.includes('tilaa') || pageText.includes('uutiskirje')) {
                            currentLanguage = 'fi';
                        }
                    }
                }
                
                
                const languageInput = document.getElementById('newsletter-language');
                if (languageInput) {
                    languageInput.value = currentLanguage;
                }
                
                // Show the newsletter modal
                const newsletterModal = new bootstrap.Modal(document.getElementById('newsletterModal'));
                newsletterModal.show();
            });
        }

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
    });
</script>

</body>
</html>