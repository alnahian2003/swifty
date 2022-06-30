<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate blog post object
$cat = new Category($db);

// Get the Post ID
$cat->id = isset($_GET["id"]) ? htmlspecialchars($_GET["id"]) : die();

// Get Single Category
$cat->single();


// Create the Post Array
$single = [
    "id" => $cat->id,
    "name" => $cat->name,
    "created_at" => $cat->createdAt
];

// Convert Single post to JSON
echo json_encode($single, JSON_PRETTY_PRINT);
