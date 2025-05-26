<?php
/**
 * UI Functions
 * 
 * Contains:
 * - changeLanguage() - Sets UI language
 */
?>

<?php

/**
 * Loads the appropriate language strings
 * 
 * @param string $language The language code ('sv' or 'fi')
 * @return array The language strings
 */
function loadLanguageStrings($language) {
    $langFile = __DIR__ . '/../languages/' . $language . '.php'; // <--- CHANGE THIS LINE
    
    if (file_exists($langFile)) {
        include $langFile;
        return $lang_strings;
    } else {
        // Fallback to Swedish if language file doesn't exist
        include __DIR__ . '/../languages/sv.php'; // <--- CHANGE THIS LINE
        return $lang_strings;
    }
}

/**
 * Changes the user interface language
 * 
 * @param string $language The language code ('sv' for Swedish, 'fi' for Finnish)
 * @return void
 */
function changeLanguage($language) {
    // Validate language parameter (only accept 'sv' or 'fi')
    if ($language !== 'sv' && $language !== 'fi') {
        $language = 'sv'; // Default to Swedish if invalid
    }
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Update session with new language
    $_SESSION['language'] = $language;
    
    // Get the referrer URL (the page that sent the request)
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // If we have a referrer, parse its components to get the path
    if (!empty($referrer)) {
        $parsedUrl = parse_url($referrer);
        $path = $parsedUrl['path'] ?? '';
        
        // Extract the filename from the path
        $filename = basename($path);
        
        // Get query parameters without the lang parameter
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            unset($queryParams['lang']);
        }
        
        // Use the routing function to generate the URL
        $redirectUrl = url($filename, $queryParams);
    } else {
        // Fallback to index if no referrer
        $redirectUrl = url('index.php');
    }
    
    // Redirect to the appropriate page
    header('Location: ' . $redirectUrl);
    exit;
}

// Handle language switching request if present
if (isset($_GET['lang'])) {
    changeLanguage($_GET['lang']);
}
?>