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

// fetch user details
$stmt = $conn->prepare("SELECT * FROM user WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// fetch provinces and cities
$provinces = $conn->query("SELECT * FROM province")->fetch_all(MYSQLI_ASSOC);
$cities = $conn->query("SELECT * FROM city")->fetch_all(MYSQLI_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $province_id = !empty($_POST['province']) ? (int)$_POST['province'] : null;
    $city_id = !empty($_POST['city']) ? (int)$_POST['city'] : null;

    // handle profile picture
    $profile_picture_filename = $user['profile_picture']; // retain current by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
        $upload_path = "uploads/profile_pictures/" . $new_filename;

        if (!file_exists("uploads/profile_pictures")) {
            mkdir("uploads/profile_pictures", 0777, true);
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $profile_picture_filename = $new_filename;
        }
    }

    // validate inputs
    if (empty($username) || empty($occupation) || empty($dob)) {
        $error = "All fields are required.";
    } else {
        // update user
        $stmt = $conn->prepare("UPDATE user SET profile_picture = ?, username = ?, occupation = ?, dateOfBirth = ?, provinceID = ?, cityID = ? WHERE userID = ?");
        $stmt->bind_param("ssssiii", $profile_picture_filename, $username, $occupation, $dob, $province_id, $city_id, $user_id);
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
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-picture-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="title-bar">
        <img src="logo-white.png" alt="PlateMate Logo">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a user or a recipe..." onkeypress="handleSearch(event)">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>

    <h2>Edit Profile</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div>
            <?php
                $profile_pic_path = !empty($user['profile_picture']) 
                    ? 'uploads/profile_pictures/' . $user['profile_picture']
                    : 'uploads/profile_pictures/default-profile.png';
            ?>
            <img src="<?php echo $profile_pic_path; ?>" class="profile-picture-preview" alt="Profile Picture">
        </div>

        <label>Change Profile Picture:
            <input type="file" name="profile_picture" accept="image/*">
        </label><br>

        <label>Username:
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </label><br>

        <label>Occupation:
            <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation']); ?>" required>
        </label><br>

        <label>Date of Birth:
            <input type="date" name="dob" value="<?php echo $user['dateOfBirth']; ?>" required>
        </label><br>

        <label>Province:
            <select name="province" required>
                <option value="">Select Province</option>
                <?php foreach ($provinces as $p): ?>
                    <option value="<?php echo $p['provinceID']; ?>" <?php echo $p['provinceID'] == $user['provinceID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['provinceName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>City:
            <select name="city" required>
                <option value="">Select City</option>
                <?php foreach ($cities as $c): ?>
                    <option value="<?php echo $c['cityID']; ?>" <?php echo $c['cityID'] == $user['cityID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['cityName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <button class="post-recipe-button" type="submit">Save Changes</button>
        <a href="profile.php">Back to profile</a>
    </form>
</body>
</html>

<?php $conn->close(); ?>
