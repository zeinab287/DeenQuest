<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

// Check for ID and fetch data
if (!isset($_GET['id'])) {
    header("Location: manage_challenges.php"); exit();
}
$challengeId = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM Challenge WHERE challenge_id = ?");
$stmt->bind_param("i", $challengeId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: manage_challenges.php"); exit();
}
$challenge = $result->fetch_assoc();
$stmt->close();

$pageTitle = "Edit Challenge: " . htmlspecialchars($challenge['title']);
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit text-primary me-2"></i>Edit Challenge</h1>
                <a href="manage_challenges.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-primary border-3">
                <div class="card-body p-4">
                    <form action="../actions/update_challenge_action.php" method="POST">
                        <input type="hidden" name="challenge_id" value="<?= $challenge['challenge_id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Challenge Title</label>
                            <input type="text" class="form-control" name="title" required value="<?= htmlspecialchars($challenge['title']) ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Goal Type</label>
                                <select class="form-select" name="goal_type" required>
                                    <option value="xp_earned" <?= $challenge['goal_type'] == 'xp_earned' ? 'selected' : '' ?>>Total XP Earned</option>
                                    <option value="quests_completed" <?= $challenge['goal_type'] == 'quests_completed' ? 'selected' : '' ?>>Number of Quests Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Target Value</label>
                                <input type="number" class="form-control" name="target_value" min="1" required value="<?= $challenge['target_value'] ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required value="<?= $challenge['start_date'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control" name="end_date" required value="<?= $challenge['end_date'] ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">XP Reward</label>
                                <input type="number" class="form-control" name="xp_reward" min="10" step="10" required value="<?= $challenge['xp_reward'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" name="is_active" required>
                                    <option value="1" <?= $challenge['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= $challenge['is_active'] == 0 ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($challenge['description']) ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Update Challenge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>