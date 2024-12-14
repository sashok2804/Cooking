<?php
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "Cook"; 



if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Max-Age: 1000");

    http_response_code(204);
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();
$data = json_decode(file_get_contents("php://input"));
$headers = getallheaders();
