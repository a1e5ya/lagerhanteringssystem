<?php
/**
 * JavaScript Loader - UPDATED with centralized message system
 * 
 * This file centralizes all JavaScript includes and dependencies.
 * Include this file once in your templates to load all necessary JS files.
 */

// Define JS files groups
$js_files = [
    // CDN resources with full URLs
    'cdn' => [
        'jquery' => 'https://code.jquery.com/jquery-3.6.0.min.js',
        'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
        'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js'
    ],
    
    // Core files needed on all pages (LOAD FIRST)
    'core' => [
        'message-system', 
        'main',
    ],
    
    // Admin-specific files (showMessage() calls removed from these)
    'admin' => [
        'ui-components',
        'forms',
        'ajax',
        'data-operations',
        'admin',
        'lists',
        'pagination'
    ],
    
    // Public-specific files
    'public' => [
        'newsletter',
        'formatter'
    ]
];

/**
 * Load JavaScript files
 * 
 * @param array $types Array of file types to load ('cdn', 'core', 'admin', 'public')
 * @param array $specific Array of specific files to load, overriding the defaults
 * @return string HTML script tags
 */
function loadJavaScript($types = ['cdn', 'core'], $specific = []) {
    global $js_files;
    $output = '';
    
    // Add BASE_URL for JavaScript
    $output .= "<script>const BASE_URL = '" . getBasePath() . "';</script>\n";
    
    // Load requested types IN ORDER (core first, then others)
    $orderedTypes = [];
    if (in_array('cdn', $types)) $orderedTypes[] = 'cdn';
    if (in_array('core', $types)) $orderedTypes[] = 'core';
    if (in_array('admin', $types)) $orderedTypes[] = 'admin';
    if (in_array('public', $types)) $orderedTypes[] = 'public';
    
    foreach ($orderedTypes as $type) {
        if (isset($js_files[$type])) {
            foreach ($js_files[$type] as $key => $file) {
                if ($type === 'cdn') {
                    $output .= "<script src=\"{$file}\"></script>\n";
                } else {
                    $output .= "<script src=\"" . asset('js', $file . '.js') . "\"></script>\n";
                }
            }
        }
    }
    
    // Load specific files if any
    if (!empty($specific)) {
        foreach ($specific as $file) {
            $output .= "<script src=\"" . asset('js', $file . '.js') . "\"></script>\n";
        }
    }
    
    return $output;
}

/**
 * Load all admin JavaScript files
 * 
 * @return string HTML script tags
 */
function loadAdminJavaScript() {
    return loadJavaScript(['cdn', 'core', 'admin']);
}

/**
 * Load all public JavaScript files
 * 
 * @return string HTML script tags
 */
function loadPublicJavaScript() {
    return loadJavaScript(['cdn', 'core', 'public']);
}

/**
 * Load minimal JavaScript files (jQuery + Bootstrap + message system)
 * 
 * @return string HTML script tags
 */
function loadMinimalJavaScript() {
    return loadJavaScript(['cdn', 'core']);
}

/**
 * Load custom JavaScript files
 * 
 * @param array $files Array of file names without extension
 * @return string HTML script tags
 */
function loadCustomJavaScript($files) {
    return loadJavaScript(['cdn', 'core'], $files);
}