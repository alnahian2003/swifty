<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Content-type,Access-Control-Allow-Origin, Authorization, X-Requested-With");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate category object
$cat = new Category($db);

// Get raw POSTed data
$data = file_get_contents("php://input") != null ? json_decode(file_get_contents("php://input")) : die();

$cat->name = $data->name;

if ($cat->create()) {
    echo json_encode(["message" => "Category Created Successfully"]);
} else {
    echo json_encode(["message" => "Cannot Create Category"]);
}
