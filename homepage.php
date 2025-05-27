<?php
session_start();
echo implode($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Platemate Homepage </title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body>
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
        <div class="feed">
            <div class="featured-section" id="featured-section">
                <div class="section-title"><h1>Today's Featured Recipes</h1></div>
                <div id="section">
                    <button class="nav-button left-button">
                    <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="featured-posts" id="featured-posts"></div>
                    <button class="nav-button right-button">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                
            </div>
            <div class="homepage-container">
                <!-- <i class="fa-solid fa-user"></i> -->
                <div class="upload-section">
                    <a href="posting.php"> 
                        <button type="button">
                        <input type="text" id="uploadRecipe" placeholder="New recipe idea?                   ">
                        <i class="fa-regular fa-image"></i> </button>
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
            <div class="posts-section" id="posts-section"></div>
        <script src="renderFeed.js"></script>
        <script src="searchHandler.js"></script>
    </body>
</html>