<?php
session_start();
$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = isset($_SESSION["userID"]) ? intval($_SESSION["userID"]) : 0;
$recipeID = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($recipeID === 0) {
    $_SESSION['error'] = "Invalid recipe ID.";
    header("Location: homepage.php");
    exit();
}

// fetch recipe details
$stmt = $conn->prepare("SELECT * FROM recipe WHERE recipeID = ?");
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$recipes = $stmt->get_result();

if ($recipes->num_rows == 1) {
    $recipe = $recipes->fetch_assoc();

    // fetch tags
    $stmt = $conn->prepare("SELECT t.tagTitle FROM tag t
                            INNER JOIN tags ts ON t.tagID = ts.tagID
                            WHERE ts.recipeID = ?");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $tags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $tagTitles = array_column($tags, 'tagTitle'); // Extract tagTitle values
    $stmt->close();

    // fetch pictures
    $stmt = $conn->prepare("SELECT pictureLink FROM picture WHERE recipeID = ?");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $pictures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $pictureLink = !empty($pictures) ? $pictures[0]['pictureLink'] : 'default-recipe.png';
    $stmt->close();

    // fetch ingredients
    $stmt = $conn->prepare("
        SELECT c.measurementValue, u.unitName, i.ingredientName
        FROM contains c
        JOIN ingredient i ON c.ingredientID = i.ingredientID
        LEFT JOIN unit u ON c.unitID = u.unitID
        WHERE c.recipeID = ?
    ");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $ingredients = $stmt->get_result();
    $ingredientList = "<ul>";
    while ($ingredient = $ingredients->fetch_assoc()) {
        $unit = $ingredient["unitName"] ? $ingredient["unitName"] . "s" : "";
        $ingredientList .= "<li>" . htmlspecialchars($ingredient["measurementValue"]) . " " . htmlspecialchars($unit) . " of " . htmlspecialchars($ingredient["ingredientName"]) . "</li>";
    }
    $ingredientList .= "</ul>";
    $stmt->close();

    // fetch instructions
    $stmt = $conn->prepare("SELECT instructionNumber, instructionDetails FROM instruction WHERE recipeID = ? ORDER BY instructionNumber");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $instructions = $stmt->get_result();
    $instructionList = "<ul>";
    while ($instruction = $instructions->fetch_assoc()) {
        $instructionList .= "<li>Step " . htmlspecialchars($instruction["instructionNumber"]) . ": " . htmlspecialchars($instruction["instructionDetails"]) . "</li>";
    }
    $instructionList .= "</ul>";
    $stmt->close();

    // fetch owner
    $stmt = $conn->prepare("SELECT u.username FROM user u
                            INNER JOIN owns o ON u.userID = o.userID
                            WHERE o.recipeID = ?");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $owns = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // fetch saves for display
    $stmt = $conn->prepare("SELECT u.username FROM user u
                            INNER JOIN saves s ON u.userID = s.userID
                            WHERE s.recipeID = ?");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $saves = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // fetch ratings and comments
    $stmt = $conn->prepare("
        SELECT u.username, c.comment, r.rating
        FROM user u
        INNER JOIN comments c ON u.userID = c.userID
        INNER JOIN rates r ON u.userID = r.userID
        WHERE c.recipeID = ? AND r.recipeID = ?
    ");
    $stmt->bind_param("ii", $recipeID, $recipeID);
    $stmt->execute();
    $ratings = $stmt->get_result();
    $ratingList = "";
    while ($rating = $ratings->fetch_assoc()) {
        $ratingList .= '
        <div class="interaction-container"> 
            <div class="user-header-row">
                <div id="user-profile">
                    <a href="profile.php?id=' . htmlspecialchars($rating["username"]) . '">' . htmlspecialchars($rating["username"]) . ':</a>
                </div>
                <div class="user-ratings">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <p>' . htmlspecialchars($rating["rating"]) . '</p>
                </div>
            </div>
            <div class="user-comment">
                <p>' . htmlspecialchars($rating["comment"]) . '</p>
            </div>
        </div>  
        ';
    }
    $stmt->close();

    // fetch average rating
    $stmt = $conn->prepare("
        SELECT AVG(rating) AS avgRating, COUNT(rating) AS countRating
        FROM rates WHERE recipeID = ?
    ");
    $stmt->bind_param("i", $recipeID);
    $stmt->execute();
    $average = $stmt->get_result()->fetch_assoc();
    $avgRating = $average && $average["avgRating"] ? rtrim(rtrim(number_format($average["avgRating"], 1), '0'), '.') : 0;
    $countRating = $average ? $average["countRating"] : 0;
    $stmt->close();

    $recipeContent = '
    <div class="display-post-feed">
        <div class="post-container"> 
            <div class="post-title">
                <div class="post-title-row">
                    <h2>' . htmlspecialchars($recipe["recipeTitle"]) . '</h2>
                    <div id="poster-profile">
                        <a href="profile.php?id=' . htmlspecialchars($owns["username"]) . '">' . htmlspecialchars($owns["username"]) . '</a>
                        <div class="post-bookmark">
                            <button type="button" onclick="saveRecipe(' . $recipeID . ', this)">
                                <i class="fa-regular fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="post-rate">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <p>' . $avgRating . ' stars (' . $countRating . ' ratings)</p>
                </div>
            </div>
            <div class="post-description">
                <p>' . htmlspecialchars($recipe["recipeDescription"]) . '</p>
            </div>
            <div class="post-image">
                <img src="' . htmlspecialchars($pictureLink) . '" alt="recipe image">
            </div>
            <div class="post-tags">
                <p>Tags: </p>
                <p>' . (!empty($tagTitles) ? '#' . implode(' #', array_map('htmlspecialchars', $tagTitles)) : 'No tags') . '</p>
            </div>
            <div class="servings-multiplier">
                <button type="button" id="decrement-servings" class="servings-btn">-</button>
                <input type="number" id="servings-count" value="1" min="1" readonly>
                <button type="button" id="increment-servings" class="servings-btn">+</button>
                <p>Servings</p>
            </div>
            <hr>
            <div class="recipe-information">
                <h3>Ingredients:</h3>
                <div class="post-ingredients">
                    ' . $ingredientList . '
                </div>
                <div class="recipe-instructions">
                    <h4>Instructions: </h4>
                    <div class="recipe-steps">
                        ' . $instructionList . '
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="post-interactions">
            <h5>Ratings</h5>
            <div class="post-ratings-container">
    ';

    // checks if user has commented
    $stmt = $conn->prepare("SELECT * FROM comments WHERE userID = ? AND recipeID = ?");
    $stmt->bind_param("ii", $userID, $recipeID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $recipeContent .= '
                <div class="your-interactions">
                    <form method="post" action="postRating.php">
                        <input type="hidden" name="recipeID" value="' . $recipeID . '">
                        <div class="your-ratings">
                            <p>Your rating:</p>
                            <input type="number" id="rating-input" name="rating-input" min="0" max="5" step=".5" value="0">
                        </div>
                        <div class="your-comment">
                            <input type="text" id="comment-input" name="comment-input" placeholder="Leave a comment...">
                            <button type="submit" id="comment-button">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <hr>
                <br>
        ';
    }
    $stmt->close();

    $recipeContent .= '
                <div class="post-comments-ratings">
                    ' . $ratingList . '
                </div>
            </div>
        </div>
    </div>
    ';
} else {
    $_SESSION['error'] = "Invalid recipe ID.";
    header("Location: homepage.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe["recipeTitle"] ?? 'Post'); ?> - PlateMate</title>
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="title-bar">
        <img src="logo-white.png">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a user or a recipe..." onkeypress="handleSearch(event)">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
    <div class="left-panel">
        <a href="homepage.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i> Recipe Search</a>
        <a href="cookbook.php"><i class="fa-solid fa-book"></i> Cookbook</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php echo $recipeContent; ?>
    <script src="searchHandler.js"></script>
    <script>
        function saveRecipe(recipeId, button) {
            fetch('saveRecipe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `recipe_id=${recipeId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.querySelector('i').classList.toggle('fa-regular');
                    button.querySelector('i').classList.toggle('fa-solid');
                    alert('Recipe saved/unsaved successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => alert('Error: ' + error.message));
        }

        const decrementBtn = document.getElementById('decrement-servings');
        const incrementBtn = document.getElementById('increment-servings');
        const servingsInput = document.getElementById('servings-count');

        if (decrementBtn && incrementBtn && servingsInput) {
            decrementBtn.addEventListener('click', () => {
                let value = parseInt(servingsInput.value);
                if (value > 1) {
                    servingsInput.value = value - 1;
                }
            });

            incrementBtn.addEventListener('click', () => {
                let value = parseInt(servingsInput.value);
                servingsInput.value = value + 1;
            });
        }
    </script>
</body>
</html>