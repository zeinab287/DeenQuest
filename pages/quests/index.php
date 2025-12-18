<?php
// start session and role check
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';

// fetch all quests and order by level first, then by subject for better organization.
$sql = "SELECT * FROM Quest ORDER BY level ASC, subject ASC";
$result = $conn->query($sql);

$pageTitle = "All Quests";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold text-success"><i class="fas fa-map-signs me-3"></i>Quest Library</h1>
            <p class="text-muted lead mb-0">Explore all available quests and continue your learning journey.</p>
        </div>
        </div>

    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($quest = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="badge bg-success"><?= htmlspecialchars($quest['subject']) ?></span>
                            <span class="badge bg-light text-dark border">Lvl <?= $quest['level'] ?></span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-truncate" title="<?= htmlspecialchars($quest['title']) ?>">
                                <?= htmlspecialchars($quest['title']) ?>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= htmlspecialchars(substr($quest['description'], 0, 120)) ?>...
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="fw-bold text-warning">
                                <i class="fas fa-star me-1"></i>+<?= $quest['xp_reward'] ?> XP
                            </span>
                            <a href="quest_view.php?quest_id=<?= $quest['quest_id'] ?>" class="btn btn-sm btn-outline-success fw-bold stretched-link">
                                View Quest <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <p class="fw-bold mb-1">No quests are currently available.</p>
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