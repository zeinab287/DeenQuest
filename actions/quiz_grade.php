<?php
// start session and include database connection

session_start();
require_once '../config/db.php';

// security checks: user must be logged in and form must be POSTed correctly.
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['quest_id']) || !isset($_POST['answers'])) {
    header("Location: ../dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$quest_id = $_POST['quest_id'];
// array:  [question_id => selected_option]
$user_answers = $_POST['answers']; 

// initialize score variables
$score = 0;
$total_questions = 0;


// grade the quiz
// fetch correct answers for this quest from database
    // fetch correct option
    $sql = "SELECT question_id, correct_option FROM quizquestion WHERE quest_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quest_id);
$stmt->execute();
$result = $stmt->get_result();

// loop through each question found in database
while ($row = $result->fetch_assoc()) {
    $total_questions++;
    $q_id = $row['question_id'];
    $correct_answer = $row['correct_option'];

    // check if the user submitted an answer for this specific question ID
    if (isset($user_answers[$q_id])) {
        // compare user's answer with the correct answer
        if ($user_answers[$q_id] === $correct_answer) {
            // got it right
            $score++;
        }
    }
}
$stmt->close();


// handle xp awarding if passed

$xp_gained = 0;

// only award XP if user got at least 1 question right
if ($score > 0) {
    
    // has user already completed the quiz stage for this quest?
    // check the progress table.
    $checkSql = "SELECT progress_id FROM progress WHERE user_id = ? AND quest_id = ? AND progress_stage IN ('quiz_completed', 'completed')";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $user_id, $quest_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    // if num_rows is 0, it means they haven't finished this stage yet: award XP
    if ($checkResult->num_rows === 0) {
        
        // find out how much XP this quest is worth
        $xpSql = "SELECT xp_reward FROM Quest WHERE quest_id = ?";
        $xpStmt = $conn->prepare($xpSql);
        $xpStmt->bind_param("i", $quest_id);
        $xpStmt->execute();
        $xpResult = $xpStmt->get_result();
        $questData = $xpResult->fetch_assoc();
        $xp_reward = $questData['xp_reward'];
        $xpStmt->close();
        
        // update user table:        // Update user XP
        $updateUserSql = "UPDATE user SET xp = xp + ? WHERE user_id = ?";
        $updateUserStmt = $conn->prepare($updateUserSql);
        $updateUserStmt->bind_param("ii", $xp_reward, $user_id);
        $updateUserStmt->execute();
        $updateUserStmt->close();

        // update session XP so the navbar updates immediately
        $_SESSION['xp'] += $xp_reward;
        
        // update/insert into progress table
        // use "ON DUPLICATE KEY UPDATE" because the user might have started the quest (creating an 'active' entry) but not finished the quiz yet.
        $progressSql = "INSERT INTO Progress (user_id, quest_id, xp_earned_in_quest, progress_stage, last_updated) 
                        VALUES (?, ?, ?, 'quiz_completed', NOW()) 
                        ON DUPLICATE KEY UPDATE progress_stage = 'quiz_completed', xp_earned_in_quest = ?, last_updated = NOW()";
        $progressStmt = $conn->prepare($progressSql);
        $progressStmt->bind_param("iiii", $user_id, $quest_id, $xp_reward, $xp_reward);
        $progressStmt->execute();
        $progressStmt->close();

        // set variable to show on results page
        $xp_gained = $xp_reward;

    } 
    // if user had already completed it, do nothing. $xp_gained remains 0.

    $checkStmt->close();
}


// save results and redirect to results page

// save the results temporarily in the session to show them on the next page.
$_SESSION['quiz_result'] = [
    'score' => $score,
    'total' => $total_questions,
    'xp_gained' => $xp_gained
];

$conn->close();

// redirect to the visual results page
header("Location: ../pages/quests/quiz_result.php");
exit();
?>