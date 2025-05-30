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

$user_id = $_SESSION['userID'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM user WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch provinces and cities for dropdowns
$provinces = $conn->query("SELECT * FROM province")->fetch_all(MYSQLI_ASSOC);
$cities = $conn->query("SELECT * FROM city")->fetch_all(MYSQLI_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $province_id = !empty($_POST['province']) ? (int)$_POST['province'] : null;
    $city_id = !empty($_POST['city']) ? (int)$_POST['city'] : null;

    // Validate inputs
    if (empty($username) || empty($email) || empty($occupation) || empty($dob)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update user
        $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, occupation = ?, dateOfBirth = ?, provinceID = ?, cityID = ? WHERE userID = ?");
        $stmt->bind_param("ssssiii", $username, $email, $occupation, $dob, $province_id, $city_id, $user_id);
        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            $error = "Error updating profile.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - PlateMate</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Profile</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
        <label>Occupation: <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation']); ?>" required></label><br>
        <label>Date of Birth: <input type="date" name="dob" value="<?php echo $user['dateOfBirth']; ?>" required></label><br>
        <label>Province:
            <select name="province">
                <option value="">Select Province</option>
                <?php foreach ($provinces as $p): ?>
                    <option value="<?php echo $p['provinceID']; ?>" <?php echo $p['provinceID'] == $user['provinceID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['provinceName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>City:
            <select name="city">
                <option value="">Select City</option>
                <?php foreach ($cities as $c): ?>
                    <option value="<?php echo $c['cityID']; ?>" <?php echo $c['cityID'] == $user['cityID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['cityName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Save Changes</button>
        <a href="profile.php">Cancel</a>
    </form>
</body>
</html>
<?php $conn->close(); ?>