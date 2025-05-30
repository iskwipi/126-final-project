<?php
session_start();
// echo implode($_SESSION);
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Platemate search results </title>
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
        <div class="search-recipe-feed">
            <div class="search-feed-header">
                <h2>Quick search results</h2>
                <div class="category-section">
                    <a> 
                        <button type="button" class="recipe-button">
                            <i class="fa-solid fa-file-lines"></i>
                            Recipes
                        </button>
                    </a>
                    <a> 
                        <button type="button" class="user-button" onclick="switchSearchMode()">
                            <i class="fa-solid fa-user"></i>
                            Users
                        </button>
                    </a>
                </div>
                <h3>
                    <div class="query-title" id="query-title"></div>
                </h3>
                <div class="mode-section">
                    <label for="display-mode">Display Mode: </label>
                    <select id="display-mode">
                        <option value="id">Most Recent</option>
                        <option value="rating">Top Rated</option>
                    </select>
                </div>
            </div>
            <div class="search-posts" id="search-posts"></div>  
        </div>
        <script src="renderSearch.js"></script>
        <script src="searchHandler.js"></script>
        <script src="searchModeSwitcher.js"></script>
    </body>
</html>