<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// admin security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'])) {
    $userIdToDelete = intval($_GET['id']);

    // prevent self-deletion check
    if ($userIdToDelete == $_SESSION['user_id']) {
        $_SESSION['error_message'] = "You cannot delete your own admin account.";
        header("Location: ../admin/manage_users.php"); exit();
    }

    // perform the deletion. due to ON DELETE CASCADE relationships in the database, this will automatically 
    // delete user's progress, reflections, quiz history, etc.
    $stmt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $userIdToDelete);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User and all associated data deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting user: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
header("Location: ../admin/manage_users.php");
exit();
?>