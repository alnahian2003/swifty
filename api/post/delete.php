<?php

declare(strict_types=1);

// Use centralized bootstrap
require_once '../../bootstrap.php';

// Try modern routing first
if (tryModernRoute('Swifty\Controllers\PostController', 'deletePost')) {
    exit;
}

// Legacy approach for backward compatibility
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
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

    // Get ID from URL parameter or JSON data
    $postId = null;
    
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $postId = (int) $_GET['id'];
    } else {
        // Try to get from JSON data
        $input = file_get_contents("php://input");
        
        if (!empty($input)) {
            $data = json_decode($input, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($data['id']) && is_numeric($data['id'])) {
                $postId = (int) $data['id'];
            }
        }
    }

    if ($postId === null) {
        http_response_code(400);
        echo json_encode(["error" => "Post ID is required"], JSON_PRETTY_PRINT);
        exit;
    }

    $post->id = $postId;

    if ($post->delete()) {
        echo json_encode(["message" => "✅ Post deleted successfully!"], JSON_PRETTY_PRINT);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "❌ Cannot delete post!"], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
    error_log("API Error: " . $e->getMessage());
}
