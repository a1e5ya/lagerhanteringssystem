<?php
/**
 * Application Initialization File
 *
 * This file serves as a central point for loading all required dependencies
 * for the application. Include this file at the beginning of each main PHP file
 * to ensure all necessary components are available.
 */

// Set the absolute base path for includes
$include_base = __DIR__;

// 1. Include configuration first (might define constants needed by security.php)
require_once $include_base . '/config/config.php';

// 2. Include security settings (this should contain session ini_set() calls)
//    This MUST come BEFORE session_start().
require_once $include_base . '/includes/security.php'; 



// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include core files
require_once $include_base . '/includes/functions.php';
require_once $include_base . '/includes/db_functions.php';
require_once $include_base . '/includes/auth.php';
require_once $include_base . '/includes/ui.php';
require_once $include_base . '/includes/Formatter.php';
require_once $include_base . '/includes/ErrorHandler.php';
require_once $include_base . '/includes/Database.php';
require_once $include_base . '/includes/ImageProcessor.php';
require_once $include_base . '/includes/Paginator.php';


// Determine current language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'sv';

// Load language strings - if ui.php defines loadLanguageStrings()
if (function_exists('loadLanguageStrings')) {
    $strings = loadLanguageStrings($language);
}

// Create formatter instance - if Formatter class exists
if (class_exists('Formatter')) {
    $formatter = new Formatter($language === 'fi' ? 'fi_FI' : 'sv_SE');
}

/**
 * Set page title - call this function before including header.php
 *
 * @param string $title Page title
 * @return void
 */
function setPageTitle($title) {
    global $pageTitle;
    $pageTitle = $title;
}