<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

$pageTitle = "Manage Challenges";
include('../includes/header.php');

// fetch challenges ordered by 'newest end date' first
$sql = "SELECT * FROM Challenge ORDER BY end_date DESC";
$result = $conn->query($sql);
$today = date('Y-m-d');
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-trophy text-danger me-2"></i>Manage Challenges</h1>
        <a href="add_challenge.php" class="btn btn-danger fw-bold">
            <i class="fas fa-plus-circle me-2"></i>Create Challenge
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th scope="col" class="ps-4">Title / Goal</th>
                            <th scope="col" class="text-center">Timeframe</th>
                            <th scope="col" class="text-center">Reward</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                    // determine status
                                    $isActive = $row['is_active'] == 1;
                                    $isExpired = $today > $row['end_date'];
                                    $statusBadge = '';
                                    if (!$isActive) {
                                        $statusBadge = '<span class="badge bg-secondary">Disabled</span>';
                                    } elseif ($isExpired) {
                                        $statusBadge = '<span class="badge bg-dark">Expired</span>';
                                    } else {
                                        $statusBadge = '<span class="badge bg-success">Active</span>';
                                    }

                                    // format goal text nicely with proper checks
                                    $goalText = '';
                                    if (isset($row['target_value']) && isset($row['goal_type'])) {
                                        $goalText = $row['target_value'] . " ";
                                        $goalText .= ($row['goal_type'] == 'xp_earned') ? 'XP Earned' : 'Quests Completed';
                                    } else {
                                        $goalText = 'N/A';
                                    }
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold d-block"><?= htmlspecialchars($row['title']) ?></span>
                                        <small class="text-muted">Goal: <?= $goalText ?></small>
                                    </td>
                                    <td class="text-center small">
                                        <div>Start: <?= date('M d, Y', strtotime($row['start_date'])) ?></div>
                                        <div class="text-muted">End: <?= date('M d, Y', strtotime($row['end_date'])) ?></div>
                                    </td>
                                    <td class="text-center text-warning fw-bold">
                                        <i class="fas fa-star me-1"></i><?= $row['xp_reward'] ?> XP
                                    </td>
                                    <td class="text-center"><?= $statusBadge ?></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_challenge.php?id=<?= $row['challenge_id'] ?>" 
                                               class="btn btn-outline-primary" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../actions/delete_challenge_action.php?id=<?= $row['challenge_id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               title="Delete" 
                                               onclick="return confirm('Are you sure you want to delete this challenge?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-trophy fa-3x mb-3 opacity-50"></i>
                                    <p class="fw-bold mb-1">No challenges found.</p>
                                    <p class="small mb-0">Create one to motivate your learners!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); include('../includes/footer.php'); ?>