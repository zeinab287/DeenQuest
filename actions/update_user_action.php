<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// admin security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get and sanitize input
$userIdToUpdate = intval($_POST['user_id']);
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$role = $_POST['role'];
$xp = intval($_POST['xp']);

// data validation
if ($userIdToUpdate < 1 || empty($name) || empty($email) || $xp < 0) {
     $_SESSION['error_message'] = "Invalid input data.";
     header("Location: ../admin/edit_user.php?id=" . $userIdToUpdate); exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Invalid email format.";
    header("Location: ../admin/edit_user.php?id=" . $userIdToUpdate); exit();
}
// prevent changing your own role away from admin
if ($userIdToUpdate == $_SESSION['user_id'] && $role !== 'admin') {
     $_SESSION['error_message'] = "You cannot demote your own admin account.";
     header("Location: ../admin/edit_user.php?id=" . $userIdToUpdate); exit();
}


// UPDATE database
$sql = "UPDATE User SET name = ?, email = ?, role = ?, xp = ? WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    // 'sssii' = string, string, string, int, int
    $stmt->bind_param("sssii", $name, $email, $role, $xp, $userIdToUpdate);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User details updated successfully.";
        header("Location: ../admin/manage_users.php");
    } else {
        // check for duplicate email error 
        if ($conn->errno === 1062) {
             $_SESSION['error_message'] = "Error: That email address is already in use by another user.";
        } else {
             $_SESSION['error_message'] = "Database Error: " . $stmt->error;
        }
        header("Location: ../admin/edit_user.php?id=" . $userIdToUpdate);
    }
    $stmt->close();
}
$conn->close();
?>