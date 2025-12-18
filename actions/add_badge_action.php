<?php
//start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

$name = trim($_POST['name']);
$description = trim($_POST['description']);
$icon = trim($_POST['icon']);
$xpRequired = intval($_POST['xp_required']);

if (empty($name) || empty($description) || empty($icon) || $xpRequired < 0) {
     $_SESSION['error_message'] = "Please fill in all fields correctly.";
     header("Location: ../admin/add_badge.php"); exit();
}

$sql = "INSERT INTO Badge (name, description, icon, xp_required) VALUES (?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssi", $name, $description, $icon, $xpRequired);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Badge added successfully!";
        header("Location: ../admin/manage_badges.php");
    } else {
        $_SESSION['error_message'] = "Database Error: " . $stmt->error;
        header("Location: ../admin/add_badge.php");
    }
    $stmt->close();
}
$conn->close();
?>