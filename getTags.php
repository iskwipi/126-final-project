<?php
$conn = new mysqli("localhost", "root", "", "platemate");

$result = $conn->query("SELECT recipe.recipeID, user.username, recipe.recipeTitle, picture.pictureLink, rating.avgRating, rating.countRating, recipe.recipeDescription, tag.tagTitle
FROM ((((((recipe
INNER JOIN owns ON recipe.recipeID = owns.recipeID)
INNER JOIN user ON owns.userID = user.userID)
INNER JOIN picture ON recipe.recipeID = picture.recipeID)
INNER JOIN (SELECT recipeID, AVG(rating) AS avgRating, COUNT(rating) AS countRating FROM rates GROUP BY recipeID)
AS rating ON recipe.recipeID = rating.recipeID)
LEFT JOIN tags ON recipe.recipeID = tags.recipeID)
LEFT JOIN tag ON tags.tagID = tag.tagID)");

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['recipeID'];
    if (!isset($recipes[$id])) {
        $recipes[$id] = [
            "recipeID" => $row['recipeID'],
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
header('Content-Type: application/json');
echo json_encode($output);
?>