<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// Admin and POST check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get and sanitize input
$challengeId = intval($_POST['challenge_id']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$goalType = $_POST['goal_type'];
$targetValue = intval($_POST['target_value']);
$xpReward = intval($_POST['xp_reward']);
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];
$isActive = intval($_POST['is_active']); // assuming checkbox returns 1 for checked, 0 for unchecked

// data validation
if ($challengeId < 1 || empty($title) || empty($description) || $targetValue < 1) {
    $_SESSION['error_message'] = "Please fill in all fields correctly.";
    header("Location: ../admin/edit_challenge.php?id=" . $challengeId); exit();
}

// date validation
if ($endDate < $startDate) {
    $_SESSION['error_message'] = "End date cannot be before start date.";
    header("Location: ../admin/edit_challenge.php?id=" . $challengeId); exit();
}

// prepare UPDATE query
$sql = "UPDATE Challenge SET title = ?, description = ?, goal_type = ?, target_value = ?, xp_reward = ?, start_date = ?, end_date = ?, is_active = ? WHERE challenge_id = ?";
if ($stmt = $conn->prepare($sql)) {
    // 'sssiissii' = string, string, string, int, int, string, string, int, int
    $stmt->bind_param("sssiissii", $title, $description, $goalType, $targetValue, $xpReward, $startDate, $endDate, $isActive, $challengeId);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Challenge updated successfully!";
        header("Location: ../admin/manage_challenges.php");
    } else {
        $_SESSION['error_message'] = "Error updating challenge: " . $stmt->error;
        header("Location: ../admin/edit_challenge.php?id=" . $challengeId);
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database prepare error.";
    header("Location: ../admin/manage_challenges.php");
}
$conn->close();
?>