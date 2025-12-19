<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }
require_once '../config/db.php';

// fetch all quests and games for the dropdown lists
$quests = $conn->query("SELECT quest_id, title FROM Quest ORDER BY title ASC");
// fetch games for selection
$games = $conn->query("SELECT game_id, title FROM game ORDER BY title ASC");

$pageTitle = "Create Challenge";
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus-circle text-danger me-2"></i>Create Challenge</h1>
                <a href="manage_challenges.php" class="btn btn-outline-secondary">Back to List</a>
            </div>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-danger border-3">
                <div class="card-body p-4">
                    <form action="../actions/add_challenge_action.php" method="POST" id="challengeForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Challenge Title</label>
                            <input type="text" class="form-control" name="title" required placeholder="e.g., The Ramadan Quest">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Activity Type</label>
                            <select class="form-select" name="activity_type" id="activityTypeSelect" required onchange="toggleActivitySelect()">
                                <option value="" selected disabled>Choose type...</option>
                                <option value="quest">Complete a Quest</option>
                                <option value="game">Play a Game</option>
                            </select>
                        </div>

                        <div class="mb-3" id="questSelectGroup" style="display:none;">
                            <label class="form-label fw-bold">Select Quest</label>
                            <select class="form-select" name="quest_id" id="questIdSelect">
                                <option value="">Choose a quest...</option>
                                <?php while($q = $quests->fetch_assoc()): ?>
                                    <option value="<?= $q['quest_id'] ?>"><?= htmlspecialchars($q['title']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3" id="gameSelectGroup" style="display:none;">
                            <label class="form-label fw-bold">Select Game</label>
                            <select class="form-select" name="game_id" id="gameIdSelect">
                                <option value="">Choose a game...</option>
                                <?php while($g = $games->fetch_assoc()): ?>
                                    <option value="<?= $g['game_id'] ?>"><?= htmlspecialchars($g['title']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bonus XP Reward</label>
                            <input type="number" class="form-control" name="xp_reward" min="10" step="10" value="100" required>
                            <div class="form-text">This is in addition to the activity's normal XP.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="3" required placeholder="e.g., Complete this special quest before the deadline to earn a bonus!"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg fw-bold">Create Challenge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleActivitySelect() {
    const type = document.getElementById('activityTypeSelect').value;
    const questGroup = document.getElementById('questSelectGroup');
    const gameGroup = document.getElementById('gameSelectGroup');
    const questSelect = document.getElementById('questIdSelect');
    const gameSelect = document.getElementById('gameIdSelect');

    questGroup.style.display = (type === 'quest') ? 'block' : 'none';
    gameGroup.style.display = (type === 'game') ? 'block' : 'none';
    
    // make the visible one required, disable the other
    if (type === 'quest') {
        questSelect.required = true;
        gameSelect.required = false;
        gameSelect.value = "";
    } else if (type === 'game') {
        gameSelect.required = true;
        questSelect.required = false;
        questSelect.value = "";
    }
}
</script>

<?php $conn->close(); include('../includes/footer.php'); ?>