<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get and sanitize input
$gameId = intval($_POST['game_id']);
$title = trim($_POST['title']);
$type = trim($_POST['type']);
$subject = trim($_POST['subject']);
$difficultyLevel = intval($_POST['difficulty_level']);
$xpReward = intval($_POST['xp_reward']);
// Fix: Store relative path only. Do not force /DeenQuest/ prefix.
$gameFilePath = trim($_POST['game_file_path'], "/ ");
$description = trim($_POST['description']);

// data validation
if ($gameId < 1 || empty($title) || empty($gameFilePath)) {
     $_SESSION['error_message'] = "Invalid data.";
     header("Location: ../admin/edit_game.php?id=" . $gameId); exit();
}

// prepare UPDATE query
$sql = "UPDATE Game SET title = ?, description = ?, type = ?, subject = ?, difficulty_level = ?, xp_reward = ?, game_file_path = ? WHERE game_id = ?";
if ($stmt = $conn->prepare($sql)) {
    // 'ssssiisi' = string, string, string, string, int, int, string, int
    $stmt->bind_param("ssssiisi", $title, $description, $type, $subject, $difficultyLevel, $xpReward, $gameFilePath, $gameId);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Game updated successfully!";
        header("Location: ../admin/manage_games.php");
    } else {
        $_SESSION['error_message'] = "Error updating game: " . $stmt->error;
        header("Location: ../admin/edit_game.php?id=" . $gameId);
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database error.";
    header("Location: ../admin/manage_games.php");
}
$conn->close();
?>