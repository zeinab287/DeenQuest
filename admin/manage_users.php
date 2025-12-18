<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

$pageTitle = "Manage Users";
include('../includes/header.php');

// fetch all users: newest first
$sql = "SELECT user_id, name, email, role, xp, created_at FROM User ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users text-primary me-2"></i>Manage Users</h1>
        </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th scope="col" class="ps-4">Name / Email</th>
                            <th scope="col" class="text-center">Role</th>
                            <th scope="col" class="text-center">Total XP</th>
                            <th scope="col" class="text-center">Joined Date</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php 
                                    $roleBadge = ($row['role'] === 'admin') 
                                        ? '<span class="badge bg-danger">Admin</span>' 
                                        : '<span class="badge bg-success">Learner</span>';
                                    
                                    // prevent an admin from deleting themselves
                                    $isSelf = ($row['user_id'] == $_SESSION['user_id']);
                                    $deleteDisabled = $isSelf ? 'disabled' : '';
                                    $deleteTitle = $isSelf ? 'You cannot delete yourself' : 'Delete User';
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold d-block"><?= htmlspecialchars($row['name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                                    </td>
                                    <td class="text-center"><?= $roleBadge ?></td>
                                    <td class="text-center fw-bold text-primary"><?= number_format($row['xp']) ?></td>
                                    <td class="text-center small text-muted">
                                        <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-outline-primary" title="Edit User"><i class="fas fa-edit"></i></a>
                                            <?php if (!$isSelf): ?>
                                            <a href="../actions/delete_user_action.php?id=<?= $row['user_id'] ?>" class="btn btn-outline-danger" title="Delete User" onclick="return confirm('Are you sure? This will delete all their progress, reflections, and quiz history regardless of foreign keys.');"><i class="fas fa-trash-alt"></i></a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary" disabled title="You cannot delete yourself."><i class="fas fa-trash-alt"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                    <p class="fw-bold mb-1">No users found.</p>
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