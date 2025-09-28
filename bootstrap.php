<?php

declare(strict_types=1);

/**
 * Bootstrap file for the Swifty API
 * Sets up autoloading and error handling
 */

// Load Composer autoloader
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