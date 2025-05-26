<?php
session_start();
echo implode($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Platemate Homepage </title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body>

        <div class="title-bar">
            <h1>PlateMate</h1>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search for a user or a recipe...">
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

        <div class="post-container">
            <div class = "title-field">
                <p>Title</p> 
                <input type="text" id="recipe-title" placeholder="Title">
            </div>
            <div class = "description-field">
                <p>Description</p> 
                <textarea id="recipe-description" placeholder="Paragraph"></textarea>
            </div>
            <div class ="serving-field"> 
                <p>People per serving</p> 
                <input type="number" id="recipe-serving" placeholder="Integer">
            </div> 
           <div class="ingredients-field">
                <p>Ingredients</p>
                <div class="ingredient-row">
                    <div class="input-wrapper">
                    <input type="text" id="recipe-ingredients" placeholder="Ingredient Name">
                    <i class="fa-solid fa-plus"></i>
                    </div>
                    <input type="text" id="ingredient-measurement" placeholder="Measure">
                    <select id="unit-type">
                    <option value="">Unit:</option>
                    <option value="teaspoon">Teaspoon (tsp)</option>
                    <option value="tablespoon">Tablespoon (tbsp)</option>
                    <option value="cup">Cup</option>
                    <option value="milliliter">Milliliter (ml)</option>
                    <option value="liter">Liter (l)</option>
                    <option value="gram">Gram (g)</option>
                    <option value="kilogram">Kilogram (kg)</option>
                    <option value="ounce">Ounce (oz)</option>
                    <option value="pound">Pound (lb)</option>
                    <option value="pinch">Pinch</option>
                    <option value="dash">Dash</option>
                    <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="instructions-field">
                <p>Instructions</p>
                <p style="font-size:1rem">Write your step-by-step instructions here.</p>
                  <div class="step-wrapper">
                    <input type="text" id="recipe-instructions" placeholder="Step X: ...">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </div>
            <div class="posting-tags-field">
                <p>Tags</p>
                <input type="text" id="tag-Input" placeholder="Insert tag...">
            </div>
            <div class="picture-field">
                <p>Picture upload</p>
                <p style="font-size:1rem">Upload your dish picture here.</p>
                <div class="upload-pic-container"> 
                    <button class = "upload-picture">
                        <i class="fa-regular fa-image"></i> 
                        <div class="plus"> 
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <!-- <img src = "Image.png"> -->
                    </button>
                </div>
            </div>
            <button class = "post-recipe-button">Post recipe</button>
        </div>
    </body>
</html>