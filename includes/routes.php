<?php
/**
 * Centralized Routing Configuration
 * 
 * This file provides a single location to manage all paths and routes
 * throughout the application. This approach makes it easier to migrate
 * to a new server or change the application's base path.
 */

class Routes {
    // Base application path - change this when moving to a new server
    private static $basePath = '';
    
    /**
     * Initialize the routing configuration
     * 
     * @param string $basePath The base path of the application
     */
    public static function init($basePath) {
        self::$basePath = $basePath;
    }
    
    /**
     * Get the base path
     * 
     * @return string The base path
     */
    public static function getBasePath() {
        return self::$basePath;
    }
    
    /**
     * Get the URL for a path
     * 
     * @param string $path The path
     * @param array $params Optional query parameters
     * @return string The URL
     */
    public static function url($path = '', $params = []) {
        $url = self::$basePath . '/' . ltrim($path, '/');
        
        // Add query parameters if any
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
    /**
     * Get an asset URL
     * 
     * @param string $type Asset type (css, js, images)
     * @param string $file Asset filename
     * @return string Asset URL
     */
    public static function asset($type, $file) {
        return self::$basePath . '/assets/' . $type . '/' . $file;
    }
}

/**
 * Helper functions for easier use in templates
 */

/**
 * Get the base path
 * 
 * @return string The base path
 */
function getBasePath() {
    return Routes::getBasePath();
}

/**
 * Get the URL for a path
 * 
 * @param string $path The path
 * @param array $params Optional query parameters
 * @return string The URL
 */
function url($path = '', $params = []) {
    return Routes::url($path, $params);
}

/**
 * Get an asset URL
 * 
 * @param string $type Asset type (css, js, images)
 * @param string $file Asset filename
 * @return string Asset URL
 */
function asset($type, $file) {
    return Routes::asset($type, $file);
}