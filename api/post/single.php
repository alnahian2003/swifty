<?php

declare(strict_types=1);

// Modern approach with autoloading (if available)
if (file_exists("../../vendor/autoload.php")) {
    require_once "../../vendor/autoload.php";
    
    use Swifty\Controllers\PostController;
    
    try {
        $controller = new PostController();
        $controller->getPostById();
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

    // Get the Post ID
    if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Valid post ID is required"], JSON_PRETTY_PRINT);
        exit;
    }

    $post->id = (int) $_GET["id"];

    // Get Single Post
    if ($post->single()) {
        // Create the Post Array
        $single = [
            "id" => (int) $post->id,
            "category_id" => (int) $post->categoryId,
            "category_name" => $post->categoryName,
            "title" => $post->title,
            "body" => html_entity_decode($post->body),
            "author" => $post->author,
            "created_at" => $post->createdAt ?? $post->created_at
        ];

        // Convert Single post to JSON
        echo json_encode($single, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Post not found"], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
    error_log("API Error: " . $e->getMessage());
}
