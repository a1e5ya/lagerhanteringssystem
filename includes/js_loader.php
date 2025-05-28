<?php
/**
 * JavaScript Loader
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
    
    // Common files needed on all pages
    'common' => [
        'main',
    ],
    
    // Admin-specific files
    'admin' => [
        'ui-components',
        'forms',
        'ajax',
        'data-operations',
        'admin',
        'lists',
        'batch-operations',
        'pagination',
        'validation'
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
 * @param array $types Array of file types to load ('cdn', 'common', 'admin', 'public')
 * @param array $specific Array of specific files to load, overriding the defaults
 * @return string HTML script tags
 */
function loadJavaScript($types = ['cdn', 'common'], $specific = []) {
    global $js_files;
    $output = '';
    
    // Add BASE_URL for JavaScript
    $output .= "<script>const BASE_URL = '" . getBasePath() . "';</script>\n";
    
    // Load requested types
    foreach ($types as $type) {
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
    return loadJavaScript(['cdn', 'common', 'admin']);
}

/**
 * Load all public JavaScript files
 * 
 * @return string HTML script tags
 */
function loadPublicJavaScript() {
    return loadJavaScript(['cdn', 'common', 'public']);
}

/**
 * Load minimal JavaScript files (jQuery + Bootstrap + main)
 * 
 * @return string HTML script tags
 */
function loadMinimalJavaScript() {
    return loadJavaScript(['cdn', 'common']);
}

/**
 * Load custom JavaScript files
 * 
 * @param array $files Array of file names without extension
 * @return string HTML script tags
 */
function loadCustomJavaScript($files) {
    return loadJavaScript(['cdn', 'common'], $files);
}