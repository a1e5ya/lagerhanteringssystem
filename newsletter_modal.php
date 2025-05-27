<?php
/**
 * Newsletter Subscription Modal
 * 
 * This file contains the modal dialogs for newsletter subscription
 * Including main subscription modal and success modal
 */

// Load language strings if not already loaded
if (!isset($strings)) {
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';
    $strings = loadLanguageStrings($language);
}
?>

<!-- Newsletter Modal -->
<div class="modal fade" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsletterModalLabel">
                    <i class="fas fa-envelope me-2"></i>
                    <span id="newsletter-modal-title"><?php echo $strings['newsletter'] ?? 'Nyhetsbrev'; ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="newsletter-modal-desc"><?php echo $strings['newsletter_desc'] ?? 'Prenumerera på vårt nyhetsbrev för att få information om nya objekt och erbjudanden.'; ?></p>
                
                <!-- Newsletter Form -->
                <form id="newsletter-form-modal" method="post">
                    <div class="mb-3">
                        <label for="newsletter-name" class="form-label">
                            <span id="newsletter-name-label"><?php echo $strings['name'] ?? 'Namn'; ?></span>
                            <small class="text-muted">(<span id="newsletter-optional"><?php echo $strings['optional'] ?? 'Valfritt'; ?></span>)</small>
                        </label>
                        <input type="text" class="form-control" name="name" id="newsletter-name" 
                               placeholder="<?php echo $strings['your_name'] ?? 'Ditt namn'; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="newsletter-email-modal" class="form-label">
                            <span id="newsletter-email-label"><?php echo $strings['email'] ?? 'E-postadress'; ?></span>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" name="email" id="newsletter-email-modal" 
                               placeholder="<?php echo $strings['your_email'] ?? 'Din e-postadress'; ?>" required>
                    </div>
                    
                    <!-- reCAPTCHA (commented out by default - uncomment when you have keys) -->
                    <!--
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                        <div class="invalid-feedback" id="recaptcha-error" style="display: none;">
                            <span id="recaptcha-error-text"><?php echo $strings['recaptcha_required'] ?? 'Vänligen verifiera att du inte är en robot'; ?></span>
                        </div>
                    </div>
                    -->
                    
                    <!-- Hidden language field -->
                    <input type="hidden" name="language" id="newsletter-language" value="">
                    
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit" id="newsletter-submit-btn">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;" id="newsletter-spinner"></span>
                            <span id="newsletter-submit-text"><?php echo $strings['subscribe'] ?? 'Prenumerera'; ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="newsletterSuccessModal" tabindex="-1" aria-labelledby="newsletterSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-success" id="newsletterSuccessModalLabel">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="success-modal-title"><?php echo $strings['success'] ?? 'Tack!'; ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-envelope-open-text text-success" style="font-size: 3rem;"></i>
                </div>
                <p id="success-message" class="mb-0">
                    <?php echo $strings['newsletter_success'] ?? 'Tack för din prenumeration på vårt nyhetsbrev!'; ?>
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <span id="success-close-text"><?php echo $strings['close'] ?? 'Stäng'; ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Load reCAPTCHA script (uncomment when you have keys) -->
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->