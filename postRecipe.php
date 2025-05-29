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
        $picture = $_POST['picture-link']; 
        $userID = $_SESSION['userID'];

        // insert to recipe table
        $stmt = $conn->prepare("INSERT INTO recipe (recipeTitle, recipeDescription, peoplePerServing) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $desc, $serving);

        if ($stmt->execute()) {
            $recipeID = $conn->insert_id;

            $stmt10 = $conn->prepare("INSERT INTO owns (userID, recipeID) VALUES (?, ?)");
            $stmt10->bind_param("ii", $userID, $recipeID);
            $stmt10->execute();
            $stmt10->close();

            $picture = trim($picture);
            $pictureNum = 1;
            $stmt10 = $conn->prepare("INSERT INTO picture (recipeID, pictureLink) VALUES (?, ?)");
            $stmt10->bind_param("is", $recipeID, $picture);
            $stmt10->execute();
            $stmt10->close();

            // $steps = explode("\n", $instructions);
            $stepNum = 1;
            // steps separated through new lines 
            foreach ($instructions as $step) {
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
                $measurement = $measurements[$i];
                $unitName = $units[$i];
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

                // Check if unit exists
                $stmt3 = $conn->prepare("SELECT unitID FROM unit WHERE unitName = ?");
                $stmt3->bind_param("s", $unitName);
                $stmt3->execute();
                $stmt3->bind_result($unitID);
                if ($stmt3->fetch()) {
                    $stmt3->close();
                } else {
                    $stmt3->close();
                    $stmt4 = $conn->prepare("INSERT INTO unit (unitName) VALUES (?)");
                    $stmt4->bind_param("s", $unitName);
                    $stmt4->execute();
                    $unitID = $stmt4->insert_id;
                    $stmt4->close();
                }

                // may unit id pa ba? 
                $stmt6 = $conn->prepare("INSERT INTO contains (recipeID, ingredientID, measurementValue, unitID) VALUES (?, ?, ?, ?)");
                $stmt6->bind_param("iidi", $recipeID, $ingredientID, $measurement, $unitID);
                $stmt6->execute();
                $stmt6->close();
            }

            // tag separated by commas 
            // $tagArr = array_map('trim', explode(',', $tags));
            echo implode($tags);
            echo implode($ingredients);
            foreach ($tags as $tagTitle) {
                $tagTitle = trim($tagTitle);
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