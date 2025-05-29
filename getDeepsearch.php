<?php
// 1. Database connection
$host = 'localhost';
$dbname = 'platemate';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Get tags and ingredient matches from the form
$tagsFromFormRaw = isset($_POST['tag-Input']) ? $_POST['tag-Input'] : [];
$tagsFromForm = array_filter($tagsFromFormRaw, function($v) {
    return trim($v) !== '';
});
$ingredientMatchesRaw = isset($_POST['recipe-ingredients']) ? $_POST['recipe-ingredients'] : [];
$ingredientMatches = array_filter($ingredientMatchesRaw, function($v) {
    return trim($v) !== '';
});

// 3. Build SQL and parameters based on whether tags are provided
if (count($tagsFromForm) > 0) {
    // Tags provided: filter recipes with tags
    $placeholders = implode(',', array_fill(0, count($tagsFromForm), '?'));
    $sql = "
        SELECT r.recipeID
        FROM recipe AS r
        JOIN tags AS t ON r.recipeID = t.recipeID
        JOIN tag AS ta ON t.tagID = ta.tagID
        WHERE ta.tagTitle IN ($placeholders)
        GROUP BY r.recipeID
        HAVING COUNT(DISTINCT ta.tagTitle) >= ?
    ";
    $stmt = $conn->prepare($sql);

    // Prepare binding types and values
    $types = str_repeat('s', count($tagsFromForm)) . 'i';
    $params = array_merge($tagsFromForm, [1]);
    $bindNames = [];
    $bindNames[] = &$types;
    foreach ($params as $key => $value) {
        $bindNames[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindNames);

    $stmt->execute();
    $result = $stmt->get_result();
    $recipes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    echo "filtered recipes";
} else {
    // No tags provided: fetch all recipes
    $sql = "SELECT recipeID FROM recipe";
    $result = $conn->query($sql);
    $recipes = $result->fetch_all(MYSQLI_ASSOC);
    echo "all recipes";
}

unset($recipe);
$recipeIDs = [];
if (!empty($recipes)) {
    foreach ($recipes as $recipe) {
        $recipeIDs[] = $recipe['recipeID'];
    }
}
unset($recipes);

$result = $conn->query("SELECT recipe.recipeID, user.userID, user.username, recipe.recipeTitle, picture.pictureLink, rating.avgRating, rating.countRating, recipe.recipeDescription, tag.tagTitle
FROM ((((((recipe
INNER JOIN owns ON recipe.recipeID = owns.recipeID)
INNER JOIN user ON owns.userID = user.userID)
INNER JOIN picture ON recipe.recipeID = picture.recipeID)
LEFT JOIN (SELECT recipeID, AVG(rating) AS avgRating, COUNT(rating) AS countRating FROM rates GROUP BY recipeID) AS rating ON recipe.recipeID = rating.recipeID)
LEFT JOIN tags ON recipe.recipeID = tags.recipeID)
LEFT JOIN tag ON tags.tagID = tag.tagID)");

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

$recipes = array_values($recipes);

// Filter recipes
$filteredRecipes = array_filter($recipes, function ($recipe) use ($recipeIDs) {
    return in_array($recipe['recipeID'], $recipeIDs);
});

// Reset array keys (optional)
unset($recipes);
$recipes = array_values($filteredRecipes);

// 4. For each recipe, calculate the score
if(count($ingredientMatches) > 0){
    foreach ($recipes as &$recipe) {
        $recipeID = $recipe['recipeID'];

        $sqlIng = "
            SELECT i.ingredientName
            FROM contains AS c
            JOIN ingredient AS i ON c.ingredientID = i.ingredientID
            WHERE c.recipeID = ?
        ";
        $stmtIng = $conn->prepare($sqlIng);
        $stmtIng->bind_param('i', $recipeID);
        $stmtIng->execute();
        $resultIng = $stmtIng->get_result();
        $ingredients = [];
        while ($row = $resultIng->fetch_assoc()) {
            $ingredients[] = $row['ingredientName'];
        }

        // Calculate score
        $score = 0;
        foreach ($ingredients as $ing) {
            if (in_array($ing, $ingredientMatches)) {
                $score++;
            }
        }
        $recipe['score'] = $score;
        $stmtIng->close();
    }
}else{
    foreach ($recipes as &$recipe) {
        $recipe['score'] = 1;
    }
}

// 5. Sort recipes by score descending
usort($recipes, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});

$result = [];
// 6. Display results
if (!empty($recipes)) {
    unset($recipe);
    foreach ($recipes as $recipe) {
        if($recipe['score'] > 0){
            // echo "<h3>{$recipe['recipeTitle']} (Score: {$recipe['score']})</h3>";
            // echo "<p>{$recipe['recipeDescription']}</p>";
            // echo "<p>Serves: {$recipe['peoplePerServing']}</p>";
            $result[] = $recipe;
        }
    }
} else {
    echo "<p>No recipes found!</p>";
}

// 7. Close connection
$conn->close();
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Platemate search results </title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="title-bar">
            <img src="logo-white.png">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search for a user or a recipe..."  onkeypress="handleSearch(event)">
                <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>    
        </div>
        <div class="left-panel">
            <a href="homepage.php"><i class="fa-solid fa-house"></i> Home</a>
            <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i>  Deep Search</a>
            <a href="cookbook.php"><i class="fa-solid fa-book"></i>  Cookbook</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i>  Profile</a>
        </div>
        <div class="search-recipe-feed">
            <div class="search-feed-header">
                <h2>Deep search results</h2>
                <div class="mode-section">
                    <label for="display-mode">Display Mode: </label>
                    <select id="display-mode">
                        <option value="score">Nearest Match</option>
                        <option value="rating">Top Rated</option>
                    </select>
                </div>
            </div>
            <div class="search-posts" id="search-posts"></div>  
        </div>
        <script>
            const deepsearchResult = <?php echo json_encode($result); ?>;
            console.log(deepsearchResult);
        </script>
        <script src="renderDeepSearch.js"></script>
        <script src="searchHandler.js"></script>
    </body>
</html>