<?php
/**
 * Utility Functions
 * 
 * Contains:
 * - sanitizeInput() - Sanitizes user input
 * - formatCurrency() - Formats price values
 * - formatDate() - Formats dates consistently
 * - displayError() - Shows error messages
 * - displaySuccess() - Shows success messages
 * - logEvent() - Records actions in event log
 * - backupDatabase() - Creates database backup
 * - subscribeToNewsletter() - Adds email to newsletter list
 */
?>

<?php

/**
 * Safely applies htmlspecialchars to a potentially null value
 * 
 * @param mixed $value The value to sanitize
 * @return string The sanitized string or empty string if value is null
 */
function safeEcho($value) {
    return ($value !== null) ? htmlspecialchars($value) : '';
}

?>