<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

$badgeId = intval($_POST['badge_id']);
$name = trim($_POST['name']);
$description = trim($_POST['description']);
$icon = trim($_POST['icon']);
$xpRequired = intval($_POST['xp_required']);

if ($badgeId < 1 || empty($name) || empty($description) || empty($icon) || $xpRequired < 0) {
     $_SESSION['error_message'] = "Invalid input data.";
     header("Location: ../admin/edit_badge.php?id=" . $badgeId); exit();
}

$sql = "UPDATE Badge SET name = ?, description = ?, icon = ?, xp_required = ? WHERE badge_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssii", $name, $description, $icon, $xpRequired, $badgeId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Badge updated successfully!";
        header("Location: ../admin/manage_badges.php");
    } else {
        $_SESSION['error_message'] = "Database Error: " . $stmt->error;
        header("Location: ../admin/edit_badge.php?id=" . $badgeId);
    }
    $stmt->close();
}
$conn->close();
?>