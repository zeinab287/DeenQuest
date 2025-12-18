<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

if (!isset($_GET['quest_id'])) {
    header("Location: manage_quests.php"); exit();
}
$questId = intval($_GET['quest_id']);

// get quest info 
$questSql = "SELECT title FROM quest WHERE quest_id = ?";
$stmtQ = $conn->prepare($questSql);
$stmtQ->bind_param("i", $questId);
$stmtQ->execute();
$questResult = $stmtQ->get_result();
if ($questResult->num_rows === 0) { header("Location: manage_quests.php"); exit(); }
$quest = $questResult->fetch_assoc();
$stmtQ->close();

// get all questions for this quest 
$qSql = "SELECT * FROM quizquestion WHERE quest_id = ? ORDER BY question_id ASC";
$stmt = $conn->prepare($qSql);
$stmt->bind_param("i", $questId);
$stmt->execute();
$questions = $stmt->get_result();
$stmt->close();

$pageTitle = "Manage Quiz: " . $quest['title'];
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-question-circle text-warning me-2"></i>Quiz for: <span class="text-muted"><?= htmlspecialchars($quest['title']) ?></span></h2>
        <div>
            <a href="manage_quests.php" class="btn btn-outline-secondary me-2"><i class="fas fa-arrow-left me-2"></i>Back to Quests</a>
            <a href="add_question.php?quest_id=<?= $questId ?>" class="btn btn-warning fw-bold"><i class="fas fa-plus me-2"></i>Add Question</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if ($questions->num_rows > 0): ?>
                <?php $count = 1; while($q = $questions->fetch_assoc()): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">Q<?= $count ?>. <?= htmlspecialchars($q['question_text']) ?></h5>
                                <ul class="list-unstyled ms-3 mb-0 text-muted small">
                                    <li class="<?= $q['correct_option'] == 'a' ? 'text-success fw-bold' : '' ?>">
                                        A) <?= htmlspecialchars($q['option_a']) ?> 
                                        <?= $q['correct_option'] == 'a' ? '<i class="fas fa-check-circle text-success ms-1"></i>' : '' ?>
                                    </li>
                                    <li class="<?= $q['correct_option'] == 'b' ? 'text-success fw-bold' : '' ?>">
                                        B) <?= htmlspecialchars($q['option_b']) ?>
                                        <?= $q['correct_option'] == 'b' ? '<i class="fas fa-check-circle text-success ms-1"></i>' : '' ?>
                                    </li>
                                    <li class="<?= $q['correct_option'] == 'c' ? 'text-success fw-bold' : '' ?>">
                                        C) <?= htmlspecialchars($q['option_c']) ?>
                                        <?= $q['correct_option'] == 'c' ? '<i class="fas fa-check-circle text-success ms-1"></i>' : '' ?>
                                    </li>
                                    <li class="<?= $q['correct_option'] == 'd' ? 'text-success fw-bold' : '' ?>">
                                        D) <?= htmlspecialchars($q['option_d']) ?>
                                        <?= $q['correct_option'] == 'd' ? '<i class="fas fa-check-circle text-success ms-1"></i>' : '' ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="ms-3">
                                <form method="POST" action="../actions/delete_question_action.php" 
                                      onsubmit="return confirm('Are you sure you want to delete this question?');" 
                                      style="display: inline;">
                                    <input type="hidden" name="question_id" value="<?= $q['question_id'] ?>">
                                    <input type="hidden" name="quest_id" value="<?= $questId ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php $count++; endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted py-4 mb-0">
                    <i class="fas fa-info-circle me-2"></i>No questions added to this quiz yet.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $conn->close(); 
include('../includes/footer.php'); ?>