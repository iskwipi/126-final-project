<?php
session_start();
$conn = new mysqli("localhost", "root", "", "platemate");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //reads incoming json and gets userID and recipeID
    $data = json_decode(file_get_contents("php://input"), true);
    $userID = $_SESSION['userID'];
    $recipeID = $data['recipeID'];

    // check if already saved
    $check = $conn->prepare("SELECT * FROM saves WHERE userID = ? AND recipeID = ?");
    $check->bind_param("ii", $userID, $recipeID);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // not saved, so save it
        $stmt = $conn->prepare("INSERT INTO saves (userID, recipeID) VALUES (?, ?)");
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        echo json_encode(["status" => "saved"]);
    } else {
        // already saved, so unsave it
        $stmt = $conn->prepare("DELETE FROM saves WHERE userID = ? AND recipeID = ?");
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        echo json_encode(["status" => "unsaved"]);
    }
}
?>
