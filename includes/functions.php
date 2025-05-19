<?php
/**
 * Utility Functions
 * 
 * Contains:
 * - sanitizeInput() - Sanitizes user input
 * - safeEcho() - Safely applies htmlspecialchars to a potentially null value
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