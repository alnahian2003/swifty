<?php
// Headers for GET Request
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

include_once("../../config/Database.php");
include_once("../../models/Category.php");


// Instantiate DB and Connect to It
$database = new Database();
$db = $database->connect();


// Instantiate category object
$cat = new Category($db);

// Category Query
$cats = $cat->read();

// Get Rows Count
$rows = $cats->rowCount();



/* IMPORTANT PART: THIS IS WHERE I'M PROCESSING THE DB DATA INTO JSON */

// Check For Categories in The Database
if ($rows > 0) {
    // Posts Available
    $catsArr = [];

    while ($row = $cats->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $catItem = [
            "id" => $id,
            "name" => $name,
            "created_at" => $created_at,
        ];

        // Push post item to data
        array_push($catsArr, $catItem);
    }

    // Turn categories array into JSON and display it
    echo json_encode($catsArr, JSON_PRETTY_PRINT);
} else {
    // No category in the DB
    echo json_encode(["error" => "No Category Found"]);
}
