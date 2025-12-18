<?php
// start session and include database connection
session_start();
require_once '../config/db.php';

// security checks
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner' || $_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];
$gameId = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

if ($gameId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid game ID']);
    exit();
}

// start transaction
$conn->begin_transaction();

try {
    // fetch game info
    $stmt = $conn->prepare("SELECT xp_reward FROM Game WHERE game_id = ?");
    $stmt->bind_param("i", $gameId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Game not found');
    }
    
    $game = $result->fetch_assoc();
    $xpReward = $game['xp_reward'];
    $stmt->close();
    
    // check if user already completed this game
    $checkStmt = $conn->prepare("SELECT completion_id, xp_awarded FROM GameCompletion WHERE user_id = ? AND game_id = ?");
    $checkStmt->bind_param("ii", $userId, $gameId);
    $checkStmt->execute();
    $completionResult = $checkStmt->get_result();
    $alreadyCompleted = $completionResult->num_rows > 0;
    $checkStmt->close();
    
    if ($alreadyCompleted) {
        // if user already completed this game before, then no XP awarded
        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'firstTime' => false,
            'xpAwarded' => 0,
            'message' => 'Game completed again! (No XP for replay)'
        ]);
        exit();
    }
    
    // when first time completion, award XP
    if ($xpReward > 0) {
        // update user XP
        $updateStmt = $conn->prepare("UPDATE User SET xp = xp + ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $xpReward, $userId);
        $updateStmt->execute();
        $updateStmt->close();
        
        // update session
        $_SESSION['xp'] += $xpReward;
    }
    
    // record completion in GameCompletion table
    $insertStmt = $conn->prepare("INSERT INTO GameCompletion (user_id, game_id, xp_awarded) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iii", $userId, $gameId, $xpReward);
    $insertStmt->execute();
    $insertStmt->close();
    
    // check if this game is part of an active challenge
    $today = date('Y-m-d');
    $challengeStmt = $conn->prepare("
        SELECT c.challenge_id, c.xp_reward as bonus_xp
        FROM Challenge c
        WHERE c.activity_type = 'game' 
        AND c.activity_id = ?
        AND c.is_active = 1
        AND c.end_date >= ?
        AND NOT EXISTS (
            SELECT 1 FROM UserChallengeProgress ucp 
            WHERE ucp.user_id = ? AND ucp.challenge_id = c.challenge_id
        )
    ");
    $challengeStmt->bind_param("isi", $gameId, $today, $userId);
    $challengeStmt->execute();
    $challengeResult = $challengeStmt->get_result();
    
    $challengeCompleted = false;
    $bonusXP = 0;
    
    if ($challengeResult->num_rows > 0) {
        $challenge = $challengeResult->fetch_assoc();
        $challengeId = $challenge['challenge_id'];
        $bonusXP = $challenge['bonus_xp'];
        
        // award challenge bonus XP
        $bonusStmt = $conn->prepare("UPDATE User SET xp = xp + ? WHERE user_id = ?");
        $bonusStmt->bind_param("ii", $bonusXP, $userId);
        $bonusStmt->execute();
        $bonusStmt->close();
        
        // record challenge completion
        $challengeInsert = $conn->prepare("INSERT INTO UserChallengeProgress (user_id, challenge_id) VALUES (?, ?)");
        $challengeInsert->bind_param("ii", $userId, $challengeId);
        $challengeInsert->execute();
        $challengeInsert->close();
        
        $_SESSION['xp'] += $bonusXP;
        $challengeCompleted = true;
    }
    $challengeStmt->close();
    
    // commit transaction
    $conn->commit();
    
    // return success with details
    echo json_encode([
        'status' => 'success',
        'firstTime' => true,
        'xpAwarded' => $xpReward,
        'bonusXP' => $bonusXP,
        'challengeCompleted' => $challengeCompleted,
        'totalXP' => $xpReward + $bonusXP,
        'message' => 'Congratulations! XP awarded!'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>