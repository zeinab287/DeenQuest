<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// security checks
// ensure that user is admin and request is POST method
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    // invalid access attempt
    $_SESSION['error_message'] = "Unauthorized access.";
    header("Location: ../admin/manage_quests.php");
    exit();
}

// get and validate inputs
$questionId = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
// quest_id to redirect back to the right page afterwards.
$questId = isset($_POST['quest_id']) ? intval($_POST['quest_id']) : 0;

if ($questionId <= 0) {
    $_SESSION['error_message'] = "Invalid question ID provided.";
    // redirect back based on whether we know the quest ID or not
    $redirectBack = ($questId > 0) ? "../admin/edit_quest.php?id=$questId" : "../admin/manage_quests.php";
    header("Location: $redirectBack");
    exit();
}

// perform    // delete query
    $sql = "DELETE FROM quizquestion WHERE question_id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $questionId);
    
    if ($stmt->execute()) {
        // check if a row was actually deleted
        if ($stmt->affected_rows > 0) {
             $_SESSION['success_message'] = "Question deleted successfully.";
        } else {
             $_SESSION['error_message'] = "Error: Question could not be found or already deleted.";
        }
    } else {
        // SQL execution error
        $_SESSION['error_message'] = "Database Error during execution: " . $stmt->error;
    }
    $stmt->close();
} else {
     // SQL preparation error
     $_SESSION['error_message'] = "Database Error during preparation: " . $conn->error;
}

$conn->close();

// redirect back to the manage quiz page
$redirectBack = ($questId > 0) ? "../admin/edit_quest.php?id=$questId" : "../admin/manage_quests.php";
header("Location: $redirectBack");
exit();
?>