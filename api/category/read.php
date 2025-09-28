<?php

declare(strict_types=1);

// Modern approach with autoloading (if available)
if (file_exists("../../vendor/autoload.php")) {
    require_once "../../vendor/autoload.php";
    
    use Swifty\Controllers\CategoryController;
    
    try {
        $controller = new CategoryController();
        $controller->getAllCategories();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
        error_log("API Error: " . $e->getMessage());
    }
    exit;
}

// Legacy approach for backward compatibility
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

include_once("../../config/Database.php");
include_once("../../models/Category.php");

try {
    // Instantiate DB and Connect to It
    $database = new Database();
    $db = $database->connect();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    // Instantiate category object
    $cat = new Category($db);

    // Category Query
    $cats = $cat->read();

    // Get Rows Count
    $rows = $cats->rowCount();

    // Check For Categories in The Database
    if ($rows > 0) {
        // Categories Available
        $catsArr = [];

        while ($row = $cats->fetch(PDO::FETCH_ASSOC)) {
            $catItem = [
                "id" => (int) $row['id'],
                "name" => $row['name'],
                "created_at" => $row['created_at'] ?? null,
            ];

            $catsArr[] = $catItem;
        }

        // Turn categories array into JSON and display it
        echo json_encode($catsArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // No category in the DB
        http_response_code(404);
        echo json_encode(["error" => "No categories found"], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
    error_log("API Error: " . $e->getMessage());
}
