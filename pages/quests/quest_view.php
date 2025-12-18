<?php
// start session and role check
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';

// validate URL parameter
if (!isset($_GET['quest_id']) || !is_numeric($_GET['quest_id'])) {
    // if no valid ID provided, redirect back to the dashboard or quest list
    header("Location: index.php"); exit();
}
$questId = intval($_GET['quest_id']);

// fetch quest details from database
$sql = "SELECT * FROM Quest WHERE quest_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $questId);
$stmt->execute();
$result = $stmt->get_result();

// check if quest exists
if ($result->num_rows === 0) {
    // quest not found in DB
    header("Location: index.php?error=questnotfound"); exit();
}
$quest = $result->fetch_assoc();
$stmt->close();

// set page title for header
$pageTitle = $quest['title'];
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="mb-4">
        <a href="index.php" class="text-decoration-none text-muted mb-3 d-inline-block">
            <i class="fas fa-arrow-left me-2"></i>Back to All Quests
        </a>
        <div class="d-flex align-items-center flex-wrap">
            <h1 class="fw-bold text-success me-3 mb-0Display-4"><?= htmlspecialchars($quest['title']) ?></h1>
            <span class="badge bg-success fs-6"><?= htmlspecialchars($quest['subject']) ?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                 <div class="card-header bg-white py-3">
                    <h4 class="fw-bold mb-0"><i class="fas fa-book-open text-primary me-2"></i>Learning Content</h4>
                </div>
                <div class="card-body p-4 content-body">
                    <?php echo nl2br(htmlspecialchars($quest['description'])); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
             <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Quest Details</h5>
                     <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-layer-group text-muted me-2"></i>Difficulty Level</span>
                            <span class="fw-bold">Lvl <?= $quest['level'] ?></span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-star text-warning me-2"></i>XP Reward</span>
                            <span class="fw-bold text-warning">+<?= $quest['xp_reward'] ?> XP</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow border-success text-center">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-success mb-3">Ready to test your knowledge?</h4>
                    <p class="text-muted mb-4">Complete the quiz to earn your XP reward!</p>
                    
                    <a href="../quizzes/take_quiz.php?quest_id=<?= $quest['quest_id'] ?>" class="btn btn-success btn-lg w-100 fw-bold hover-lift">
                        <i class="fas fa-play-circle me-2"></i>Start Quiz Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-body {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #333;
    }
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
</style>

<?php
$conn->close();
include('../../includes/footer.php');
?>