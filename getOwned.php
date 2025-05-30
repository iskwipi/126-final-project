<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) && !isset($_GET["id"])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in or no user ID provided']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$userID = isset($_GET["id"]) ? (int)$_GET["id"] : (int)$_SESSION['userID'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT recipe.recipeID, owns.userID, user.username, recipe.recipeTitle, picture.pictureLink, 
                        rating.avgRating, rating.countRating, recipe.recipeDescription, tag.tagTitle
FROM ((((((recipe
INNER JOIN owns ON recipe.recipeID = owns.recipeID)
INNER JOIN user ON owns.userID = user.userID)
INNER JOIN picture ON recipe.recipeID = picture.recipeID)
LEFT JOIN (SELECT recipeID, AVG(rating) AS avgRating, COUNT(rating) AS countRating FROM rates GROUP BY recipeID)
AS rating ON recipe.recipeID = rating.recipeID)
LEFT JOIN tags ON recipe.recipeID = tags.recipeID)
LEFT JOIN tag ON tags.tagID = tag.tagID)
WHERE owns.userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['recipeID'];
    if (!isset($recipes[$id])) {
        $recipes[$id] = [
            "recipeID" => $row['recipeID'],
            "userID" => $row['userID'],
            "username" => $row['username'],
            "recipeTitle" => $row['recipeTitle'],
            "pictureLink" => $row['pictureLink'],
            "avgRating" => $row['avgRating'],
            "countRating" => $row['countRating'],
            "recipeDescription" => $row['recipeDescription'],
            "tags" => []
        ];
    }
    if ($row['tagTitle']) {
        $recipes[$id]['tags'][] = $row['tagTitle'];
    }
}

$output = array_values($recipes);
echo json_encode($output);

$stmt->close();
$conn->close();
?>