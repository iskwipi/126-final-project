<?php
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$userID = $data['userID'];
$currentUserID = $_SESSION['userID'] ?? null;

if (!$currentUserID || !$userID || $currentUserID == $userID) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user IDs']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'platemate');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// checks if nakafollow na
$stmt = $conn->prepare("SELECT * FROM follows WHERE followerID=? AND followingID=?");
$stmt->bind_param("ii", $currentUserID, $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO follows (followerID, followingID) VALUES (?, ?)");
    $stmt->bind_param("ii", $currentUserID, $userID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'action' => 'followed']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Insert failed: ' . $stmt->error]);
    }
} else {
    $stmt = $conn->prepare("DELETE FROM follows WHERE followerID=? AND followingID=?");
    $stmt->bind_param("ii", $currentUserID, $userID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'action' => 'unfollowed']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Unfollow failed: ' . $stmt->error]);
    }
}

$conn->close();
