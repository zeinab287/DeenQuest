<?php
// start the session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
$pageTitle = "Add New Quest";
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus-circle text-success me-2"></i>Add New Quest</h1>
                <a href="manage_quests.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="../actions/add_quest_action.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Quest Title</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., The Pillars of Faith">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="subject" class="form-label fw-bold">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" list="subjectOptions" required placeholder="e.g., Aqeedah">
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
                                <input type="number" class="form-control" id="level" name="level" min="1" value="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="xp_reward" class="form-label fw-bold">XP Reward</label>
                                <input type="number" class="form-control" id="xp_reward" name="xp_reward" min="10" step="10" value="100" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Quest Description / Learning Content</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required placeholder="Write the main learning content for this quest here..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg fw-bold">Create Quest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>