<?php
// start session and check user role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

// fetch active challenges that haven't ended yet
$sql = "SELECT * FROM Challenge WHERE is_active = 1 AND end_date >= ? ORDER BY end_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$challengesResult = $stmt->get_result();

$pageTitle = "Active Challenges";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold text-danger"><i class="fas fa-trophy me-3"></i>Active Challenges</h1>
            <p class="text-muted lead mb-0">Complete these time-limited goals to earn bonus rewards!</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($challengesResult && $challengesResult->num_rows > 0): ?>
            <?php while($challenge = $challengesResult->fetch_assoc()): ?>
                <?php
                    // check if user has already completed this challenge
                    $progSql = "SELECT completed_at FROM UserChallengeProgress WHERE user_id = ? AND challenge_id = ?";
                    $stmtP = $conn->prepare($progSql);
                    $stmtP->bind_param("ii", $userId, $challenge['challenge_id']);
                    $stmtP->execute();
                    $progResult = $stmtP->get_result();
                    $isComplete = $progResult->num_rows > 0;
                    $completedAt = $isComplete ? $progResult->fetch_assoc()['completed_at'] : null;
                    $stmtP->close();

                    // determine styling and link based on activity type and completion status
                    $cardClass = $isComplete ? 'border-success bg-success bg-opacity-10' : 'border-danger';
                    
                    $startLink = "#";
                    $linkText = "Start Challenge";
                    $linkIcon = "fa-play";

                    if ($challenge['activity_type'] === 'quest') {
                        $startLink = "../quests/quest_view.php?quest_id=" . $challenge['activity_id'];
                    } elseif ($challenge['activity_type'] === 'game') {
                         $startLink = "../games/play.php?game_id=" . $challenge['activity_id'];
                    }
                ?>
                
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm border-0 border-start border-4 <?= $cardClass ?>">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($challenge['title']) ?></h4>
                                    <span class="badge bg-secondary me-2">
                                        <i class="fas <?= $challenge['activity_type'] == 'quest' ? 'fa-map-signs' : 'fa-gamepad' ?> me-1"></i>
                                        <?= ucfirst($challenge['activity_type']) ?>
                                    </span>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-star me-1"></i>Bonus: +<?= $challenge['xp_reward'] ?> XP
                                    </span>
                                </div>
                                <?php if($isComplete): ?>
                                    <div class="text-end text-success">
                                        <span class="badge bg-success fs-6 mb-1"><i class="fas fa-check-circle me-1"></i>Completed!</span>
                                        <div class="small"><?= date('M d, Y', strtotime($completedAt)) ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-end text-danger small fw-bold">
                                        <div>Ends: <?= date('M d, Y', strtotime($challenge['end_date'])) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-muted flex-grow-1"><?= htmlspecialchars($challenge['description']) ?></p>

                            <div class="mt-4">
                                <?php if($isComplete): ?>
                                    <button class="btn btn-success w-100 fw-bold" disabled>
                                        <i class="fas fa-check me-2"></i>Challenge Complete
                                    </button>
                                <?php else: ?>
                                    <a href="<?= $startLink ?>" class="btn btn-danger w-100 fw-bold hover-lift">
                                        <i class="fas <?= $linkIcon ?> me-2"></i><?= $linkText ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
             <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-trophy fa-3x mb-3 opacity-50"></i>
                    <p class="fw-bold mb-1">No active challenges right now.</p>
                    <p class="small mb-0">Check back soon for new goals!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
</style>

<?php
$stmt->close();
$conn->close();
include('../../includes/footer.php');
?>