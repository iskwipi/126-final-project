<?php
$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Add password column if it doesn't exist
// $conn->query("ALTER TABLE user ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL");

// Step 2: Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'], $_POST['password'])) {
    $user_id = (int)$_POST['user_id'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE user SET password_hash = ? WHERE userID = ?");
    $stmt->bind_param("si", $hash, $user_id);
    $stmt->execute();

    echo "<p style='color:green;'>Password set for user ID: $user_id</p>";
}

// Step 3: Get users without passwords
$result = $conn->query("SELECT userID, username FROM user WHERE password_hash IS NULL");

if ($result->num_rows > 0) {
    echo "<h2>Assign Passwords to Users</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "
        <form method='post'>
            <strong>{$row['username']}</strong> (ID: {$row['userID']})<br>
            <input type='hidden' name='user_id' value='{$row['userID']}'>
            <input type='password' name='password' placeholder='New Password' required>
            <button type='submit'>Set Password</button>
        </form><br>
        ";
    }
} else {
    echo "<p>All users already have passwords assigned.</p>";
}

$conn->close();
?>
