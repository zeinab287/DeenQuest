<?php
//start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get and sanitize input
$questId = intval($_POST['quest_id']); // Get hidden ID
$title = trim($_POST['title']);
$subject = trim($_POST['subject']);
$level = intval($_POST['level']);
$xp_reward = intval($_POST['xp_reward']);
$description = trim($_POST['description']);

// data validation
if (empty($title) || empty($subject) || empty($description) || $level < 1 || $xp_reward < 1 || $questId < 1) {
    $_SESSION['error_message'] = "Invalid data. Update failed.";
    // redirect back to the edit page for this specific ID
    header("Location: ../admin/edit_quest.php?id=" . $questId); exit();
}

// 3. prepare UPDATE query
$sql = "UPDATE Quest SET title = ?, subject = ?, description = ?, level = ?, xp_reward = ? WHERE quest_id = ?";
if ($stmt = $conn->prepare($sql)) {
    // 'sssisi' = string, string, string, int, int, int
    $stmt->bind_param("sssisi", $title, $subject, $description, $level, $xp_reward, $questId);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Quest updated successfully!";
        header("Location: ../admin/manage_quests.php");
    } else {
        $_SESSION['error_message'] = "Error updating quest: " . $stmt->error;
        header("Location: ../admin/edit_quest.php?id=" . $questId);
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database prepare error.";
    header("Location: ../admin/manage_quests.php");
}
$conn->close();
?>