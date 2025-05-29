<?php
session_start();
echo implode($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Platemate Deep Search </title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="title-bar">
            <img src="logo-white.png">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Quick search"  onkeypress="handleSearch(event)">
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
        <div class="deepsearch-feed">
            <div class="deepsearch-container">
                <h3>Deep Search</h3>
                <form class="recipe-search-fields" method="POST" action="getDeepsearch.php">
                    <div class="ingredients-field">
                        <h4>Ingredients Lookup</h4>
                        <div class="input-wrapper">
                            <input type="text" id="recipe-ingredients" name="recipe-ingredients[]" placeholder="Ingredient Name">
                            <i class="fa-solid fa-plus" onclick="addIngredientRow(this)"></i>
                            <i class="fa-regular fa-circle-xmark" onclick="removeIngredientRow(this)"></i>
                        </div>
                    </div>
                    <div class="posting-tags-field">
                        <h4>Tags Filter</h4>
                        <div class="input-wrapper">
                            <input type="text" id="tag-Input" name="tag-Input[]" placeholder="Insert tag...">
                            <i class="fa-solid fa-plus" onclick="addTagRow(this)"></i>
                            <i class="fa-regular fa-circle-xmark" onclick="removeTagRow(this)"></i>
                        </div>
                    </div>
                    <button type="submit" class="deep-search-button">Search recipe</button>
                </form>
            </div>
        </div>
        <script src="searchHandler.js"></script>
        <script src="searching.js"></script>
    </body>
</html>

