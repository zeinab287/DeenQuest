<?php
// start the session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
$pageTitle = "Add New Game";
require_once '../config/db.php';

// fetch unique types for datalist
$existingTypes = [];
$typeSql = "SELECT DISTINCT type FROM game WHERE type IS NOT NULL AND type != '' ORDER BY type ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult) {
    while ($row = $typeResult->fetch_assoc()) {
        if (!empty($row['type'])) {
            $existingTypes[] = $row['type'];
        }
    }
}

include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus-circle text-warning me-2"></i>Define New Game</h1>
                <a href="manage_games.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-warning border-3">
                <div class="card-body p-4">
                    <form action="../actions/add_game_action.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Game Title</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Prophets Memory Match">
                        </div>
                        
                        <div class="mb-4">
                            <label for="type" class="form-label fw-bold">Game Type</label>
                            <input class="form-control" list="gameTypeOptions" id="type" name="type" required placeholder="Select or type a new type (e.g., 'memory', 'quiz', 'sorting')">
                            <datalist id="gameTypeOptions">
                                <?php foreach ($existingTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <div class="form-text text-muted">Choose an existing type from the list or type a completely new one.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="subject" class="form-label fw-bold">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" list="subjectOptions" required placeholder="e.g., Islamic Studies">
                                <datalist id="subjectOptions">
                                    <option value="Islamic Studies"></option>
                                    <option value="Arabic Language"></option>
                                    <option value="Quran"></option>
                                    <option value="Hadith"></option>
                                    <option value="Aqeedah (Belief)"></option>
                                    <option value="Fiqh (Jurisprudence)"></option>
                                    <option value="Seerah (History)"></option>
                                    <option value="General Knowledge"></option>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label for="difficulty_level" class="form-label fw-bold">Difficulty Level</label>
                                <input type="number" class="form-control" id="difficulty_level" name="difficulty_level" min="1" max="5" value="1" required>
                                <div class="form-text text-muted">1 = Easy, 5 = Very Hard</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="xp_reward" class="form-label fw-bold">XP Reward</label>
                            <input type="number" class="form-control" id="xp_reward" name="xp_reward" min="10" step="10" value="50" required>
                        </div>

                        <div class="mb-3">
                            <label for="game_file_path" class="form-label fw-bold">Game File Path (Relative URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-folder"></i></span>
                                <input type="text" class="form-control" id="game_file_path" name="game_file_path" required placeholder="assets/games/my_game/index.html">
                            </div>
                            <div class="form-text">This is the path to the main HTML file that launches the game.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Short Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Briefly describe the game objective..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Define Game</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>