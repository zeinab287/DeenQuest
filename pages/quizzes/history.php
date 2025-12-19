<?php
// start session and authenticate user
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];

// fetch user's quiz history

$sql = "SELECT 
            uqp.quest_id,
            q.title, 
            q.subject, 
            uqp.score, 
            quest.title,
        quest.xp_reward AS total_xp_possible
        FROM userquestprogress uqp
        JOIN quest ON uqp.quest_id = quest.quest_id
        WHERE uqp.user_id = ?
        ORDER BY uqp.completed_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My Results History";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-2">
                <h1 class="fw-bold text-success mb-0 me-3"><i class="fas fa-history me-2"></i>My Results History</h1>
                <a href="history.php" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    <i class="fas fa-sync-alt me-1"></i>Refresh List
                </a>
            </div>
            <p class="text-muted lead mb-0">Review your past performance on all quizzes.</p>
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
                            <th scope="col" class="ps-4">Quiz/Quest Title</th>
                            <th scope="col">Subject</th>
                            <th scope="col" class="text-center">Score</th>
                            <th scope="col" class="text-center">Outcome</th>
                            <th scope="col" class="text-center">XP Gained</th>
                            <th scope="col" class="text-end pe-4">Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                    $score = $row['score'];
                                    $passed = $score >= 70;
                                    $outcomeBadge = $passed ? '<span class="badge bg-success">Passed</span>' : '<span class="badge bg-danger">Failed</span>';
                                    $xpClass = $row['xp_earned'] > 0 ? 'text-success fw-bold' : 'text-muted';
                                ?>
                                <tr>
                                    <td class="fw-bold ps-4"><?= htmlspecialchars($row['title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['subject']) ?></span></td>
                                    <td class="text-center fw-bold"><?= $score ?>%</td>
                                    <td class="text-center"><?= $outcomeBadge ?></td>
                                    <td class="text-center <?= $xpClass ?>">+<?= $row['xp_earned'] ?> XP</td>
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
                                    <p class="fw-bold mb-1">No results found yet.</p>
                                    <p class="small mb-0">Complete quizzes to see your history here!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $stmt->close(); 
$conn->close(); 
include('../../includes/footer.php'); ?>