<?php

declare(strict_types=1);

// Modern approach with autoloading (if available)
if (file_exists("../../vendor/autoload.php")) {
    require_once "../../vendor/autoload.php";
    
    use Swifty\Controllers\PostController;
    
    try {
        $controller = new PostController();
        $controller->createPost();
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
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-type, Access-Control-Allow-Origin, Authorization, X-Requested-With");

include_once("../../config/Database.php");
include_once("../../models/Post.php");

try {
    // Instantiate DB and Connect to It
    $database = new Database();
    $db = $database->connect();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    // Instantiate blog post object
    $post = new Post($db);

    // Get raw POSTed data
    $input = file_get_contents("php://input");
    
    if (empty($input)) {
        http_response_code(400);
        echo json_encode(["error" => "JSON data is required"], JSON_PRETTY_PRINT);
        exit;
    }

    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"], JSON_PRETTY_PRINT);
        exit;
    }

    // Validate required fields
    $requiredFields = ['title', 'category_id', 'body', 'author'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        http_response_code(422);
        echo json_encode([
            "error" => "Missing required fields",
            "missing_fields" => $missingFields
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $post->title = $data['title'];
    $post->categoryId = (int) $data['category_id'];
    $post->body = $data['body'];
    $post->author = $data['author'];

    if ($post->create()) {
        http_response_code(201);
        echo json_encode(["message" => "Post created successfully"], JSON_PRETTY_PRINT);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Cannot create post"], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
    error_log("API Error: " . $e->getMessage());
}
