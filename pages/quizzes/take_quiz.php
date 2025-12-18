<?php
session_start();
require_once '../../config/db.php';

// security and role check
// making sure that only learners can take quizzes.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}

// validate URL param
if (!isset($_GET['quest_id']) || !is_numeric($_GET['quest_id'])) {
    // if missing the ID is missing, send user back to dashboard
    header("Location: ../../learner_dashboard.php"); exit();
}
$questId = intval($_GET['quest_id']);

// fetch quest info 
$qStmt = $conn->prepare("SELECT title FROM Quest WHERE quest_id = ?");
$qStmt->bind_param("i", $questId);
$qStmt->execute();
$questResult = $qStmt->get_result();
if ($questResult->num_rows === 0) {
     header("Location: ../../learner_dashboard.php?error=notfound"); exit();
}
$quest = $questResult->fetch_assoc();
$qStmt->close();

// fetch quiz questions
$sql = "SELECT question_id, question_text, option_a, option_b, option_c, option_d FROM QuizQuestion WHERE quest_id = ? ORDER BY question_id ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $questId);
$stmt->execute();
$questionsResult = $stmt->get_result();
$stmt->close();

$pageTitle = "Quiz: " . $quest['title'];
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
             <div class="text-center mb-5">
                <span class="badge bg-success mb-2">Quiz Time!</span>
                <h1 class="fw-bold"><?= htmlspecialchars($quest['title']) ?></h1>
                 <p class="text-muted">Answer all questions below to complete the quest.</p>
            </div>

             <?php if ($questionsResult->num_rows > 0): ?>
                
                <form action="../../actions/process_quiz.php" method="POST">
                    <input type="hidden" name="quest_id" value="<?= $questId ?>">

                    <?php $count = 1; while($q = $questionsResult->fetch_assoc()): ?>
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">
                                    <span class="text-success me-2">Q<?= $count ?>.</span> 
                                    <?= htmlspecialchars($q['question_text']) ?>
                                </h5>

                                <div class="vstack gap-3">
                                    <label class="form-check p-3 border rounded d-flex align-items-center radio-hover pointer">
                                        <input class="form-check-input me-3 mt-0" type="radio" name="answers[<?= $q['question_id'] ?>]" value="a" required>
                                        <span><?= htmlspecialchars($q['option_a']) ?></span>
                                    </label>
                                    <label class="form-check p-3 border rounded d-flex align-items-center radio-hover pointer">
                                        <input class="form-check-input me-3 mt-0" type="radio" name="answers[<?= $q['question_id'] ?>]" value="b">
                                        <span><?= htmlspecialchars($q['option_b']) ?></span>
                                    </label>
                                    <label class="form-check p-3 border rounded d-flex align-items-center radio-hover pointer">
                                        <input class="form-check-input me-3 mt-0" type="radio" name="answers[<?= $q['question_id'] ?>]" value="c">
                                        <span><?= htmlspecialchars($q['option_c']) ?></span>
                                    </label>
                                    <label class="form-check p-3 border rounded d-flex align-items-center radio-hover pointer">
                                        <input class="form-check-input me-3 mt-0" type="radio" name="answers[<?= $q['question_id'] ?>]" value="d">
                                        <span><?= htmlspecialchars($q['option_d']) ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php $count++; endwhile; ?>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-success btn-lg fw-bold p-3 hover-lift">
                            Submit Answers <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>

             <?php else: ?>
                <div class="alert alert-warning text-center p-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>No Questions Found</h4>
                    <p>It seems this quiz doesn't have any questions yet. Please contact an administrator.</p>
                    <a href="../../learner_dashboard.php" class="btn btn-outline-dark">Back to Dashboard</a>
                </div>
             <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .pointer { cursor: pointer; }
    .radio-hover:hover { background-color: #f8f9fa; border-color: #198754 !important; }
    .form-check-input:checked + span { font-weight: bold; color: #198754; }
    .form-check-input:checked[type=radio] {
        background-color: #198754;
        border-color: #198754;
    }
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
</style>

<?php
$conn->close();
include('../../includes/footer.php');
?>