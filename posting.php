<?php
session_start(); 

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "platemate";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST['recipe-title'], $_POST['recipe-description'], $_POST['recipe-serving'], $_POST['recipe-instructions'], $_POST['tag-Input'], $_POST['recipe-ingredients'], $_POST['ingredient-measurement'], $_POST['unit-type'])
    ) {
        $title = $_POST['recipe-title'];
        $desc = $_POST['recipe-description'];
        $serving = $_POST['recipe-serving'];
        $instructions = $_POST['recipe-instructions'];
        $tags = $_POST['tag-Input']; 
        $ingredients = $_POST['recipe-ingredients'];
        $measurements = $_POST['ingredient-measurement'];
        $units = $_POST['unit-type']; 

        // insert to recipe table
        $stmt = $conn->prepare("INSERT INTO recipe (recipeTitle, recipeDescription, peoplePerServing) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $desc, $serving);

        if ($stmt->execute()) {
            $recipeID = $conn->insert_id;

            $steps = explode("\n", $instructions);
            $stepNum = 1;
            // steps separated through new lines 
            foreach ($steps as $step) {
                $step = trim($step);
                if ($step !== "") {
                    $stmt2 = $conn->prepare("INSERT INTO instruction (recipeID, instructionNumber, instructionDetails) VALUES (?, ?, ?)");
                    $stmt2->bind_param("iis", $recipeID, $stepNum, $step);
                    $stmt2->execute();
                    $stmt2->close();
                    $stepNum++;
                }
            }

            //inserting ingredients
            foreach ($ingredients as $i => $ingredientName) {
                $ingredientName = trim($ingredientName);
                if ($ingredientName === "") continue;

                // Check if ingredient exists
                $stmt3 = $conn->prepare("SELECT ingredientID FROM ingredient WHERE ingredientName = ?");
                $stmt3->bind_param("s", $ingredientName);
                $stmt3->execute();
                $stmt3->bind_result($ingredientID);
                if ($stmt3->fetch()) {
                    $stmt3->close();
                } else {
                    $stmt3->close();
                    $stmt4 = $conn->prepare("INSERT INTO ingredient (ingredientName) VALUES (?)");
                    $stmt4->bind_param("s", $ingredientName);
                    $stmt4->execute();
                    $ingredientID = $stmt4->insert_id;
                    $stmt4->close();
                }

                $measurement = $measurements[$i];
                $unitName = $units[$i];

                // may unit id pa ba? 
                $stmt6 = $conn->prepare("INSERT INTO contains (recipeID, ingredientID, measurementValue, unitID, ingredientDescription) VALUES (?, ?, ?, ?, '')");
                $stmt6->bind_param("iidi", $recipeID, $ingredientID, $measurement, $unitID);
                $stmt6->execute();
                $stmt6->close();
            }

            // tag separated by commas 
            $tagArr = array_map('trim', explode(',', $tags));
            foreach ($tagArr as $tagTitle) {
                if ($tagTitle === "") continue;

                // check if tag exists
                $stmt7 = $conn->prepare("SELECT tagID FROM tag WHERE tagTitle = ?");
                $stmt7->bind_param("s", $tagTitle);
                $stmt7->execute();
                $stmt7->bind_result($tagID);
                if ($stmt7->fetch()) {
                    $stmt7->close();
                } else {
                    $stmt7->close();
                    $stmt8 = $conn->prepare("INSERT INTO tag (tagTitle) VALUES (?)");
                    $stmt8->bind_param("s", $tagTitle);
                    $stmt8->execute();
                    $tagID = $stmt8->insert_id;
                    $stmt8->close();
                }
                $stmt9 = $conn->prepare("INSERT INTO tags (recipeID, tagID) VALUES (?, ?)");
                $stmt9->bind_param("ii", $recipeID, $tagID);
                $stmt9->execute();
                $stmt9->close();
            }

            header("Location: homepage.php");
            exit();
        } else {
            echo "Error adding recipe: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Missing required fields.";
    }
    $conn->close();
}
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
        <form class="post-container" id="post-recipe-form" method="POST" action="posting.php" enctype="multipart/form-data">
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
                <input type="number" id="recipe-serving" name="recipe-serving" placeholder="Integer" required>
            </div>
            <div class="ingredients-field">
                <p>Ingredients</p>
                <div class="ingredient-row">
                    <div class="input-wrapper">
                        <input type="text" id="recipe-ingredients" name="recipe-ingredients[]" placeholder="Ingredient Name" required>
                        <i class="fa-solid fa-plus" onclick="addIngredientRow(this)"></i>
                        <i class="fa-solid fa-xmark" onclick="removeIngredientRow(this)"></i>
                    </div>
                    <input type="text" id="ingredient-measurement" name="ingredient-measurement[]" placeholder="Measure" required>
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
                </div>
            </div>
            <div class="instructions-field">
                <p>Instructions</p>
                <p style="font-size:1rem">Write your step-by-step instructions here.</p>
                <textarea id="recipe-instructions" name="recipe-instructions" placeholder="Step-by-step instructions" required></textarea>
            </div>
            <div class="posting-tags-field">
                <p>Tags</p>
                <input type="text" id="tag-Input" name="tag-Input" placeholder="Insert tag..." required>
            </div>
            <button class="post-recipe-button" type="submit">Post recipe</button>
        </form>
        <script src="posting.js"></script>
    </body>
</html>