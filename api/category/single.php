<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate blog post object
$cat = new Category($db);

// Get the Category ID
$categoryId = isset($_GET["id"]) ? (int) htmlspecialchars($_GET["id"]) : 0;

if ($categoryId <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Valid category ID is required"], JSON_PRETTY_PRINT);
    exit;
}

// Get Single Category
if (!$cat->get($categoryId)) {
    http_response_code(404);
    echo json_encode(["error" => "Category not found"], JSON_PRETTY_PRINT);
    exit;
}


// Create the Post Array
$single = [
    "id" => $cat->id,
    "name" => $cat->name,
    "created_at" => $cat->createdAt
];

// Convert Single post to JSON
echo json_encode($single, JSON_PRETTY_PRINT);
