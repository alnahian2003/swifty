<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Content-type,Access-Control-Allow-Origin, Authorization, X-Requested-With");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate Category object
$cat = new Category($db);

// Get raw data
$data = file_get_contents("php://input") != null ? json_decode(file_get_contents("php://input")) : die();

$cat->id = $data->id;
$cat->name = $data->name;

if ($cat->update()) {
    echo json_encode(["message" => "✅ Category Updated!"]);
} else {
    echo json_encode(["message" => "❌ Cannot Update the Category!"]);
}
