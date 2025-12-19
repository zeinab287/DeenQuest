<?php
// start the session and include database connection
session_start();
require_once '../config/db.php';

// security checks
// ensure user is logged in as a learner and request is POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    // invalid request method or authentification, redirect away
    header("Location: ../login.php"); exit();
}

if (isset($_POST['reflection_id'])) {
    $reflectionId = intval($_POST['reflection_id']);
    $userId = $_SESSION['user_id'];

    // prepare DELETE query for Reflection
    // include "AND user_id = ?" to ensure the user can only delete a reflection that belongs to him/her.
    $sql = "DELETE FROM reflection WHERE reflection_id = ? AND user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $reflectionId, $userId);
        $stmt->execute();

        // check if a row was actuall deleted
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Reflection deleted successfully.";
        } else {
            // either the ID doesn't exist, or it doesn't belong to this user.
            $_SESSION['error_message'] = "Error: Could not delete reflection.";
        }
        $stmt->close();
    } else {
         $_SESSION['error_message'] = "Database error.";
    }
}

$conn->close();
// redirect back to the list page
header("Location: ../pages/reflection/index.php");
exit();
?>