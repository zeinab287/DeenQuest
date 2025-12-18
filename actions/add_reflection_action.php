<?php
//start session and include database connection

session_start();
require_once '../config/db.php';

// security and request method check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get input data
$userId = $_SESSION['user_id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);
$goals = trim($_POST['goals']);

// data validation
if (empty($title) || empty($content)) {
    $_SESSION['error_message'] = "Please fill in both the Title and Reflection content.";
    header("Location: ../pages/reflection/add.php"); exit();
}

// insert data into database
$sql = "INSERT INTO Reflection (user_id, title, content, goals) VALUES (?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    // 'isss' = int, string, string, string
    $stmt->bind_param("isss", $userId, $title, $content, $goals);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Reflection saved successfully!";
        header("Location: ../pages/reflection/index.php");
    } else {
        $_SESSION['error_message'] = "Database Error: " . $stmt->error;
        header("Location: ../pages/reflection/add.php");
    }
    $stmt->close();
} else {
     $_SESSION['error_message'] = "Prepare Error: " . $conn->error;
     header("Location: ../pages/reflection/add.php");
}
$conn->close();
?>