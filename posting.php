<?php
session_start();
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
            <img src="logo-white.png">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search for a user or a recipe..."  onkeypress="handleSearch(event)">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <form class="post-container" id="post-recipe-form" method="POST" action="postRecipe.php" enctype="multipart/form-data">
            <div class="title-field">
                <p>Title</p>
                <input type="text" id="recipe-title" name="recipe-title" placeholder="Title" required>
            </div>
            <div class="description-field">
                <p>Description</p>
                <textarea id="recipe-description" name="recipe-description" placeholder="Paragraph" required></textarea>
            </div>
            <div class="serving-field">
                <p>People per serving</p>
                <input type="number" id="recipe-serving" name="recipe-serving" placeholder="Integer" min="1" required>
            </div>
            <div class="ingredients-field">
                <p>Ingredients</p>
                <div class="ingredient-row">
                    <input type="number" id="ingredient-measurement" name="ingredient-measurement[]" placeholder="Measure" required>
                    <select id="unit-type" name="unit-type[]" required>
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
                    <div class="input-wrapper">
                        <input type="text" id="recipe-ingredients" name="recipe-ingredients[]" placeholder="Ingredient Name" required>
                        <i class="fa-solid fa-plus" onclick="addIngredientRow(this)"></i>
                        <i class="fa-regular fa-circle-xmark" onclick="removeIngredientRow(this)"></i>
                    </div>
                </div>
            </div>
            <div class="instructions-field">
                <p>Instructions</p>
                <!-- <p style="font-size:1rem">Write your step-by-step instructions here.</p> -->
                <div class="input-wrapper">
                    <textarea id="recipe-instructions" name="recipe-instructions[]" placeholder="Step-by-step instructions" required></textarea>
                    <i class="fa-solid fa-plus" onclick="addInstructionRow(this)"></i>
                    <i class="fa-regular fa-circle-xmark" onclick="removeInstructionRow(this)"></i>
                </div>
            </div>
            <div class="posting-tags-field">
                <p>Tags</p>
                <div class="input-wrapper">
                    <input type="text" id="tag-Input" name="tag-Input[]" placeholder="Insert tag..." required>
                    <i class="fa-solid fa-plus" onclick="addTagRow(this)"></i>
                    <i class="fa-regular fa-circle-xmark" onclick="removeTagRow(this)"></i>
                </div>
            </div>
            <div class="picture-field">
                <p>Picture</p>
                <input type="text" id="picture-link" name="picture-link" placeholder="Picture link" required>
            </div>
            <div class="submit-cancel">
                <button class="post-recipe-button" type="submit">Post Recipe</button>
                <a href="profile.php">Back to profile</a>
            </div>

        </form>
        <script src="posting.js"></script>
    </body>
</html>