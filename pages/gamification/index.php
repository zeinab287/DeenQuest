<?php
// start session and user validation
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];

// fetch user's Current total XP
// get user xp and name
$stmtUsr = $conn->prepare("SELECT xp, name FROM user WHERE user_id = ?");
$stmtUsr->bind_param("i", $userId);
$stmtUsr->execute();
$userResult = $stmtUsr->get_result();
$userData = $userResult->fetch_assoc();
$currentXp = $userData['xp'];
$userName = $userData['name'];
$stmtUsr->close();

// define ranks and calculate current rank
$ranks = [
    0    => "Beginner",
    300  => "Intermediate Learner",
    600  => "Advanced Scholar",
    1000 => "Expert",
    2000 => "Master"
];
// default rank values and next target
$currentRankTitle = "Beginner"; 
$nextRankXp = 300; 
foreach ($ranks as $xpThreshold => $title) {
    if ($currentXp >= $xpThreshold) {
        $currentRankTitle = $title;
    } else {
        $nextRankXp = $xpThreshold;
        break; 
    }
}
// calculate progress to next rank for progress bar
$xpNeededForNext = $nextRankXp - $currentXp;
// percentage calculation
$progressPercentage = ($currentXp / $nextRankXp) * 100;
if ($progressPercentage > 100) $progressPercentage = 100; 


// fetch all badges
$sqlBadges = "SELECT * FROM badge ORDER BY xp_required ASC";
$resultBadges = $conn->query($sqlBadges);
$totalBadges = $resultBadges->num_rows;
$unlockedCount = 0;

$pageTitle = "Gamification Hub";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-5 text-center">
        <div class="col-lg-8 mx-auto">
            <h1 class="fw-bold text-success display-5"><i class="fas fa-trophy me-3"></i>Achievements Hub</h1>
            <p class="lead text-muted">Track your progress, ranks, and badge collection.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-success bg-opacity-10">
                <div class="card-body text-center p-4">
                    <i class="fas fa-star fa-3x text-success mb-3"></i>
                    <h2 class="fw-bold display-4 text-success mb-0"><?= number_format($currentXp) ?></h2>
                    <p class="fw-bold text-uppercase small text-success ls-1">Total XP Earned</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-primary bg-opacity-10">
                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                    <i class="fas fa-medal fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold text-primary mb-1"><?= $currentRankTitle ?></h3>
                    <p class="fw-bold text-uppercase small text-primary ls-1 mb-3">Current Rank</p>
                    
                    <?php if ($nextRankXp > $currentXp): ?>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressPercentage ?>%"></div>
                        </div>
                        <small class="text-muted mt-2"><?= number_format($xpNeededForNext) ?> XP to next rank</small>
                    <?php else: ?>
                        <span class="badge bg-primary">Max Rank Achieved!</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center p-4">
                    <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                    <h2 class="fw-bold display-4 text-warning mb-0" id="unlockedCountDisplay">0</h2>
                    <p class="fw-bold text-uppercase small text-warning ls-1">Badges Unlocked</p>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold border-bottom pb-2 mb-4"><i class="fas fa-award me-2 text-warning"></i>Badge Collection</h3>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
        <?php if ($resultBadges && $resultBadges->num_rows > 0): ?>
            <?php while($badge = $resultBadges->fetch_assoc()): ?>
                <?php 
                    // check if badge unlocked
                    $isUnlocked = $currentXp >= $badge['xp_required'];
                    if ($isUnlocked) { $unlockedCount++; }
                    
                    // styling logic based on unlock status
                    $cardClass = $isUnlocked ? 'border-warning shadow-sm badge-unlocked' : 'border-light bg-light opacity-50 badge-locked';
                    $iconColor = $isUnlocked ? 'text-warning' : 'text-muted';
                    $statusBadge = $isUnlocked 
                        ? '<span class="badge bg-warning text-dark"><i class="fas fa-check me-1"></i>Unlocked</span>' 
                        : '<span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Locked</span>';
                ?>
                <div class="col">
                    <div class="card h-100 text-center p-3 <?= $cardClass ?> position-relative overflow-hidden">
                        <div class="card-body">
                            <i class="fas <?= $badge['icon'] ?> fa-4x <?= $iconColor ?> mb-3"></i>
                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($badge['name']) ?></h5>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($badge['description']) ?></p>
                            
                            <div class="mt-auto">
                                <?= $statusBadge ?>
                                <?php if (!$isUnlocked): ?>
                                    <div class="mt-2 small text-muted fw-bold">Requires <?= number_format($badge['xp_required']) ?> XP</div>
                                <?php endif; ?>
                            </div>
                        </div>
                         <?php if ($isUnlocked): ?>
                            <div class="shine"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
             <p class="text-muted">No badges defined yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('unlockedCountDisplay').innerText = "<?= $unlockedCount ?> / <?= $totalBadges ?>";
</script>

<style>
    .ls-1 { letter-spacing: 1px; }
    .badge-unlocked { transition: transform 0.3s; }
    .badge-unlocked:hover { transform: translateY(-5px); }
    
    /* shine css effect */
    .shine {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-20deg) translateX(-100%);
        transition: transform 0.5s;
    }
    .badge-unlocked:hover .shine {
        transform: skewX(-20deg) translateX(100%);
        transition: transform 0.7s;
    }
</style>

<?php
$conn->close();
include('../../includes/footer.php');
?>