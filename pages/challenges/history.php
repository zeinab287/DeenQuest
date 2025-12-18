<?php
// start session and check user role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];

// fetch completed challenges by joining UserChallengeProgress with the Challenge table
$sql = "SELECT c.title, c.description, c.xp_reward, ucp.completed_at, c.activity_type
        FROM UserChallengeProgress ucp
        JOIN Challenge c ON ucp.challenge_id = c.challenge_id
        WHERE ucp.user_id = ?
        ORDER BY ucp.completed_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My Challenge History";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold text-success"><i class="fas fa-clipboard-check me-3"></i>My Challenge History</h1>
            <p class="text-muted lead mb-0">A record of all the time-limited challenges you have conquered.</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm border-0 border-start border-4 border-success bg-success bg-opacity-10">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="fw-bold mb-0"><?= htmlspecialchars($row['title']) ?></h4>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Completed</span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-secondary me-1">
                                    <i class="fas <?= $row['activity_type'] == 'quest' ? 'fa-map-signs' : 'fa-gamepad' ?> me-1"></i>
                                    <?= ucfirst($row['activity_type']) ?>
                                </span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i>+<?= $row['xp_reward'] ?> XP Earned
                                </span>
                            </div>

                            <p class="text-muted"><?= htmlspecialchars($row['description']) ?></p>

                            <div class="text-end text-muted small fw-bold mt-3">
                                Completed on: <?= date('M d, Y', strtotime($row['completed_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
             <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                    <p class="fw-bold mb-1">You haven't completed any challenges yet.</p>
                    <a href="index.php" class="btn btn-sm btn-danger mt-2">View Active Challenges</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include('../../includes/footer.php');
?>