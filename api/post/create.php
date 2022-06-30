<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Methods: POST");
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

$post->title = $data->title;
$post->categoryId = $data->category_id;
$post->body = $data->body;
$post->author = $data->author;

if ($post->create()) {
    echo json_encode(["message" => "Post Created Successfully"]);
} else {
    echo json_encode(["message" => "Cannot Create Post"]);
}
