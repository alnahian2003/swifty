<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Content-type,Access-Control-Allow-Origin, Authorization, X-Requested-With");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate blog post object
$cat = new Category($db);

// Get raw POSTed data
$data = file_get_contents("php://input") != null ? json_decode(file_get_contents("php://input")) : die();

$cat->id = $data->id;

if ($cat->delete()) {
    echo json_encode(["message" => "✅ Category Deleted!"]);
} else {
    echo json_encode(["message" => "❌ Cannot Delete The Category!"]);
}
