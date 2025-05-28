<?php
$conn = new mysqli("localhost", "root", "", "platemate");

$result = $conn->query("SELECT user.userID, username, dateOfBirth, occupation, email, provinceID, cityID, followerCount, avgRating FROM ((user
LEFT JOIN (SELECT followingID AS userID, COUNT(followerID) AS followerCount FROM follows GROUP BY followingID) AS follows ON user.userID = follows.userID)
LEFT JOIN (SELECT owns.userID as userID, AVG(avgRating) as avgRating FROM owns
LEFT JOIN (SELECT recipeID, AVG(rating) AS avgRating, COUNT(rating) AS countRating FROM rates GROUP BY recipeID) AS rating ON owns.recipeID = rating.recipeID
GROUP BY owns.userID) AS owns ON user.userID = owns.userID)
");
$users = [];

while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
?>