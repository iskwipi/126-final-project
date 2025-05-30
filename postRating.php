<?php
session_start();
// echo implode($_SESSION);
$conn = new mysqli("localhost", "root", "", "platemate");
$recipeID = $_POST["recipeID"];
$userID = $_SESSION["userID"];
$rating = $_POST["rating-input"];
$comment = $_POST["comment-input"];
$stmt = $conn->prepare("INSERT INTO rates (userID, recipeID, rating) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $userID, $recipeID, $rating);
$stmt->execute();
$stmt->close();
$stmtd = $conn->prepare("INSERT INTO comments (userID, recipeID, comment) VALUES (?, ?, ?)");
$stmtd->bind_param("iis", $userID, $recipeID, $comment);
$stmtd->execute();
$stmtd->close();
$conn->close();
header("Location: homepage.php");
?>