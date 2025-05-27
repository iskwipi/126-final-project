<?php
session_start();
echo implode($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Platemate - Log In or Sign Up</title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <body id="overflow-hidden">
        <div class="background-design-landing-signuppage">
                <div id="landing-rectangle1"></div>
                <div id="landing-rectangle2"></div>
        </div>

        <div id="landingpage" class="landing-signuppage-container">
            <div class="website-name">
                <div id="logo"><img src="logo-white.png"></div>
                <p>latemate</p>
            </div>
            <div class="login-tab">
                <p id="slogan">A taste of everyone’s table.</p>
                <div class="login-tab-container">
                    <div class="login-tab-contents">
                        <form action="login.php" method="post">
                            <p id="join-today">Join today!</p>
                            <p id="subtitle">Already have an account?</p>
                            <div class="login-input">
                                <input type="text" name="username" placeholder="Username" required autofocus>
                            </div>
                            <div class="login-input">
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <div id="login-button" class="login-input">
                                <button type="submit">Log In</button>
                            </div>
                            <div id="or-div">
                                <hr><p>OR</p><hr>
                            </div>
                            <div id="signup-button" class="login-input">
                                <button type="button" onclick="window.location.href='signup.php'">Create an Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>