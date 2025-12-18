<?php
//grade and process quiz submission
session_start();
require_once '../config/db.php';

// security and request method check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php"); exit();
}

$userId = $_SESSION['user_id'];
$questId = isset($_POST['quest_id']) ? intval($_POST['quest_id']) : 0;
$userAnswers = isset($_POST['answers']) ? $_POST['answers'] : [];

if ($questId <= 0 || empty($userAnswers)) {
    // redirect if data is missing, i.e the user submitted an empty form)
    header("Location: ../pages/quizzes/index.php"); exit();
}

// fetch correct answers and base XP reward for this Quest
$sql = "SELECT q.question_id, q.correct_option, qt.xp_reward
        FROM QuizQuestion q
        JOIN Quest qt ON q.quest_id = qt.quest_id
        WHERE q.quest_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $questId);
$stmt->execute();
$result = $stmt->get_result();

$correctAnswers = [];
// The full reward amount defined in the database
$baseXpReward = 0; 
$totalQuestions = $result->num_rows;

if ($totalQuestions === 0) {
     header("Location: ../pages/quizzes/index.php"); exit();
}

while ($row = $result->fetch_assoc()) {
    $correctAnswers[$row['question_id']] = $row['correct_option'];
    $baseXpReward = $row['xp_reward'];
}
$stmt->close();


// calculate score
$correctCount = 0;
foreach ($userAnswers as $qId => $userSelectedOption) {
    if (isset($correctAnswers[$qId]) && $userAnswers[$qId] === $correctAnswers[$qId]) {
        $correctCount++;
    }
}
$scorePercentage = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;
$passingScore = 70;
$passed = ($scorePercentage >= $passingScore);


// now determine XP to award based on pass/fail and previous completions
$xpEarned = 0;

if ($passed) {
    // check if user have passed this before and earned XP (xp_earned > 0)
    // use SELECT 1 for efficiency as i just need to know if a row exists.
    $checkPrevSql = "SELECT 1 FROM UserQuestProgress WHERE user_id = ? AND quest_id = ? AND xp_earned > 0 LIMIT 1";
    $stmtCheck = $conn->prepare($checkPrevSql);
    $stmtCheck->bind_param("ii", $userId, $questId);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        // already passed before: no reward.
        $xpEarned = 0; 
    } else {
        // first time passing: give full base reward.
        $xpEarned = $baseXpReward;
    }
    $stmtCheck->close();
} else {
    // failed: no XP.
    $xpEarned = 0;
}

// record quest result in database using the calculated $xpEarned
$insertSql = "INSERT INTO UserQuestProgress (user_id, quest_id, score, xp_earned) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($insertSql);
$stmtInsert->bind_param("iiii", $userId, $questId, $scorePercentage, $xpEarned);
$stmtInsert->execute();
$stmtInsert->close();


// award XP to user account and check challenges if they earned any XP
if ($xpEarned > 0) {
    // award the calculated Quest XP (either full or 0) to user account
    $updateXpSql = "UPDATE User SET xp = xp + ? WHERE user_id = ?";
    $stmtXp = $conn->prepare($updateXpSql);
    $stmtXp->bind_param("ii", $xpEarned, $userId);
    $stmtXp->execute();
    $stmtXp->close();
    // update session variable for immediate display updates
    $_SESSION['xp'] += $xpEarned;

    // check for linked challenge completion
    $today = date('Y-m-d');
    $chalSql = "SELECT challenge_id, xp_reward FROM Challenge
                WHERE is_active = 1
                AND activity_type = 'quest'
                AND activity_id = ?
                AND ? BETWEEN start_date AND end_date
                LIMIT 1";
    $stmtChal = $conn->prepare($chalSql);
    $stmtChal->bind_param("is", $questId, $today);
    $stmtChal->execute();
    $chalResult = $stmtChal->get_result();

    if ($chalResult->num_rows > 0) {
        $challengeData = $chalResult->fetch_assoc();
        $challengeId = $challengeData['challenge_id'];
        $challengeBonusXp = $challengeData['xp_reward'];

        // check if user already completed this specific challenge
        $checkProgSql = "SELECT 1 FROM UserChallengeProgress WHERE user_id = ? AND challenge_id = ?";
        $stmtCheck = $conn->prepare($checkProgSql);
        $stmtCheck->bind_param("ii", $userId, $challengeId);
        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows === 0) {
            // mark challenge as complete
            $insertProgSql = "INSERT INTO UserChallengeProgress (user_id, challenge_id) VALUES (?, ?)";
            $stmtProg = $conn->prepare($insertProgSql);
            $stmtProg->bind_param("ii", $userId, $challengeId);
            $stmtProg->execute();
            $stmtProg->close();

            // award challenge bonus XP
            if ($challengeBonusXp > 0) {
                $updateBonusSql = "UPDATE User SET xp = xp + ? WHERE user_id = ?";
                $stmtBonus = $conn->prepare($updateBonusSql);
                $stmtBonus->bind_param("ii", $challengeBonusXp, $userId);
                $stmtBonus->execute();
                $stmtBonus->close();
                $_SESSION['xp'] += $challengeBonusXp;
                // add bonus to the total displayed on results page
                $xpEarned += $challengeBonusXp;
            }
        }
        $stmtCheck->close();
    }
    $stmtChal->close();
}

$conn->close();
// redirect to results page 
// pass the total XP earned (quest xp + potential challenge bonus) to display to the user dashboard
header("Location: ../pages/quizzes/quiz_result.php?quest_id=$questId&score=$scorePercentage&passed=" . ($passed ? 1 : 0) . "&xp=$xpEarned");
exit();
?>