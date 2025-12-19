<?php
session_start();
// security and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';

// fetch all quests that have quizzes
$sql = "SELECT * FROM quest q WHERE EXISTS (SELECT 1 FROM quizquestion qq WHERE qq.quest_id = q.quest_id) ORDER BY q.level ASC, q.subject ASC";
$result = $conn->query($sql);

$pageTitle = "All Available Quizzes";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold text-success"><i class="fas fa-vial me-3"></i>Quiz Library</h1>
            <p class="text-muted lead mb-0">Test your knowledge with these available quizzes.</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($quest = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-body text-center p-4 d-flex flex-column">
                            <div class="mb-3">
                                <span class="badge bg-success"><?= htmlspecialchars($quest['subject']) ?></span>
                                <span class="badge bg-light text-dark border ms-1">Lvl <?= $quest['level'] ?></span>
                            </div>
                            <h5 class="card-title fw-bold text-truncate mb-3" title="<?= htmlspecialchars($quest['title']) ?>">
                                <?= htmlspecialchars($quest['title']) ?>
                            </h5>
                            <div class="mt-auto">
                                <span class="d-block fw-bold text-warning mb-3">
                                    <i class="fas fa-star me-1"></i>Possible +<?= $quest['xp_reward'] ?> XP
                                </span>
                                <a href="take_quiz.php?quest_id=<?= $quest['quest_id'] ?>" class="btn btn-success w-100 fw-bold stretched-link">
                                    <i class="fas fa-play-circle me-2"></i>Take Quiz
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <p class="fw-bold mb-1">No quizzes are currently available.</p>
                    <p class="small mb-0">Please check back later!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>

<?php
$conn->close();
include('../../includes/footer.php');
?>