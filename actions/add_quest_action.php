<?php
//start of the session and include database connection
session_start();
require_once '../config/db.php';

// admin and POST request check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get input
$title = trim($_POST['title']);
$subject = trim($_POST['subject']);
$level = intval($_POST['level']);
$xp_reward = intval($_POST['xp_reward']);
$description = trim($_POST['description']);

// data validation
if (empty($title) || empty($subject) || empty($description) || $level < 1 || $xp_reward < 1) {
    $_SESSION['error_message'] = "Please fill in all fields correctly.";
    header("Location: ../admin/add_quest.php"); exit();
}

// prepare the INSERT query
$sql = "INSERT INTO Quest (title, subject, description, level, xp_reward) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    // 'sssis' = string, string, string, integer, integer
    $stmt->bind_param("sssis", $title, $subject, $description, $level, $xp_reward);
    
    if ($stmt->execute()) {
        // if success
        $_SESSION['success_message'] = "Quest '$title' created successfully!";
        // redirect admin back to the list
        header("Location: ../admin/manage_quests.php"); 
    } else {
        // when database error
        $_SESSION['error_message'] = "Error creating quest: " . $stmt->error;
        header("Location: ../admin/add_quest.php");
    }
    $stmt->close();
} else {
    // In case of an error
    $_SESSION['error_message'] = "Database prepare error: " . $conn->error;
    header("Location: ../admin/add_quest.php");
}
$conn->close();
?>