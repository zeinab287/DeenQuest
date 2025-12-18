<?php
session_start();
require_once '../../config/db.php';

// security and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}

// validate input parameters from URL
if (!isset($_GET['quest_id']) || !isset($_GET['score']) || !isset($_GET['passed']) || !isset($_GET['xp'])) {
    // when missing info, redirect to dashboard
    header("Location: ../../learner_dashboard.php"); exit();
}

$questId = intval($_GET['quest_id']);
$score = intval($_GET['score']);
$passed = intval($_GET['passed']) === 1;
$xpEarned = intval($_GET['xp']);

// fetch quest title 
$stmt = $conn->prepare("SELECT title FROM Quest WHERE quest_id = ?");
$stmt->bind_param("i", $questId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header("Location: ../../learner_dashboard.php"); exit(); }
$quest = $result->fetch_assoc();
$stmt->close();

// determine styling based on result
$statusColor = $passed ? 'success' : 'danger';
$statusIcon = $passed ? 'fa-check-circle' : 'fa-times-circle';
$statusTitle = $passed ? 'Quest Complete!' : 'Quest Not Complete Yet';
$statusMessage = $passed 
    ? "Great job! You've mastered this topic." 
    : "Don't give up! Review the content and try again to earn your XP.";

$pageTitle = "Quiz Results";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0 text-center mb-4">
                <div class="card-body p-5">
                    <i class="fas <?= $statusIcon ?> text-<?= $statusColor ?> display-1 mb-4 animated-icon"></i>
                    
                    <h1 class="fw-bold mb-3"><?= $statusTitle ?></h1>
                    <p class="text-muted mb-4 lead"><?= $statusMessage ?></p>
                    
                    <span class="badge bg-light text-dark border px-3 py-2 mb-4">
                        Quest: <?= htmlspecialchars($quest['title']) ?>
                    </span>

                    <div class="row justify-content-center mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded bg-<?= $statusColor ?> bg-opacity-10 border border-<?= $statusColor ?>">
                                <h6 class="text-<?= $statusColor ?> fw-bold mb-1">YOUR SCORE</h6>
                                <h2 class="display-4 fw-bold text-<?= $statusColor ?> mb-0"><?= $score ?>%</h2>
                            </div>
                        </div>
                    </div>

                    <?php if ($passed && $xpEarned > 0): ?>
                        <div class="alert alert-warning border-warning d-inline-flex align-items-center px-4 py-3 mb-4 animated-pop">
                            <i class="fas fa-star fa-2x text-warning me-3"></i>
                            <div class="text-start">
                                <h5 class="fw-bold mb-0">+<?= $xpEarned ?> XP Earned!</h5>
                                <small>Your total XP has been updated.</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2 d-md-block">
                        <a href="../../learner_dashboard.php" class="btn btn-outline-secondary btn-lg px-4 fw-bold me-md-2">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <?php if ($passed): ?>
                            <a href="../quests/index.php" class="btn btn-success btn-lg px-4 fw-bold">
                                Next Quest <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <a href="../quests/quest_view.php?quest_id=<?= $questId ?>" class="btn btn-danger btn-lg px-4 fw-bold">
                                <i class="fas fa-redo me-2"></i>Retry Quest
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animated-icon {
        animation: pulse 1.5s infinite;
    }
    .animated-pop {
        animation: popIn 0.5s ease-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        80% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<?php
$conn->close();
include('../../includes/footer.php');
?>