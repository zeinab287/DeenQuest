<?php
//start session and include database connection
session_start();
require_once '../config/db.php';

// Admin and POST check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get input
$title = trim($_POST['title']);
$type = trim($_POST['type']);
$subject = trim($_POST['subject']);
$difficultyLevel = intval($_POST['difficulty_level']);
$xpReward = intval($_POST['xp_reward']);
// Fix: Store relative path only. Do not force /DeenQuest/ prefix.
$gameFilePath = trim($_POST['game_file_path'], "/ "); 
$description = trim($_POST['description']);

// data validation
if (empty($title) || empty($type) || empty($subject) || empty($gameFilePath) || $difficultyLevel < 1) {
    $_SESSION['error_message'] = "Please fill in all required fields.";
    header("Location: ../admin/add_game.php"); exit();
}

// Prepare INSERT query
$sql = "INSERT INTO Game (title, description, type, subject, difficulty_level, xp_reward, game_file_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    // 'ssssiis' = string, string, string, string, int, int, string
    $stmt->bind_param("ssssiis", $title, $description, $type, $subject, $difficultyLevel, $xpReward, $gameFilePath);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Game '$title' defined successfully!";
        header("Location: ../admin/manage_games.php");
    } else {
        $_SESSION['error_message'] = "Error adding game: " . $stmt->error;
        header("Location: ../admin/add_game.php");
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database error: " . $conn->error;
    header("Location: ../admin/add_game.php");
}
$conn->close();
?>