<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $conn = new mysqli("localhost", "root", "", "platemate");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = trim($_GET["username"]);
    $password = $_GET["password"];
    // echo $username;
    // echo $password;
    $stmt = $conn->prepare("SELECT userID, passwordHash FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['passwordHash'])) {
            $_SESSION["userID"] = $user['userID'];
            $_SESSION["username"] = $username;
            header("Location: homepage.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Failed</title>
</head>
<body>
    <h2>Login Failed</h2>
    <p style="color:red;"><?= $error ?? "Unknown error." ?></p>
    <a href="landing.php">Try Again</a>
</body>
</html>
