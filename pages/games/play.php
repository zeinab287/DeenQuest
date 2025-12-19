<?php
// start session, check user role and include database connection
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';

if (!isset($_GET['game_id'])) { header("Location: index.php"); exit(); }
$gameId = intval($_GET['game_id']);
$userId = $_SESSION['user_id'];

// fetch game info
$stmt = $conn->prepare("SELECT title, description, xp_reward, game_file_path FROM game WHERE game_id = ?");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$gameInfo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$gameInfo || empty($gameInfo['game_file_path'])) { 
    header("Location: index.php"); exit(); 
}

// FIX: Normalize game path to handle live server folder changes
// The DB might have "/DeenQuest/assets/..." but we might be at "/" or "/OtherApp/"
$gamePath = $gameInfo['game_file_path'];
if (strpos($gamePath, 'assets/games/') !== false) {
    // Extract the part starting from assets/games/...
    $relativePath = substr($gamePath, strpos($gamePath, 'assets/games/'));
    // Rebuild with the correct current Web Root
    $gamePath = $webRoot . $relativePath;
}
// Update the variable for the view
$gameInfo['game_file_path'] = $gamePath;

// check if user already completed this game
$checkStmt = $conn->prepare("SELECT completion_id FROM gamecompletion WHERE user_id = ? AND game_id = ?");
$checkStmt->bind_param("ii", $userId, $gameId);
$checkStmt->execute();
$alreadyCompleted = $checkStmt->get_result()->num_rows > 0;
$checkStmt->close();
$conn->close();

$pageTitle = "Play: " . htmlspecialchars($gameInfo['title']);
include('../../includes/header.php');
?>

<style>
/* hide the game's internal victory message */
#gameIframe {
    border: 0;
}
</style>

<div class="container py-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-success mb-1"><?= htmlspecialchars($gameInfo['title']) ?></h2>
        <p class="text-muted mb-3"><?= htmlspecialchars($gameInfo['description']) ?></p>
        
        <?php if ($alreadyCompleted): ?>
            <span class="badge bg-secondary fs-6 shadow-sm me-2">
                <i class="fas fa-check-circle me-1"></i>Already Completed
            </span>
            <span class="badge bg-info text-dark fs-6 shadow-sm">
                <i class="fas fa-redo me-1"></i>Playing for Fun (No XP)
            </span>
        <?php else: ?>
            <span class="badge bg-warning text-dark fs-6 shadow-sm">
                <i class="fas fa-star me-1"></i>First Time - Earn <?= $gameInfo['xp_reward'] ?> XP!
            </span>
        <?php endif; ?>
    </div>

    <div class="ratio ratio-16x9 shadow-sm rounded border" style="max-height: 600px; background: #f8f9fa;">
        <iframe id="gameIframe" src="<?= htmlspecialchars($gameInfo['game_file_path']) ?>" title="<?= htmlspecialchars($gameInfo['title']) ?>" allowfullscreen style="border:0;"></iframe>
    </div>

    <!-- victory Overlay -->
    <div id="victory-overlay" class="position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.85); z-index: 99999; display: none; justify-content: center; align-items: center;">
        <div class="bg-white p-5 rounded-3 shadow-lg text-center animate__animated animate__zoomIn" style="max-width: 500px; margin: 20px;">
            <div class="mb-3 text-warning">
                <i class="fas fa-trophy fa-5x"></i>
            </div>
            <h2 class="fw-bold text-success mb-3">Masha'Allah! Completed!</h2>
            
            <!-- first time completion message -->
            <div id="firstTimeMessage" style="display: none;">
                <p class="lead mb-3">You earned:</p>
                <div class="mb-3">
                    <span class="badge bg-warning text-dark fs-3 px-4 py-3">
                        <i class="fas fa-star me-2"></i><span id="xpAmount">0</span> XP
                    </span>
                </div>
                <div id="bonusMessage" style="display: none;" class="alert alert-success mb-3">
                    <strong><i class="fas fa-gift me-2"></i>Challenge Bonus: +<span id="bonusAmount">0</span> XP!</strong>
                </div>
            </div>
            
            <!-- replay message -->
            <div id="replayMessage" style="display: none;">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-redo me-2"></i><strong>Great job!</strong> You've already completed this game.
                </div>
                <p class="text-muted">No XP for replays, but keep practicing!</p>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button onclick="reloadGame()" class="btn btn-primary btn-lg">
                    <i class="fas fa-redo me-2"></i>Play Again
                </button>
                <a href="index.php" class="btn btn-success btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Games
                </a>
            </div>
        </div>
    </div>
