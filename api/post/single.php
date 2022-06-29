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

// Get the Post ID
$post->id = isset($_GET["id"]) ? htmlspecialchars($_GET["id"]) : die();

// Get Single Post
$post->single();


// Create the Post Array
$single = [
    "id" => $post->id,
    "category_id" => $post->categoryId,
    "category_name" => $post->categoryName,
    "title" => $post->title,
    "body" => $post->body,
    "author" => $post->author,
    "created_at" => $post->createdAt
];

// Convert Single post to JSON
echo json_encode($single, JSON_PRETTY_PRINT);
