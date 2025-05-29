<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$servername = "localhost";    
$username = "root";           
$password = "";               
$dbname = "platemate"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$currentUserID = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT followingID FROM follows WHERE followerID = ?");
$stmt->bind_param("i", $currentUserID);
$stmt->execute();
$result = $stmt->get_result();

$following = [];
while ($row = $result->fetch_assoc()) {
    $following[] = $row['followingID'];
}

echo json_encode($following);
