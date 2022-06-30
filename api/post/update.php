<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Content-type,Access-Control-Allow-Origin, Authorization, X-Requested-With");

include_once("../../config/Database.php");
include_once("../../models/Post.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate blog post object
$post = new Post($db);

// Get raw POSTed data
$data = file_get_contents("php://input") != null ? json_decode(file_get_contents("php://input")) : die();

$post->id = $data->id;
$post->title = $data->title;
$post->categoryId = $data->category_id;
$post->body = $data->body;
$post->author = $data->author;

if ($post->update()) {
    echo json_encode(["message" => "✅ Post Updated!"]);
} else {
    echo json_encode(["message" => "❌ Cannot Update!"]);
}
