<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// tell the browser never to store a local copy of this page.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config/db.php';

//ensure user is logged in and is a learner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    $_SESSION['error_message'] = 'Please log in to access the dashboard.';
    header('Location: login.php');
    exit();
}

// fetch user data
$userId   = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$currentXp = isset($_SESSION['xp']) ? intval($_SESSION['xp']) : 0;

// determine rank based on XP
$rankTitle = 'Beginner';
if ($currentXp >= 600) {
    $rankTitle = 'Intermediate Learner';
} elseif ($currentXp >= 300) {
    $rankTitle = 'Eager Learner';
}

// get earned badges count
$earnedBadges = 0;
$stmt = $conn->prepare(
    'SELECT COUNT(*) AS badge_count FROM Badge WHERE xp_required <= ?'
);
if ($stmt) {
    $stmt->bind_param('i', $currentXp);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $earnedBadges = $result->fetch_assoc()['badge_count'];
    }
    $stmt->close();
}

// get available quests
$questsResult = $conn->query(
    'SELECT * FROM Quest ORDER BY level ASC LIMIT 9'
);

include 'includes/header.php';
?>

<div class="row mb-4 border-bottom pb-3">
    <div class="col-md-8">
        <h2 class="fw-bold text-success mb-0">
            Welcome back, <?= htmlspecialchars($userName) ?>!
        </h2>
        <p class="text-muted mt-1">Ready to continue your journey?</p>
    </div>
    <div class="col-md-4 text-end d-none d-md-block">
        <span class="text-muted small"><?= date('l, F j, Y') ?></span>
    </div>
</div>

<!-- statistics cards -->
<div class="row text-center mb-5">
    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
            <h2 class="fw-bold text-success mb-1"><?= $currentXp ?></h2>
            <span class="small fw-bold text-success">TOTAL XP</span>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-warning bg-opacity-10">
            <h2 class="fw-bold text-warning mb-1"><?= $earnedBadges ?></h2>
            <span class="small fw-bold text-warning">BADGES UNLOCKED</span>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-primary bg-opacity-10">
            <h4 class="fw-bold text-primary mb-1"><?= $rankTitle ?></h4>
            <span class="small fw-bold text-primary">CURRENT RANK</span>
        </div>
    </div>
</div>

<!-- available quests section -->
<h4 class="fw-bold text-success mb-3">
    <i class="fas fa-map-signs me-2"></i>Available Quests
</h4>

<div class="row">
    <?php if ($questsResult && $questsResult->num_rows > 0): ?>
        <?php while ($quest = $questsResult->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-success">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">
                            <?= htmlspecialchars($quest['subject']) ?>
                        </span>
                        <small class="text-muted">Lvl <?= htmlspecialchars($quest['level']) ?></small>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">
                            <?= htmlspecialchars($quest['title']) ?>
                        </h6>
                        <p class="small text-muted">
                            <?= htmlspecialchars(substr($quest['description'], 0, 100)) ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-warning">
                            <i class="fas fa-star me-1"></i>+<?= htmlspecialchars($quest['xp_reward']) ?> XP
                        </span>
                        <a href="pages/quests/quest_view.php?quest_id=<?= htmlspecialchars($quest['quest_id']) ?>"
                           class="btn btn-sm btn-success">
                            <i class="fas fa-play me-1"></i>Start
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No quests available at the moment. Check back soon!
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- quick actions -->
<div class="row mt-5">
    <div class="col-12">
        <h4 class="fw-bold text-success mb-3">
            <i class="fas fa-bolt me-2"></i>Quick Actions
        </h4>
    </div>
    <div class="col-md-3 mb-3">
        <a href="pages/quizzes/index.php" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm border-0 hover-lift">
                <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                <h6 class="fw-bold">Take a Quiz</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="pages/games/by_subject.php" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm border-0 hover-lift">
                <i class="fas fa-gamepad fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold">Play Games</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="pages/gamification/index.php" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm border-0 hover-lift">
                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                <h6 class="fw-bold">View Badges</h6>
            </div>
        </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="pages/reflection/index.php" class="text-decoration-none">
            <div class="card text-center p-4 shadow-sm border-0 hover-lift">
                <i class="fas fa-book-open fa-3x text-success mb-3"></i>
                <h6 class="fw-bold">My Reflections</h6>
            </div>
        </a>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>