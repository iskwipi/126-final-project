<?php
$conn = new mysqli("localhost", "root", "", "platemate");

$result = $conn->query("SELECT user.userID, username, dateOfBirth, occupation, email, provinceID, cityID, followerCount FROM user
LEFT JOIN (SELECT followingID AS userID, COUNT(followerID) AS followerCount FROM follows GROUP BY followingID)
AS follows ON user.userID = follows.userID");
$users = [];

while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
?>