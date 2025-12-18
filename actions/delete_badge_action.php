<?php
//start the session and include database connection
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'])) {
    $badgeId = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM Badge WHERE badge_id = ?");
    $stmt->bind_param("i", $badgeId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Badge deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting badge.";
    }
    $stmt->close();
}
$conn->close();
header("Location: ../admin/manage_badges.php");
exit();
?>