<?php
/**
 * Data Protection/Privacy Policy Page
 * 
 * Contains:
 * - Privacy policy in both Swedish and Finnish
 * - Standard header and footer
 * 
 * @package    KarisAntikvariat
 * @subpackage Frontend
 * @author     Axxell
 * @version    3.0
 */

// Include initialization file
require_once 'init.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Load language strings
$lang_strings = loadLanguageStrings($language);

// Page title
$pageTitle = $lang_strings['privacy_policy_title'] . " - Karis Antikvariat";

// Include header
include 'templates/header.php';
?>

<div class="container my-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4"><?php echo $lang_strings['privacy_policy_title']; ?></h1>
            
            <div class="privacy-content">
                <!-- Responsible party section -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['data_controller']; ?></h2>
                    <div class="contact-info bg-light p-3 rounded">
                        <strong>Karis Antikvariat</strong><br>
                        <?php echo $lang_strings['address']; ?><br>
                        <?php echo $lang_strings['country']; ?><br>
                        <?php echo $lang_strings['email']; ?>: karisantikvariat@gmail.com<br>
                        <?php echo $lang_strings['phone']; ?>: +358 40 871 9706<br>
                        <?php echo $lang_strings['business_id']; ?>: 3302825-3
                    </div>
                </section>

                <!-- Contact person section -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['data_contact_person']; ?></h2>
                    <p><?php echo $lang_strings['data_contact_info']; ?><br>
                    <?php echo $lang_strings['email']; ?>: karisantikvariat@gmail.com</p>
                </section>

                <!-- Purpose section -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['data_processing_purpose']; ?></h2>
                    
                    <h3><?php echo $lang_strings['newsletter']; ?></h3>
                    <ul>
                        <li><strong><?php echo $lang_strings['purpose']; ?>:</strong> <?php echo $lang_strings['newsletter_purpose']; ?></li>
                        <li><strong><?php echo $lang_strings['legal_basis']; ?>:</strong> <?php echo $lang_strings['consent']; ?></li>
                        <li><strong><?php echo $lang_strings['data_processed']; ?>:</strong> <?php echo $lang_strings['email_address']; ?></li>
                        <li><strong><?php echo $lang_strings['retention_period']; ?>:</strong> <?php echo $lang_strings['newsletter_retention']; ?></li>
                    </ul>

                    <h3><?php echo $lang_strings['website_function']; ?></h3>
                    <ul>
                        <li><strong><?php echo $lang_strings['purpose']; ?>:</strong> <?php echo $lang_strings['website_purpose']; ?></li>
                        <li><strong><?php echo $lang_strings['legal_basis']; ?>:</strong> <?php echo $lang_strings['legitimate_interest']; ?></li>
                        <li><strong><?php echo $lang_strings['data_processed']; ?>:</strong>
                            <ul>
                                <li><?php echo $lang_strings['language_choice']; ?></li>
                                <li><?php echo $lang_strings['technical_info']; ?></li>
                            </ul>
                        </li>
                        <li><strong><?php echo $lang_strings['retention_period']; ?>:</strong> <?php echo $lang_strings['website_retention']; ?></li>
                    </ul>
                </section>

                <!-- What we DON'T collect -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['data_not_collected']; ?></h2>
                    <ul>
                        <li><?php echo $lang_strings['no_personal_browsing']; ?></li>
                        <li><?php echo $lang_strings['no_payment_data']; ?></li>
                        <li><?php echo $lang_strings['no_tracking_cookies']; ?></li>
                        <li><?php echo $lang_strings['no_user_profiles']; ?></li>
                    </ul>
                </section>

                <!-- Your rights -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['your_rights']; ?></h2>
                    <p><?php echo $lang_strings['gdpr_rights_intro']; ?></p>
                    <ul>
                        <li><strong><?php echo $lang_strings['right_access']; ?>:</strong> <?php echo $lang_strings['right_access_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_rectification']; ?>:</strong> <?php echo $lang_strings['right_rectification_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_erasure']; ?>:</strong> <?php echo $lang_strings['right_erasure_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_restriction']; ?>:</strong> <?php echo $lang_strings['right_restriction_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_portability']; ?>:</strong> <?php echo $lang_strings['right_portability_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_object']; ?>:</strong> <?php echo $lang_strings['right_object_desc']; ?></li>
                        <li><strong><?php echo $lang_strings['right_withdraw']; ?>:</strong> <?php echo $lang_strings['right_withdraw_desc']; ?></li>
                    </ul>
                    <p><?php echo $lang_strings['exercise_rights']; ?>: karisantikvariat@gmail.com</p>
                </section>

                <!-- Newsletter unsubscribe -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['newsletter_unsubscribe']; ?></h2>
                    <p><?php echo $lang_strings['unsubscribe_methods']; ?></p>
                    <ul>
                        <li><?php echo $lang_strings['unsubscribe_link']; ?></li>
                        <li><?php echo $lang_strings['unsubscribe_contact']; ?>: karisantikvariat@gmail.com</li>
                    </ul>
                </section>

                <!-- Security -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['security']; ?></h2>
                    <p><?php echo $lang_strings['security_measures']; ?></p>
                </section>

                <!-- Data sharing -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['data_sharing']; ?></h2>
                    <p><?php echo $lang_strings['no_data_sharing']; ?></p>
                    <ul>
                        <li><?php echo $lang_strings['technical_providers']; ?></li>
                        <li><?php echo $lang_strings['legal_requirements']; ?></li>
                    </ul>
                </section>

                <!-- Changes to policy -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['policy_changes']; ?></h2>
                    <p><?php echo $lang_strings['policy_changes_info']; ?></p>
                </section>

                <!-- Complaints -->
                <section class="mb-5">
                    <h2><?php echo $lang_strings['complaints']; ?></h2>
                    <p><?php echo $lang_strings['complaints_intro']; ?></p>
                    
                    <div class="contact-info bg-light p-3 rounded">
                        <strong><?php echo $lang_strings['data_protection_authority']; ?></strong><br>
                        <?php echo $lang_strings['authority_visit_address']; ?><br>
                        <?php echo $lang_strings['authority_post_address']; ?><br>
                        <?php echo $lang_strings['email']; ?>: tietosuoja(at)om.fi<br>
                        <?php echo $lang_strings['authority_phone']; ?>: 029 566 6700<br>
                        <?php echo $lang_strings['authority_registry']; ?>: 029 566 6768
                    </div>
                </section>

                <!-- Last updated -->
                <section class="mt-5 pt-3 border-top">
                    <p class="text-muted"><em><?php echo $lang_strings['last_updated']; ?>: <?php echo date('Y-m-d'); ?></em></p>
                </section>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'templates/footer.php';
?>