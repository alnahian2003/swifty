<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

include_once("../../config/Database.php");
include_once("../../models/Post.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate blog post object
$post = new Post($db);

// Blog Post Query
$posts = $post->read();

// Get Rows Count
$rows = $posts->rowCount();



/* IMPORTANT PART: THIS IS WHERE I'M PROCESSING THE DB DATA INTO JSON */

// Check For Blog Posts in The Database
if ($rows > 0) {
    // Posts Available
    $postsArr = [];

    while ($row = $posts->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $postItem = [
            "id" => $id,
            "category_id" => $category_id,
            "category_name" => $category_name,
            "title" => $title,
            "body" => html_entity_decode($body),
            "author" => $author,
            "created_at" => $created_at,
        ];

        // Push post item to data
        array_push($postsArr, $postItem);
    }

    // Turn posts array into JSON and display it
    echo json_encode($postsArr, JSON_PRETTY_PRINT);
} else {
    // No Posts in the DB
    echo json_encode(["error" => "No Post Found"]);
}
