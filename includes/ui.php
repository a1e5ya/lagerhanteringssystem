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
    $langFile = 'languages/' . $language . '.php';
    
    if (file_exists($langFile)) {
        include $langFile;
        return $lang_strings;
    } else {
        // Fallback to Swedish if language file doesn't exist
        include 'languages/sv.php';
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
        
        // Get query parameters without the lang parameter
        $query = '';
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            unset($queryParams['lang']);
            if (!empty($queryParams)) {
                $query = '?' . http_build_query($queryParams);
            }
        }
        
        // Rebuild the URL with the path and remaining query parameters
        $redirectUrl = $path . $query;
    } else {
        // Fallback to handling based on REQUEST_URI if no referrer
        $uri = $_SERVER['REQUEST_URI'];
        $redirectUrl = strtok($uri, '?');
        
        // Get query parameters without the lang parameter
        $queryParams = $_GET;
        unset($queryParams['lang']);
        if (!empty($queryParams)) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }
    }
    
    // Redirect to the appropriate page
    header('Location: ' . $redirectUrl);
    exit;
}

?>