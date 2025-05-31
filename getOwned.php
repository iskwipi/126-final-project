<?php
session_start();
$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$profileID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sortMode = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$orderBy = ($sortMode === 'rating') ? 'avgRating DESC' : 'r.recipeID DESC';

$stmt = $conn->prepare("
    SELECT r.recipeID, r.recipeTitle, r.recipeDescription, r.peoplePerServing,
           o.userID,
           GROUP_CONCAT(DISTINCT tag.tagTitle) AS tags,
           GROUP_CONCAT(i.instructionDetails) AS instructions,
           p.pictureLink,
           AVG(rates.rating) AS avgRating,
           COUNT(rates.rating) AS countRating
    FROM recipe r
    JOIN owns o ON r.recipeID = o.recipeID
    LEFT JOIN tags t ON r.recipeID = t.recipeID
    LEFT JOIN tag ON t.tagID = tag.tagID
    LEFT JOIN instruction i ON r.recipeID = i.recipeID
    LEFT JOIN picture p ON r.recipeID = p.recipeID AND p.pictureNumber = 1
    LEFT JOIN rates ON r.recipeID = rates.recipeID
    WHERE o.userID = ?
    GROUP BY r.recipeID
    ORDER BY $orderBy
");
$stmt->bind_param("i", $profileID);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($recipes as &$recipe) {
    $recipe['tags'] = $recipe['tags'] ? explode(',', $recipe['tags']) : [];
    $recipe['instructions'] = $recipe['instructions'] ? explode(',', $recipe['instructions']) : [];
}
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($recipes);
?>