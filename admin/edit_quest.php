<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

// get quest ID and fetch data
if (!isset($_GET['id'])) {
    header("Location: manage_quests.php"); exit();
}
$questId = intval($_GET['id']);
$sql = "SELECT * FROM Quest WHERE quest_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $questId);
$stmt->execute();
$result = $stmt->get_result();

// check if quest exists
if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Quest not found.";
    header("Location: manage_quests.php"); exit();
}
$quest = $result->fetch_assoc();
$stmt->close();

$pageTitle = "Edit Quest: " . htmlspecialchars($quest['title']);
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit text-primary me-2"></i>Edit Quest</h1>
                <a href="manage_quests.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="../actions/update_quest_action.php" method="POST">
                        <input type="hidden" name="quest_id" value="<?= $quest['quest_id'] ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Quest Title</label>
                            <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($quest['title']) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="subject" class="form-label fw-bold">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" list="subjectOptions" required value="<?= htmlspecialchars($quest['subject']) ?>">
                                <datalist id="subjectOptions">
                                    <option value="Aqeedah (Belief)"></option>
                                    <option value="Fiqh (Jurisprudence)"></option>
                                    <option value="Seerah (History)"></option>
                                    <option value="Hadith"></option>
                                    <option value="Akhlaq (Manners)"></option>
                                </datalist>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="level" class="form-label fw-bold">Difficulty Level</label>
                                <input type="number" class="form-control" id="level" name="level" min="1" required value="<?= $quest['level'] ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="xp_reward" class="form-label fw-bold">XP Reward</label>
                                <input type="number" class="form-control" id="xp_reward" name="xp_reward" min="10" step="10" required value="<?= $quest['xp_reward'] ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Quest Description / Learning Content</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required><?= htmlspecialchars($quest['description']) ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Update Quest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>