<?php
session_start();

$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$provinceName = $_SESSION['province'];
$stmt = $conn->prepare("SELECT provinceID FROM province WHERE provinceName = ?");
$stmt->bind_param("s", $provinceName);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $provinceID = $row['provinceID'];
} else {
    $stmt = $conn->prepare("INSERT INTO province (provinceName) VALUES (?)");
    $stmt->bind_param("s", $provinceName);
    if ($stmt->execute()) {
        $provinceID = $stmt->insert_id;
    } else {
        die("Error inserting province: " . $stmt->error);
    }
}

$cityName = $_SESSION['city'];
$stmt = $conn->prepare("SELECT cityID FROM city WHERE cityName = ?");
$stmt->bind_param("s", $cityName);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cityID = $row['cityID'];
} else {
    $stmt = $conn->prepare("INSERT INTO city (cityName) VALUES (?)");
    $stmt->bind_param("s", $cityName);
    if ($stmt->execute()) {
        $cityID = $stmt->insert_id;
    } else {
        die("Error inserting city: " . $stmt->error);
    }
}

$stmt = $conn->prepare("INSERT INTO user 
    (username, dateOfBirth, occupation, email, provinceID, cityID, passwordHash) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "sssssss",
    $_SESSION['username'],
    $_SESSION['dob'],
    $_SESSION['occupation'],
    $_SESSION['email'],
    $provinceID,
    $cityID,
    $password_hash
);

if ($stmt->execute()) {
    session_destroy();
    header("Location: landing.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
