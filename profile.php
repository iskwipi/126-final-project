<?php
session_start();
$conn = new mysqli("localhost", "root", "", "platemate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentUserID = $_SESSION['userID'] ?? null;
$profileID = isset($_GET['id']) ? intval($_GET['id']) : $currentUserID;
$isOwnProfile = ($currentUserID == $profileID);

// fetch profile user details
$stmt = $conn->prepare("SELECT username, profile_picture FROM user WHERE userID = ?");
$stmt->bind_param("i", $profileID);
$stmt->execute();
$result = $stmt->get_result();
$profileUser = $result->fetch_assoc();
$stmt->close();

$profilePic = !empty($profileUser['profile_picture']) ? 'uploads/profile_pictures/' . $profileUser['profile_picture'] : 'uploads/profile_pictures/default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile page</title>
    <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
</head>
<body id="profile-body">
    <div class="title-bar">
        <img src="logo-white.png">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a user or a recipe..." onkeypress="handleSearch(event)">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>

    <div class="left-panel">
        <a href="homepage.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i> Recipe Search</a>
        <a href="cookbook.php"><i class="fa-solid fa-book"></i> Cookbook</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
    </div>

    <div class="profile">
        <div class="profile-header">
            <img id="profile-picture" src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture">
            <?php if ($isOwnProfile): ?>
                <button onclick="window.location.href='editprofile.php'" class="edit-profile-btn" title="Edit Profile">
                    <i class="fa-solid fa-pen-to-square edit-profile-icon"></i>
                </button>
            <?php endif; ?>
            <?php
                $_GET['id'] = $profileID;
                include 'getProfile.php';
            ?>
        </div>
    </div>

    <div class="new-post-container">
        <?php if ($isOwnProfile): ?>
            <div class="new-post-body">
                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture">
                <a href="posting.php">
                    <button type="button"> 
                        <input type="text" placeholder="New recipe idea?">
                    </button>
                </a>
            </div>
        <?php endif; ?>

        <div class="mode-section">
            <label for="display-mode">Display Mode: </label>
            <select id="display-mode">
                <option value="id">Most Recent</option>
                <option value="rating">Top Rated</option>
            </select>
        </div>
    </div>

    <div class="profile-featured-section" id="profile-featured-section"></div>

    <script>
        const profileID = <?php echo json_encode($profileID); ?>;
        const isOwnProfile = <?php echo json_encode($isOwnProfile); ?>;
        const currentUserId = <?php echo json_encode($currentUserID); ?>;
    </script>
    <script src="renderOwned.js"></script>
    <script src="searchHandler.js"></script>
</body>
</html>

<?php $conn->close(); ?>
