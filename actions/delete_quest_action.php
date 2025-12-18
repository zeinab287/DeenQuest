<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// admin security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

// get Quest ID from the URL parameter
if (isset($_GET['id'])) {
    $questId = intval($_GET['id']);

    // prepare DELETE query for Quest
    $sql = "DELETE FROM Quest WHERE quest_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $questId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Quest deleted successfully.";
        } else {
            // if the quest is linked to quizzes/progress records 
            $_SESSION['error_message'] = "Error deleting quest. It may be in use.";
        }
        $stmt->close();
    }
}
$conn->close();
// redirect back to list
header("Location: ../admin/manage_quests.php");
?>