</div>

<script>
console.log('=== Game Play Page Loaded ===');
console.log('Game ID: <?= $gameId ?>');
console.log('Already Completed: <?= $alreadyCompleted ? "true" : "false" ?>');

// state management
let xpProcessed = false;
let canAcceptMessages = false;

// wait for iframe to load
document.getElementById('gameIframe').addEventListener('load', function() {
    console.log('Game iframe loaded');
    
    // wait 2 seconds for game JavaScript to initialize
    setTimeout(() => {
        canAcceptMessages = true;
        console.log('Ready to accept gameWon messages');
    }, 2000);
});

// listen for gameWon message from the game iframe
window.addEventListener('message', function(event) {
    console.log('Received message:', event.data);
    
    // validation
    if (!canAcceptMessages) {
        console.warn('Not ready yet - ignoring message');
        return;
    }
    
    if (event.data !== 'gameWon') {
        console.log('Not a gameWon message - ignoring');
        return;
    }
    
    if (xpProcessed) {
        console.warn('Already processed - ignoring duplicate');
        return;
    }
    
    console.log('Valid gameWon message received!');
    xpProcessed = true;
    handleGameCompletion();
});

function handleGameCompletion() {
    console.log('Processing game completion...');
    
    // send AJAX request to award XP
    fetch('../../actions/process_game_win.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'game_id=<?= $gameId ?>'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);
        showVictoryOverlay(data);
    })
    .catch(error => {
        console.error('Error:', error);
        // show overlay even on error
        showVictoryOverlay({ firstTime: false, status: 'error' });
    });
}

function showVictoryOverlay(data) {
    console.log('Showing victory overlay');
    console.log('Data:', data);
    
    const overlay = document.getElementById('victory-overlay');
    const firstTimeMsg = document.getElementById('firstTimeMessage');
    const replayMsg = document.getElementById('replayMessage');
    const xpAmount = document.getElementById('xpAmount');
    const bonusMessage = document.getElementById('bonusMessage');
    const bonusAmount = document.getElementById('bonusAmount');
    
    // hide all messages initially
    firstTimeMsg.style.display = 'none';
    replayMsg.style.display = 'none';
    bonusMessage.style.display = 'none';
    
    if (data.status === 'success' && data.firstTime === true) {
        // first time completion: show XP earned
        console.log('First time completion!');
        firstTimeMsg.style.display = 'block';
        xpAmount.textContent = data.xpAwarded || 0;
        
        // show challenge bonus if earned
        if (data.challengeCompleted && data.bonusXP > 0) {
            console.log('Challenge bonus earned!');
            bonusMessage.style.display = 'block';
            bonusAmount.textContent = data.bonusXP;
        }
    } else {
        // replay: no XP
        console.log('Replay - no XP awarded');
        replayMsg.style.display = 'block';
    }
    
    // show the overlay
    overlay.style.display = 'flex';
}

function reloadGame() {
    console.log('Reloading game...');
    xpProcessed = false;
    canAcceptMessages = false;
    
    // hide overlay
    document.getElementById('victory-overlay').style.display = 'none';
    
    // reload the iframe
    const iframe = document.getElementById('gameIframe');
    iframe.src = iframe.src;
    
    // Re-enable message acceptance after reload
    setTimeout(() => {
        canAcceptMessages = true;
        console.log('Ready for next play');
    }, 2000);
}
</script>

<?php include('../../includes/footer.php'); ?>