<?php
session_start();
echo implode($_SESSION);
echo $_GET["id"];

$conn = new mysqli("localhost", "root", "", "platemate");

$recipeID = $_GET["id"];
$sql = "SELECT * FROM recipe WHERE recipeID = $recipeID";
$recipes = $conn->query($sql);

if($recipes->num_rows == 1){
    $recipe = $recipes->fetch_assoc();

    $sql = "SELECT tagTitle FROM tag as T
        INNER JOIN (SELECT tagID FROM tags WHERE recipeID = $recipeID) as R
        ON T.tagID = R.tagID";
    $tags = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT pictureLink FROM picture WHERE recipeID = $recipeID";
    $pictures = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT measurementValue, unitName, ingredientName FROM unit as U
        INNER JOIN (SELECT measurementValue, unitID, ingredientName FROM ingredient as I
            INNER JOIN (SELECT * FROM contains WHERE recipeID = $recipeID) as C
            ON I.ingredientID = C.ingredientID) as IC
        ON U.unitID = IC.unitID";
    $ingredients = $conn->query($sql);
    $ingredientList = "<ul>";
    while($ingredient = $ingredients->fetch_assoc()){
        $ingredientList .= "<li>" . $ingredient["measurementValue"] . " " . $ingredient["unitName"] . "s of " . $ingredient["ingredientName"] . "</li>";
    }
    $ingredientList .= "</ul>";

    $sql = "SELECT instructionNumber, instructionDetails FROM instruction WHERE recipeID = $recipeID";
    $instructions = $conn->query($sql);
    $instructionList = "<ul>";
    while($instruction = $instructions->fetch_assoc()){
        $instructionList .= "<li>Step " . $instruction["instructionNumber"] . ": " . $instruction["instructionDetails"] . "</li>";
    }
    $instructionList .= "</ul>";

    $sql = "SELECT username FROM user as U
        INNER JOIN (SELECT userID FROM owns WHERE recipeID = $recipeID) as O
        ON U.userID = O.userID";
    $owns = $conn->query($sql)->fetch_assoc();

    $sql = "SELECT username FROM user as U
        INNER JOIN (SELECT userID FROM saves WHERE recipeID = $recipeID) as S
        ON U.userID = S.userID";
    $saves = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT U.username as username, C.comment as comment, R.rating as rating FROM ((user as U
        INNER JOIN (SELECT userID, comment FROM comments WHERE recipeID = $recipeID) as C ON U.userID = C.userID)
        INNER JOIN (SELECT userID, rating FROM rates WHERE recipeID = $recipeID) as R ON U.userID = R.userID)";
    $ratings = $conn->query($sql);
    $ratingList = "";
    while($rating = $ratings->fetch_assoc()){
        $ratingList .= '
        <div class="interaction-container"> 
            <div class="user-header-row">
                <div id="user-profile">
                    <a href="profile.php">' . $rating["username"] . ':</a>
                </div>
                <div class="user-ratings">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <p>' . $rating["rating"] . '</p>
                </div>
            </div>
            <div class="user-comment">
                <p>' . $rating["comment"] . '</p>
            </div>
        </div>  
        ';
    }

    $sql = "SELECT AVG(rating) AS avgRating, COUNT(rating) AS countRating
        FROM rates WHERE rates.recipeID = $recipeID GROUP BY recipeID";
    $average = $conn->query($sql)->fetch_assoc();

    $recipeContent =  '
    <div class="display-post-feed">
        <div class="post-container"> 
            <div class="post-title">
                <div class="post-title-row">
                    <h2>' . $recipe["recipeTitle"] . '</h2>
                    <div id="poster-profile">
                        <a href="profile.php">' . $owns["username"] . '</a>
                        <div class="post-bookmark">
                            <button type="button" onclick="saveRecipe(' . $recipeID . ', this)">
                                <i class="fa-regular fa-bookmark "></i>
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
                    <p>' . $average["avgRating"] . ' stars (' . $average["countRating"] . ' ratings)</p>
                </div>
            </div>
            <div class="post-description">
                <p>' . $recipe["recipeDescription"] . '</p>
            </div>
            <div class="post-image">
                <img src="' . $pictures["pictureLink"] . '" alt="recipe image">
            </div>
            <div class="post-tags">
                <p>Tags: </p>
                <p>#' . implode(', #', $tags) . '</p>
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
                <div class="your-interactions">
                    <div class="your-ratings">
                        <p>Your ratings:</p>
                        <button type="button"> 
                            <i class="fa-regular fa-star"></i>
                        </button>
                        <button type="button" class="rate-button"> 
                            <i class="fa-regular fa-star"></i>
                        </button>
                        <button type="button" class="rate-button"> 
                            <i class="fa-regular fa-star"></i>
                        </button>
                        <button type="button" class="rate-button"> 
                            <i class="fa-regular fa-star"></i>
                        </button>
                        <button type="button" class="rate-button"> 
                            <i class="fa-regular fa-star"></i>
                        </button>
                    </div>
                    <div class="your-comment">
                        <input type="text" id="comment-input" placeholder="Leave a comment...">
                        <button type="button" id="comment-button">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <hr>
                <br>
                <div class="post-comments-ratings">
                    ' . $ratingList . '
                </div>
            </div>
        </div>
    </div>
    ';
}else{
    echo "Invalid recipeID</br>" . $conn->error;
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Post </title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
        <script src="renderFeed.js"></script>
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
            <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i> Deep Search</a>
            <a href="cookbook.php"><i class="fa-solid fa-book"></i> Cookbook</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        </div>

        <?php
            echo $recipeContent;
        ?>
    </body>
</html>