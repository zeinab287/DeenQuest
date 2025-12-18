<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

// ensure a quest_id is provided
if (!isset($_GET['quest_id'])) {
    header("Location: manage_quests.php"); exit();
}
$questId = intval($_GET['quest_id']);

// get quest title for context
$questSql = "SELECT title FROM Quest WHERE quest_id = ?";
$stmtQ = $conn->prepare($questSql);
$stmtQ->bind_param("i", $questId);
$stmtQ->execute();
$questResult = $stmtQ->get_result();
if ($questResult->num_rows === 0) { header("Location: manage_quests.php"); exit(); }
$quest = $questResult->fetch_assoc();
$stmtQ->close();

$pageTitle = "Add Question to: " . htmlspecialchars($quest['title']);
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus-circle text-warning me-2"></i>Add New Question</h1>
                <a href="manage_quiz.php?quest_id=<?= $questId ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Quiz</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    For Quest: <?= htmlspecialchars($quest['title']) ?>
                </div>
                <div class="card-body p-4">
                    <form action="../actions/add_question_action.php" method="POST">
                        <input type="hidden" name="quest_id" value="<?= $questId ?>">

                        <div class="mb-4">
                            <label for="question_text" class="form-label fw-bold">Question Text</label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required placeholder="e.g., What is the first pillar of Islam?"></textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="option_a" class="form-label fw-bold">Option A</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">A</span>
                                    <input type="text" class="form-control" id="option_a" name="option_a" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_b" class="form-label fw-bold">Option B</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">B</span>
                                    <input type="text" class="form-control" id="option_b" name="option_b" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_c" class="form-label fw-bold">Option C</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">C</span>
                                    <input type="text" class="form-control" id="option_c" name="option_c" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_d" class="form-label fw-bold">Option D</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">D</span>
                                    <input type="text" class="form-control" id="option_d" name="option_d" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="correct_option" class="form-label fw-bold text-success">Correct Answer</label>
                            <select class="form-select border-success" id="correct_option" name="correct_option" required>
                                <option value="" selected disabled>Select the correct option...</option>
                                <option value="a">Option A</option>
                                <option value="b">Option B</option>
                                <option value="c">Option C</option>
                                <option value="d">Option D</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Add Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>