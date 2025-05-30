<?php
session_start();
$currentUserID = $_SESSION['userID'] ?? null;
$profileID = isset($_GET['id']) ? intval($_GET['id']) : $currentUserID;
$isOwnProfile = ($currentUserID == $profileID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Profile page</title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body id="profile-body">
        <div class="title-bar">
            <img src="logo-white.png">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search for a user or a recipe..."  onkeypress="handleSearch(event)">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="left-panel">
            <a href="homepage.php"><i class="fa-solid fa-house"></i> Home</a>
            <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i>  Recipe Search</a>
            <a href="cookbook.php"><i class="fa-solid fa-book"></i>  Cookbook</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i>  Profile</a>
        </div>
        <div class="profile">
            <div class="profile-header">
                <img id="profile-picture" src="papaneyro.jpg">
                <?php
                    $_GET['id'] = $profileID;
                    include 'getProfile.php';
                ?>
            </div>
        </div>
        <div class="new-post-container">
            <?php if ($isOwnProfile): ?>
                <div class="new-post-body">
                    <img src="papaneyro.jpg">
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

        </script>
        <script src="renderOwned.js"></script>
        <script src="searchHandler.js"></script>
    </body>
</html>