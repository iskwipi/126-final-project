<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$recipe_id = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;
$user_id = $_SESSION['userID'];

// Verify ownership
$stmt = $conn->prepare("SELECT * FROM owns WHERE recipeID = ? AND userID = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows == 0) {
    die("You do not have permission to edit this recipe.");
}
$stmt->close();

// Fetch recipe details
$stmt = $conn->prepare("SELECT * FROM recipe WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch ingredients
$ingredients = [];
$stmt = $conn->prepare("SELECT c.*, i.ingredientName, u.unitName FROM contains c 
                        JOIN ingredient i ON c.ingredientID = i.ingredientID 
                        LEFT JOIN unit u ON c.unitID = u.unitID 
                        WHERE c.recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch instructions
$instructions = [];
$stmt = $conn->prepare("SELECT * FROM instruction WHERE recipeID = ? ORDER BY instructionNumber");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$instructions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch pictures
$pictures = [];
$stmt = $conn->prepare("SELECT * FROM picture WHERE recipeID = ? ORDER BY pictureNumber");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$pictures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch tags
$tags = [];
$stmt = $conn->prepare("SELECT t.tagTitle FROM tags ts JOIN tag t ON ts.tagID = t.tagID WHERE ts.recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$tags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$tag_string = implode(", ", array_column($tags, 'tagTitle')); // Convert tags to a comma-separated string
$stmt->close();

// Fetch all units for dropdowns (to match posting.php)
$all_units = $conn->query("SELECT * FROM unit")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $title = filter_input(INPUT_POST, 'recipe-title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'recipe-description', FILTER_SANITIZE_STRING);
    $servings = (int)$_POST['recipe-serving'];

    // Update recipe
    $stmt = $conn->prepare("UPDATE recipe SET recipeTitle = ?, recipeDescription = ?, peoplePerServing = ? WHERE recipeID = ?");
    $stmt->bind_param("ssii", $title, $description, $servings, $recipe_id);
    $stmt->execute();
    $stmt->close();

    // Update ingredients (delete existing and insert new)
    $stmt = $conn->prepare("DELETE FROM contains WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['recipe-ingredients']) && isset($_POST['ingredient-measurement']) && isset($_POST['unit-type'])) {
        $stmt = $conn->prepare("SELECT ingredientID FROM ingredient WHERE ingredientName = ?");
        $stmt_insert = $conn->prepare("INSERT INTO ingredient (ingredientName) VALUES (?)");
        $stmt_insert_contains = $conn->prepare("INSERT INTO contains (recipeID, ingredientID, measurementValue, unitID, ingredientDescription) VALUES (?, ?, ?, ?, ?)");
        foreach ($_POST['recipe-ingredients'] as $index => $ing_name) {
            // Check if ingredient exists, if not create it
            $ingredient_name = filter_var($ing_name, FILTER_SANITIZE_STRING);
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $ingredient_id = $result->fetch_assoc()['ingredientID'];
            } else {
                $stmt_insert->bind_param("s", $ingredient_name);
                $stmt_insert->execute();
                $ingredient_id = $conn->insert_id;
            }

            // Find unitID by unitName
            $unit_name = filter_var($_POST['unit-type'][$index], FILTER_SANITIZE_STRING);
            $unit_id = null;
            if (!empty($unit_name)) {
                $stmt_unit = $conn->prepare("SELECT unitID FROM unit WHERE unitName = ?");
                $stmt_unit->bind_param("s", $unit_name);
                $stmt_unit->execute();
                $result = $stmt_unit->get_result();
                if ($result->num_rows > 0) {
                    $unit_id = $result->fetch_assoc()['unitID'];
                }
                $stmt_unit->close();
            }

            // Insert into contains (ingredientDescription is optional, set to empty string)
            $measurement = (float)$_POST['ingredient-measurement'][$index];
            $desc = ""; // posting.php doesn't have ingredientDescription, so we set it to empty
            $stmt_insert_contains->bind_param("iidis", $recipe_id, $ingredient_id, $measurement, $unit_id, $desc);
            $stmt_insert_contains->execute();
        }
        $stmt->close();
        $stmt_insert->close();
        $stmt_insert_contains->close();
    }

    // Update instructions (delete existing and insert new)
    $stmt = $conn->prepare("DELETE FROM instruction WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['recipe-instructions'])) {
        $stmt = $conn->prepare("INSERT INTO instruction (recipeID, instructionNumber, instructionDetails) VALUES (?, ?, ?)");
        foreach ($_POST['recipe-instructions'] as $index => $instr) {
            $instruction_number = $index + 1;
            // Sanitize the instruction input directly
            $details = filter_var($instr, FILTER_SANITIZE_STRING);
            if ($details === null || $details === "") {
                $details = ""; // Default to empty string if null or empty
            }
            $stmt->bind_param("iis", $recipe_id, $instruction_number, $details);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Update pictures (delete existing and insert new)
    $stmt = $conn->prepare("DELETE FROM picture WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['picture-link'])) {
        $stmt = $conn->prepare("INSERT INTO picture (recipeID, pictureNumber, pictureLink) VALUES (?, ?, ?)");
        $picture_number = 1; // posting.php allows only one picture
        $link = filter_input(INPUT_POST, 'picture-link', FILTER_SANITIZE_URL);
        $stmt->bind_param("iis", $recipe_id, $picture_number, $link);
        $stmt->execute();
        $stmt->close();
    }

    // Update tags (delete existing and insert new)
    $stmt = $conn->prepare("DELETE FROM tags WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->close();

    if (isset($_POST['tag-Input'])) {
        $tag_list = [];
        foreach ($_POST['tag-Input'] as $tag_input) {
            $sanitized_input = filter_var($tag_input, FILTER_SANITIZE_STRING);
            $tags = array_map('trim', explode(',', $sanitized_input));
            $tag_list = array_merge($tag_list, array_filter($tags));
        }

        $stmt = $conn->prepare("SELECT tagID FROM tag WHERE tagTitle = ?");
        $stmt_insert = $conn->prepare("INSERT INTO tag (tagTitle) VALUES (?)");
        $stmt_insert_tag = $conn->prepare("INSERT INTO tags (recipeID, tagID) VALUES (?, ?)");
        foreach ($tag_list as $tag_title) {
            if (empty($tag_title)) continue;
            $stmt->bind_param("s", $tag_title);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $tag_id = $result->fetch_assoc()['tagID'];
            } else {
                $stmt_insert->bind_param("s", $tag_title);
                $stmt_insert->execute();
                $tag_id = $conn->insert_id;
            }
            $stmt_insert_tag->bind_param("ii", $recipe_id, $tag_id);
            $stmt_insert_tag->execute();
        }
        $stmt->close();
        $stmt_insert->close();
        $stmt_insert_tag->close();
    }

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe - PlateMate</title>
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
    <form class="post-container" id="edit-recipe-form" method="POST">
        <div class="title-field">
            <p>Title</p>
            <input type="text" id="recipe-title" name="recipe-title" value="<?php echo htmlspecialchars($recipe['recipeTitle']); ?>" required>
        </div>
        <div class="description-field">
            <p>Description</p>
            <textarea id="recipe-description" name="recipe-description" required><?php echo htmlspecialchars($recipe['recipeDescription']); ?></textarea>
        </div>
        <div class="serving-field">
            <p>People per serving</p>
            <input type="number" id="recipe-serving" name="recipe-serving" value="<?php echo htmlspecialchars($recipe['peoplePerServing']); ?>" min="1" required>
        </div>
        <div class="ingredients-field">
            <p>Ingredients</p>
            <div id="ingredients">
                <?php foreach ($ingredients as $index => $ing): ?>
                    <div class="ingredient-row">
                        <input type="number" id="ingredient-measurement" name="ingredient-measurement[]" value="<?php echo htmlspecialchars($ing['measurementValue']); ?>" required>
                        <select id="unit-type" name="unit-type[]" required>
                            <option value="">Unit:</option>
                            <option value="teaspoon" <?php echo $ing['unitName'] == 'teaspoon' ? 'selected' : ''; ?>>Teaspoon (tsp)</option>
                            <option value="tablespoon" <?php echo $ing['unitName'] == 'tablespoon' ? 'selected' : ''; ?>>Tablespoon (tbsp)</option>
                            <option value="cup" <?php echo $ing['unitName'] == 'cup' ? 'selected' : ''; ?>>Cup</option>
                            <option value="milliliter" <?php echo $ing['unitName'] == 'milliliter' ? 'selected' : ''; ?>>Milliliter (ml)</option>
                            <option value="liter" <?php echo $ing['unitName'] == 'liter' ? 'selected' : ''; ?>>Liter (l)</option>
                            <option value="gram" <?php echo $ing['unitName'] == 'gram' ? 'selected' : ''; ?>>Gram (g)</option>
                            <option value="kilogram" <?php echo $ing['unitName'] == 'kilogram' ? 'selected' : ''; ?>>Kilogram (kg)</option>
                            <option value="ounce" <?php echo $ing['unitName'] == 'ounce' ? 'selected' : ''; ?>>Ounce (oz)</option>
                            <option value="pound" <?php echo $ing['unitName'] == 'pound' ? 'selected' : ''; ?>>Pound (lb)</option>
                            <option value="pinch" <?php echo $ing['unitName'] == 'pinch' ? 'selected' : ''; ?>>Pinch</option>
                            <option value="dash" <?php echo $ing['unitName'] == 'dash' ? 'selected' : ''; ?>>Dash</option>
                            <option value="piece" <?php echo $ing['unitName'] == 'piece' ? 'selected' : ''; ?>>Piece</option>
                        </select>
                        <div class="input-wrapper">
                            <input type="text" id="recipe-ingredients" name="recipe-ingredients[]" value="<?php echo htmlspecialchars($ing['ingredientName']); ?>" required>
                            <i class="fa-solid fa-plus" onclick="addIngredientRow(this)"></i>
                            <i class="fa-regular fa-circle-xmark" onclick="removeIngredientRow(this)"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="instructions-field">
            <p>Instructions</p>
            <div id="instructions">
                <?php foreach ($instructions as $index => $instr): ?>
                    <div class="input-wrapper">
                        <textarea id="recipe-instructions" name="recipe-instructions[]" required><?php echo htmlspecialchars($instr['instructionDetails']); ?></textarea>
                        <i class="fa-solid fa-plus" onclick="addInstructionRow(this)"></i>
                        <i class="fa-regular fa-circle-xmark" onclick="removeInstructionRow(this)"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="posting-tags-field">
            <p>Tags</p>
            <div id="tags">
                <?php foreach ($tags as $index => $tag): ?>
                    <div class="input-wrapper">
                        <input type="text" id="tag-Input" name="tag-Input[]" value="<?php echo htmlspecialchars($tag['tagTitle']); ?>" required>
                        <i class="fa-solid fa-plus" onclick="addTagRow(this)"></i>
                        <i class="fa-regular fa-circle-xmark" onclick="removeTagRow(this)"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="picture-field">
            <p>Picture</p>
            <input type="text" id="picture-link" name="picture-link" value="<?php echo !empty($pictures) ? htmlspecialchars($pictures[0]['pictureLink']) : ''; ?>" required>
        </div>
        <div class="submit-cancel">
            <button class="post-recipe-button" type="submit">Save Changes</button>
            <a href="profile.php">Back to profile</a>
        </div>
    </form>
    <script src="editrecipe.js"></script>
</body>
</html>
<?php $conn->close(); ?>