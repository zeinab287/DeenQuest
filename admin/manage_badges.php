<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

$pageTitle = "Manage Badges";
include('../includes/header.php');

// fetch all badges
$sql = "SELECT * FROM badge ORDER BY xp_required ASC";
$result = $conn->query($sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-award text-warning me-2"></i>Manage Badges</h1>
        <a href="add_badge.php" class="btn btn-warning fw-bold">
            <i class="fas fa-plus-circle me-2"></i>Add New Badge
        </a>
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
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-warning text-dark">
                        <tr>
                            <th scope="col" class="text-center ps-4">Icon</th>
                            <th scope="col">Badge Name & Description</th>
                            <th scope="col" class="text-center">XP Required</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center ps-4">
                                        <i class="fas <?= htmlspecialchars($row['icon']) ?> fa-2x text-warning"></i>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block"><?= htmlspecialchars($row['name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($row['description']) ?></small>
                                    </td>
                                    <td class="text-center fw-bold"><?= number_format($row['xp_required']) ?> XP</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_badge.php?id=<?= $row['badge_id'] ?>" class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="../actions/delete_badge_action.php?id=<?= $row['badge_id'] ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this badge? Users will no longer see it.');"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-award fa-3x mb-3 opacity-50"></i>
                                    <p class="fw-bold mb-1">No badges defined yet.</p>
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