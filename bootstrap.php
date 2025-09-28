<?php

declare(strict_types=1);

/**
 * Bootstrap file for the Swifty API
 * Centralized autoloading, error handling, and modern controller routing
 * Eliminates repetitive autoloader checks across all API endpoints
 */

// Define bootstrap base directory
define('BOOTSTRAP_DIR', __DIR__);

/**
 * Check if modern architecture is available and route to appropriate controller
 * @param string $controllerClass The controller class name
 * @param string $method The method to call on the controller
 * @param array $params Optional parameters to pass to the method
 * @return bool True if modern routing was used, false if should fallback to legacy
 */
function tryModernRoute(string $controllerClass, string $method, array $params = []): bool {
    // Check if autoloader is available
    if (!file_exists(BOOTSTRAP_DIR . '/vendor/autoload.php')) {
        return false;
    }
    
    // Load autoloader
    require_once BOOTSTRAP_DIR . '/vendor/autoload.php';
    
    try {
        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            return false;
        }
        
        // Instantiate controller and call method
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            return false;
        }
        
        // Call the method with parameters
        call_user_func_array([$controller, $method], $params);
        
        return true;
    } catch (Exception $e) {
        // Log error and fall back to legacy
        error_log("Modern routing error: " . $e->getMessage());
        return false;
    }
}

// Load Composer autoloader if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Set error reporting for development
if ($_ENV['APP_ENV'] ?? 'development' === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set default timezone
date_default_timezone_set('UTC');

// Set up error handler for JSON APIs
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    error_log("PHP Error: $message in $file on line $line");
    
    // For API endpoints, return JSON error
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
        exit;
    }
    
    return false;
});

// Set up exception handler
set_exception_handler(function (Throwable $exception) {
    error_log("Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    
    // For API endpoints, return JSON error
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
        exit;
    }
    
    // For regular pages, show generic error
    echo "An error occurred. Please try again later.";
    exit;
});