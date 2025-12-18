<?php
// act as a traffic director based on user role
session_start();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// based on the role stored in the session, send user to the right file.
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: learner_dashboard.php");
    exit();
}
?>