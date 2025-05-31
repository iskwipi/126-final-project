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

// verify ownership
$stmt = $conn->prepare("SELECT * FROM owns WHERE recipeID = ? AND userID = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows == 0) {
    $_SESSION['error'] = "You do not have permission to edit this recipe.";
    header("Location: profile.php");
    exit();
}
$stmt->close();

// fetch recipe details
$stmt = $conn->prepare("SELECT * FROM recipe WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

// fetch ingredients
$ingredients = [];
$stmt = $conn->prepare("SELECT c.*, i.ingredientName, u.unitName FROM contains c 
                        JOIN ingredient i ON c.ingredientID = i.ingredientID 
                        LEFT JOIN unit u ON c.unitID = u.unitID 
                        WHERE c.recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// fetch instructions
$instructions = [];
$stmt = $conn->prepare("SELECT * FROM instruction WHERE recipeID = ? ORDER BY instructionNumber");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$instructions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// fetch pictures
$pictures = [];
$stmt = $conn->prepare("SELECT * FROM picture WHERE recipeID = ? ORDER BY pictureNumber");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$pictures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// fetch tags
$tags = [];
$stmt = $conn->prepare("SELECT t.tagTitle FROM tags ts JOIN tag t ON ts.tagID = t.tagID WHERE ts.recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$tags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$tag_string = implode(", ", array_column($tags, 'tagTitle'));
$stmt->close();

// Fetch all units for dropdowns
$all_units = $conn->query("SELECT * FROM unit")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'recipe-title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'recipe-description', FILTER_SANITIZE_STRING);
    $servings = (int)$_POST['recipe-serving'];

    if (empty($title) || empty($description) || $servings < 1) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }

    // update recipe
    $stmt = $conn->prepare("UPDATE recipe SET recipeTitle = ?, recipeDescription = ?, peoplePerServing = ? WHERE recipeID = ?");
    $stmt->bind_param("ssii", $title, $description, $servings, $recipe_id);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error updating recipe: " . $stmt->error;
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }
    $stmt->close();

    // checks if record exists
    $stmt = $conn->prepare("SELECT * FROM owns WHERE recipeID = ? AND userID = ?");
    $stmt->bind_param("ii", $recipe_id, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO owns (recipeID, userID) VALUES (?, ?)");
        $stmt->bind_param("ii", $recipe_id, $user_id);
        if (!$stmt->execute()) {
            $_SESSION['error'] = "Error re-inserting owns record: " . $stmt->error;
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }
    }
    $stmt->close();

    // update ingredients
    $stmt = $conn->prepare("DELETE FROM contains WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error deleting ingredients: " . $stmt->error;
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }
    $stmt->close();

    if (isset($_POST['recipe-ingredients']) && isset($_POST['ingredient-measurement']) && isset($_POST['unit-type'])) {
        $stmt = $conn->prepare("SELECT ingredientID FROM ingredient WHERE ingredientName = ?");
        $stmt_insert = $conn->prepare("INSERT INTO ingredient (ingredientName) VALUES (?)");
        $stmt_insert_contains = $conn->prepare("INSERT INTO contains (recipeID, ingredientID, measurementValue, unitID, ingredientDescription) VALUES (?, ?, ?, ?, ?)");
        foreach ($_POST['recipe-ingredients'] as $index => $ing_name) {
            $ingredient_name = filter_var($ing_name, FILTER_SANITIZE_STRING);
            if (empty($ingredient_name)) continue;
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $ingredient_id = $result->fetch_assoc()['ingredientID'];
            } else {
                $stmt_insert->bind_param("s", $ingredient_name);
                if (!$stmt_insert->execute()) {
                    $_SESSION['error'] = "Error inserting ingredient: " . $stmt_insert->error;
                    header("Location: editrecipe.php?recipe_id=$recipe_id");
                    exit();
                }
                $ingredient_id = $conn->insert_id;
            }

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

            $measurement = (float)$_POST['ingredient-measurement'][$index];
            $desc = "";
            $stmt_insert_contains->bind_param("iidis", $recipe_id, $ingredient_id, $measurement, $unit_id, $desc);
            if (!$stmt_insert_contains->execute()) {
                $_SESSION['error'] = "Error inserting contains record: " . $stmt_insert_contains->error;
                header("Location: editrecipe.php?recipe_id=$recipe_id");
                exit();
            }
        }
        $stmt->close();
        $stmt_insert->close();
        $stmt_insert_contains->close();
    }

    // update instructions
    $stmt = $conn->prepare("DELETE FROM instruction WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error deleting instructions: " . $stmt->error;
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }
    $stmt->close();

    if (isset($_POST['recipe-instructions']) && !empty($_POST['recipe-instructions'])) {
        $stmt = $conn->prepare("INSERT INTO instruction (recipeID, instructionNumber, instructionDetails) VALUES (?, ?, ?)");
        $validInstructions = false;
        foreach ($_POST['recipe-instructions'] as $index => $instr) {
            $instruction_number = $index + 1;
            $details = filter_var($instr, FILTER_SANITIZE_STRING);
            if (empty($details)) {
                continue; // Skip empty instructions
            }
            $validInstructions = true;
            $stmt->bind_param("iis", $recipe_id, $instruction_number, $details);
            if (!$stmt->execute()) {
                $_SESSION['error'] = "Error inserting instruction: " . $stmt->error;
                header("Location: editrecipe.php?recipe_id=$recipe_id");
                exit();
            }
        }
        $stmt->close();
        if (!$validInstructions) {
            $_SESSION['error'] = "At least one non-empty instruction is required.";
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }
    } else {
        $_SESSION['error'] = "At least one instruction is required.";
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }

    // update pictures (only delete if new upload)
    if (isset($_FILES['recipe-image']) && $_FILES['recipe-image']['error'] === UPLOAD_ERR_OK) {
        $stmt = $conn->prepare("DELETE FROM picture WHERE recipeID = ?");
        $stmt->bind_param("i", $recipe_id);
        if (!$stmt->execute()) {
            $_SESSION['error'] = "Error deleting pictures: " . $stmt->error;
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }
        $stmt->close();

        $upload_dir = 'Uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_tmp_path = $_FILES['recipe-image']['tmp_name'];
        $file_name = basename($_FILES['recipe-image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }
        $new_file_name = uniqid('img_') . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;
        if (move_uploaded_file($file_tmp_path, $destination)) {
            $stmt = $conn->prepare("INSERT INTO picture (recipeID, pictureNumber, pictureLink) VALUES (?, ?, ?)");
            $picture_number = 1;
            $stmt->bind_param("iis", $recipe_id, $picture_number, $destination);
            if (!$stmt->execute()) {
                $_SESSION['error'] = "Error inserting picture: " . $stmt->error;
                header("Location: editrecipe.php?recipe_id=$recipe_id");
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error uploading the file.";
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }
    }

    // update tags
    $stmt = $conn->prepare("DELETE FROM tags WHERE recipeID = ?");
    $stmt->bind_param("i", $recipe_id);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error deleting tags: " . $stmt->error;
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }
    $stmt->close();

    if (isset($_POST['tag-Input']) && !empty($_POST['tag-Input'])) {
        $tag_list = [];
        foreach ($_POST['tag-Input'] as $tag_input) {
            $sanitized_input = filter_var($tag_input, FILTER_SANITIZE_STRING);
            if (!empty($sanitized_input)) {
                $tag_list[] = $sanitized_input;
            }
        }
        if (empty($tag_list)) {
            $_SESSION['error'] = "At least one non-empty tag is required.";
            header("Location: editrecipe.php?recipe_id=$recipe_id");
            exit();
        }

        $stmt = $conn->prepare("SELECT tagID FROM tag WHERE tagTitle = ?");
        $stmt_insert = $conn->prepare("INSERT INTO tag (tagTitle) VALUES (?)");
        $stmt_insert_tag = $conn->prepare("INSERT INTO tags (recipeID, tagID) VALUES (?, ?)");
        foreach ($tag_list as $tag_title) {
            $stmt->bind_param("s", $tag_title);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $tag_id = $result->fetch_assoc()['tagID'];
            } else {
                $stmt_insert->bind_param("s", $tag_title);
                if (!$stmt_insert->execute()) {
                    $_SESSION['error'] = "Error inserting tag: " . $stmt_insert->error;
                    header("Location: editrecipe.php?recipe_id=$recipe_id");
                    exit();
                }
                $tag_id = $conn->insert_id;
            }
            $stmt_insert_tag->bind_param("ii", $recipe_id, $tag_id);
            if (!$stmt_insert_tag->execute()) {
                $_SESSION['error'] = "Error inserting tags record: " . $stmt_insert_tag->error;
                header("Location: editrecipe.php?recipe_id=$recipe_id");
                exit();
            }
        }
        $stmt->close();
        $stmt_insert->close();
        $stmt_insert_tag->close();
    } else {
        $_SESSION['error'] = "At least one tag is required.";
        header("Location: editrecipe.php?recipe_id=$recipe_id");
        exit();
    }

    $_SESSION['success'] = "Recipe updated successfully.";
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
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data">
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
                        <select id="unit-type" name="unit-type[]">
                            <option value="">Unit:</option>
                            <?php foreach ($all_units as $unit): ?>
                                <option value="<?php echo htmlspecialchars($unit['unitName']); ?>" <?php echo $ing['unitName'] == $unit['unitName'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($unit['unitName']); ?></option>
                            <?php endforeach; ?>
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
            <?php if (!empty($pictures)): ?>
                <img src="<?php echo htmlspecialchars($pictures[0]['pictureLink']); ?>" alt="Recipe Image" style="max-width: 200px;"><br>
            <?php endif; ?>
            <input type="file" name="recipe-image" accept="image/*"><br>
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