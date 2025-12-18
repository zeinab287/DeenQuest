<?php
session_start();
require_once '../config/db.php';

// admin and POST request check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

// get input
$questId = intval($_POST['quest_id']);
$questionText = trim($_POST['question_text']);
$optionA = trim($_POST['option_a']);
$optionB = trim($_POST['option_b']);
$optionC = trim($_POST['option_c']);
$optionD = trim($_POST['option_d']);
$correctOption = $_POST['correct_option']; 

// validate data
if (empty($questionText) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || empty($correctOption) || $questId < 1) {
    $_SESSION['error_message'] = "Please fill in all fields correctly.";
    header("Location: ../admin/add_question.php?quest_id=" . $questId); exit();
}

// prepare the INSERT query
$sql = "INSERT INTO QuizQuestion (quest_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    // 'issssss' = integer, string, string, string, string, string, string
    $stmt->bind_param("issssss", $questId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption);
    
    if ($stmt->execute()) {
        // if success
        $_SESSION['success_message'] = "New question added successfully!";
        header("Location: ../admin/manage_quiz.php?quest_id=" . $questId); // Redirect back to the quiz list
    } else {
        // when database error
        $_SESSION['error_message'] = "Error adding question: " . $stmt->error;
        header("Location: ../admin/add_question.php?quest_id=" . $questId);
    }
    $stmt->close();
} else {
    // error preparing statement
    $_SESSION['error_message'] = "Database prepare error: " . $conn->error;
    header("Location: ../admin/add_question.php?quest_id=" . $questId);
}
$conn->close();
?>