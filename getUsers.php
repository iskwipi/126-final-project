<?php
$conn = new mysqli("localhost", "root", "", "platemate");
session_start();
$currentUserID = $_SESSION['userID'] ?? null;

$sql = "
SELECT 
    user.userID, username, dateOfBirth, occupation, email, provinceID, cityID, 
    followerCount, avgRating,
    IF(f.userID IS NOT NULL, 1, 0) AS isFollowing
FROM user
LEFT JOIN (
    SELECT followingID AS userID, COUNT(followerID) AS followerCount 
    FROM follows 
    GROUP BY followingID
) AS follows ON user.userID = follows.userID
LEFT JOIN (
    SELECT owns.userID as userID, AVG(avgRating) as avgRating 
    FROM owns
    LEFT JOIN (
        SELECT recipeID, AVG(rating) AS avgRating 
        FROM rates 
        GROUP BY recipeID
    ) AS rating ON owns.recipeID = rating.recipeID
    GROUP BY owns.userID
) AS owns ON user.userID = owns.userID
LEFT JOIN (
    SELECT followingID AS userID
    FROM follows 
    WHERE followerID = ?
) AS f ON user.userID = f.userID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUserID);
$stmt->execute();
$result = $stmt->get_result();
$users = [];

while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
?>