<?php
session_start();
require_once '../config/db.php';

// admin and POST Request Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get input from the form fields
$title = trim($_POST['title']);
// get activity type ('quest' or 'game')
$activityType = $_POST['activity_type'];
// get the specific ID based on which dropdown was used
$questId = isset($_POST['quest_id']) ? intval($_POST['quest_id']) : 0;
$gameId = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

$xpReward = intval($_POST['xp_reward']);
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];
$description = trim($_POST['description']);

// validation checks
if (empty($title) || empty($activityType) || $xpReward < 1) {
     $_SESSION['error_message'] = "Please fill in all required fields.";
     header("Location: ../admin/add_challenge.php"); exit();
}

// date validation
if ($endDate < $startDate) {
     $_SESSION['error_message'] = "End date cannot be before start date.";
     header("Location: ../admin/add_challenge.php"); exit();
}

// determine the final activity_id based on the selected type
$activityId = 0;
if ($activityType === 'quest') {
    if ($questId < 1) {
        $_SESSION['error_message'] = "Please select a valid quest.";
        header("Location: ../admin/add_challenge.php"); exit();
    }
    $activityId = $questId;
} elseif ($activityType === 'game') {
    if ($gameId < 1) {
        $_SESSION['error_message'] = "Please select a valid game.";
        header("Location: ../admin/add_challenge.php"); exit();
    }
    $activityId = $gameId;
}

// insert into the table structure
// Columns: title, description, activity_type, activity_id, xp_reward, start_date, end_date
$sql = "INSERT INTO Challenge (title, description, activity_type, activity_id, xp_reward, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // 'sssiiss' = string, string, string, int, int, string, string
    $stmt->bind_param("sssiiss", $title, $description, $activityType, $activityId, $xpReward, $startDate, $endDate);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Challenge created successfully!";
        header("Location: ../admin/manage_challenges.php");
    } else {
        $_SESSION['error_message'] = "Database Error: " . $stmt->error;
        header("Location: ../admin/add_challenge.php");
    }
    $stmt->close();
} else {
     $_SESSION['error_message'] = "Prepare Error: " . $conn->error;
     header("Location: ../admin/add_challenge.php");
}
$conn->close();
?>