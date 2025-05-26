<?php
session_start();
$step = $_POST['step'] ?? 1;

if (isset($_SESSION['userID']) || isset($_SESSION['username'])) {
    unset($_SESSION['userID']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['dob']);
    unset($_SESSION['occupation']);
    unset($_SESSION['province']);
    unset($_SESSION['city']);
}else if ($step == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['dob'] = $_POST['dob'];
    $step = 2;
}else if ($step == 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['occupation'] = $_POST['occupation'];
    $_SESSION['province'] = $_POST['province'];
    $_SESSION['city'] = $_POST['city'];
    $step = 3;
}
echo implode($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Platemate Signup</title>
        <link href='https://fonts.googleapis.com/css?family=Quicksand' rel='stylesheet'>
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dbc4f87d4f.js" crossorigin="anonymous"></script>
    </head>
    <script>
        function validatePasswords() {
            const pw1 = document.querySelector('input[name="password"]').value;
            const pw2 = document.querySelector('input[name="verification"]').value;
            if (pw1 !== pw2) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
    <body>
        <div id="signuppage" class="landing-signuppage-container">
            <div class="signup-tab">
                <div class="signup-tab-container">
                    <div class="signup-tab-contents">
                        <p>Signup (Step <?= $step ?> of 3)</p>

                        <?php if ($step == 1): ?>
                            <p id="signup-subtitle">Get ready for your cooking journey!</p>
                            <form class="signup-form" method="post">
                                <input type="hidden" name="step" value="1">
                                <div id="username-input" class="signup-form">
                                    <input type="text" id="username" name="username" placeholder="Username" required autofocus>
                                </div>
                                <div id="email-input" class="signup-form">
                                    <input type="email" id="email" name="email" placeholder="Email" required>
                                </div>
                                <div id="dob-input" class="signup-form">
                                    <label for="dob">Date of Birth:</label>
                                    <input type="date" id="dob" name="dob" required>
                                </div>
                                <button type="submit" id="next-button">Next</button>
                            </form>

                        <?php elseif ($step == 2): ?>
                            <p id="signup-subtitle">Almost there!</p>
                            <form class="signup-form" method="post">
                                <input type="hidden" name="step" value="2">
                                <div id="occupation-input" class="signup-form">
                                    <input type="text" id="occupation" name="occupation" placeholder="Occupation" required autofocus>
                                </div>
                                <div id="location-input" class="signup-form">
                                    <input type="text" id="province" name="province" placeholder="Province" required>
                                    <input type="text" id="city" name="city" placeholder="City (Optional)">
                                </div>
                                <p id="signup-input-subtitle">This information will be displayed on your profile.</p>
                                <button type="submit" id="next-button">Next</button>
                            </form>

                        <?php elseif ($step == 3): ?>
                            <p id="signup-subtitle">One last step!</p>
                            <form class="signup-form" action="register.php" method="post" onsubmit="return validatePasswords();">
                                <input type="hidden" name="step" value="3">
                                <div id="password-input" class="signup-form">
                                    <input type="password" id="password" name="password" placeholder="Password" required autofocus minlength="8" pattern=".*[0-9].*">
                                    <input type="password" id="verification" name="verification" placeholder="Verify Password" required minlength="8" pattern=".*[0-9].*">
                                    <p id="signup-input-subtitle">Password must be eight (8) characters long and include at least one (1) number.</p>
                                </div>
                                <button type="submit" id="next-button">Create Account</button>
                            </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="website-name">
                <p>Platemate</p>
            </div>
        </div>
    </body>
</html>