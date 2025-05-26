<?php
$userID = $_SESSION['userID'];
$username = $_SESSION['username'];
$conn = new mysqli("localhost", "root", "", "platemate");
$result = $conn->query("SELECT user.userID, username, dateOfBirth, occupation, email, provinceName, cityName
FROM ((SELECT * FROM user WHERE user.userID = '$userID') AS user
INNER JOIN province ON user.provinceID = province.provinceID)
INNER JOIN city ON user.cityID = city.cityID");
$user = $result->fetch_assoc();
$dob = new DateTime($user["dateOfBirth"]);
$today = new DateTime();
$age = $today->diff($dob)->y;
$result = $conn->query("SELECT followingID AS userID, COUNT(followerID) AS followerCount FROM follows WHERE followingID = '$userID' GROUP BY followingID");
if($result->num_rows > 0){
    $follows = $result->fetch_assoc();
    $followerCount = $follows["followerCount"];
}else{
    $followerCount = 0;
}

echo '
    <div class="profile-details">
        <p id="profile-name">' . $user["username"] . '</p>
        <p id="bio">' . $age . ', ' . $user["occupation"] . '</p>
        <p id="loc">' . $user["cityName"] . ', ' . $user["provinceName"] . '</p>
    </div>
    <div class="followed-by">
        <p>Followed by:<br>' . $followerCount . ' follower/s</p>
    </div>
';