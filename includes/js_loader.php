<?php
/**
 * JavaScript Loader - UPDATED with centralized message system
 * 
 * This file centralizes all JavaScript includes and dependencies.
 * Include this file once in your templates to load all necessary JS files.
 */


    // CDN resources with full URLs
$js_files = [
    'cdn' => [
        'jquery' => [
            'src' => 'https://code.jquery.com/jquery-3.6.0.min.js',
            'integrity' => 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=',
            'crossorigin' => 'anonymous'
        ],
'bootstrap' => [
    'src' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js',
    'integrity' => 'sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO',
    'crossorigin' => 'anonymous'
],
        'fontawesome' => [
            'src' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js',
            'integrity' => 'sha512-yFjZbTYRCJodnuyGlsKamNE/LlEaEAxSUDe5+u61mV8zzqJVFOH7TnULE2/PP/l5vKWpUNnF4VGVkXh3MjgLsg==',
            'crossorigin' => 'anonymous'
        ]
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
                // Handle CDN files with integrity and crossorigin attributes
                $src = is_array($file) ? $file['src'] : $file;
                $integrity = is_array($file) && isset($file['integrity']) ? ' integrity="' . $file['integrity'] . '"' : '';
                $crossorigin = is_array($file) && isset($file['crossorigin']) ? ' crossorigin="' . $file['crossorigin'] . '"' : '';
                
                $output .= "<script src=\"{$src}\"{$integrity}{$crossorigin}></script>\n";
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