<?php
// start session and include database connection
session_start();
require_once '../config/db.php';

// ensure user is logged in, is an ADMIN, and request is POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    // if user not authorized, just kill the script entirely for security.
    die("Access Denied"); 
}


// get data from the form and clean it up 
$title = trim($_POST['title']);
$subject = trim($_POST['subject']);
// make sure level and xp are integers
$level = intval($_POST['level']);
$xp_reward = intval($_POST['xp_reward']);
$description = trim($_POST['description']);


// data validation: heck if anything is empty
if (empty($title) || empty($subject) || empty($description) || $level < 1 || $xp_reward < 1) {
    $_SESSION['error_message'] = "Error: Please fill in all fields correctly.";
    header("Location: ../admin/quest_create.php");
    exit();
}


// prepare the SQL Insert statement
$sql = "INSERT INTO Quest (title, subject, description, level, xp_reward) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // bind the parameters. 
    // s = string, i = integer. So "sssis" means String, String, String, Integer, Integer.
    $stmt->bind_param("sssis", $title, $subject, $description, $level, $xp_reward);

    // execute and check result
    if ($stmt->execute()) {
        // success
        $_SESSION['success_message'] = "New quest created successfully!";
        // send user back to the creation form so they can add another one easily.
        header("Location: ../admin/quest_create.php");
    } else {
        // database error during insert
        $_SESSION['error_message'] = "Database Error: Could not save quest. " . $stmt->error;
        header("Location: ../admin/quest_create.php");
    }
    $stmt->close();

} else {
     // error preparing the query
     $_SESSION['error_message'] = "System Error: Could not prepare database statement.";
     header("Location: ../admin/quest_create.php");
}

$conn->close();
exit();
?>