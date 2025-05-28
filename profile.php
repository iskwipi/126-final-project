<?php
session_start();
echo implode($_SESSION);
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
        </div>
        <div class="left-panel">
            <a href="homepage.php"><i class="fa-solid fa-house"></i> Home</a>
            <a href="deepsearch.php"><i class="fa-solid fa-magnifying-glass"></i>  Deep Search</a>
            <a href="cookbook.php"><i class="fa-solid fa-book"></i>  Cookbook</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i>  Profile</a>
        </div>
        <div class = "profile">
            <div class = "profile-header">
                <img id = "profile-picture" src="papaneyro.jpg">
                <?php include 'getProfile.php' ?>
            </div>
        </div>
        <div class = "new-post-container">
            <div class = "new-post-body">
                <img src="papaneyro.jpg">
                <a href="posting.php">
                    <button type="button"> 
                    <input type="text" placeholder="New recipe idea?">
                    </button>
                </a>
                <div class="images-button">
                    <a href="posting.php">
                    <button type="button">
                        <i class="fa-regular fa-image"></i> 
                    </button>
                    </a>
                </div>
                <div class="mode-section">
                    <label for="display-mode">Display Mode: </label>
                    <select id="display-mode">
                        <option value="id">Most Recent</option>
                        <option value="rating">Top Rated</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="profile-featured-section" id="profile-featured-section"></div>
        <script>
            const profileID = <?php echo json_encode($_GET["id"]); ?>;
            console.log(profileID);
        </script>
        <script src="renderOwned.js"></script>
        <script src="searchHandler.js"></script>
    </body>
<html>