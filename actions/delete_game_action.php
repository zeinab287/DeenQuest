<?php
//start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'])) {
    $gameId = intval($_GET['id']);
    $sql = "DELETE FROM Game WHERE game_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $gameId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Game definition deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting game.";
        }
        $stmt->close();
    }
}
$conn->close();
header("Location: ../admin/manage_games.php");
?>