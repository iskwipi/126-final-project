<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete a recipe.']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$user_id = $_SESSION['userID'];

if ($recipe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID.']);
    exit();
}

// Verify ownership
$stmt = $conn->prepare("SELECT * FROM owns WHERE recipeID = ? AND userID = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows == 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this recipe.']);
    exit();
}
$stmt->close();

// for deleting related data
$stmt = $conn->prepare("DELETE FROM contains WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM instruction WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM picture WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM tags WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM owns WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

// for deleting the recipe
$stmt = $conn->prepare("DELETE FROM recipe WHERE recipeID = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->close();

$conn->close();

echo json_encode(['success' => true, 'message' => 'Recipe deleted successfully']);
exit();
?>