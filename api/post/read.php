<?php

declare(strict_types=1);

// Use centralized bootstrap
require_once '../../bootstrap.php';

// Try modern routing first
if (tryModernRoute('Swifty\Controllers\PostController', 'getAllPosts')) {
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

    // Blog Post Query
    $posts = $post->read();

    // Get Rows Count
    $rows = $posts->rowCount();

    // Check For Blog Posts in The Database
    if ($rows > 0) {
        // Posts Available
        $postsArr = [];

        while ($row = $posts->fetch(PDO::FETCH_ASSOC)) {
            $postItem = [
                "id" => (int) $row['id'],
                "category_id" => (int) $row['category_id'],
                "category_name" => $row['category_name'],
                "title" => $row['title'],
                "body" => html_entity_decode($row['body']),
                "author" => $row['author'],
                "created_at" => $row['created_at'],
            ];

            $postsArr[] = $postItem;
        }

        // Turn posts array into JSON and display it
        echo json_encode($postsArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // No Posts in the DB
        http_response_code(404);
        echo json_encode(["error" => "No posts found"], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
    error_log("API Error: " . $e->getMessage());
}
