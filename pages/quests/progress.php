<?php
// start session and role check
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];

// fetch user's quest history
$sql = "SELECT 
            uqp.quest_id,
            q.title, 
            q.subject, 
            q.level, 
            quest.title,
            quest.level
        FROM userquestprogress uqp
        JOIN quest ON uqp.quest_id = quest.quest_id
        WHERE uqp.user_id = ?
        ORDER BY uqp.completed_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My Progress Tracking";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-2">
                <h1 class="fw-bold text-success mb-0 me-3"><i class="fas fa-tasks me-2"></i>My Progress Tracking</h1>
                <a href="progress.php" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    <i class="fas fa-sync-alt me-1"></i>Refresh List
                </a>
            </div>
            <p class="text-muted lead mb-0">A detailed history of all your quest attempts and scores.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Quest Title</th>
                            <th scope="col">Subject</th>
                            <th scope="col" class="text-center">Level</th>
                            <th scope="col">Score & Progress</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-end pe-4">Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                    $score = $row['score'];
                                    $progressClass = 'bg-danger';
                                    if ($score >= 80) { $progressClass = 'bg-success'; } 
                                    elseif ($score >= 60) { $progressClass = 'bg-warning'; }
                                    $passed = $score >= 70;
                                    $statusBadge = $passed ? '<span class="badge bg-success">Passed</span>' : '<span class="badge bg-danger">Failed</span>';
                                ?>
                                <tr>
                                    <td class="fw-bold ps-4"><?= htmlspecialchars($row['title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['subject']) ?></span></td>
                                    <td class="text-center"><?= ucfirst($row['level']) ?></td>
                                    <td style="min-width: 150px;">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2" style="min-width: 40px;"><?= $score ?>%</span>
                                            <div class="progress flex-grow-1 shadow-sm" style="height: 8px;">
                                                <div class="progress-bar <?= $progressClass ?>" role="progressbar" style="width: <?= $score ?>%;" aria-valuenow="<?= $score ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $statusBadge ?></td>
                                    <td class="text-end text-muted small pe-4">
                                        <?= date('M d, Y', strtotime($row['completed_at'])) ?><br>
                                        <?= date('h:i A', strtotime($row['completed_at'])) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="fw-bold mb-1">No quest history found yet.</p>
                                    <p class="small mb-0">Start completing quests to see your progress here!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $stmt->close(); $conn->close(); 
include('../../includes/footer.php'); ?>