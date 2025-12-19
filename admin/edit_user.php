<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
require_once '../config/db.php';

// fetch user data
if (!isset($_GET['id'])) { header("Location: manage_users.php"); exit(); }
$userIdToEdit = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userIdToEdit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header("Location: manage_users.php"); exit(); }
$user = $result->fetch_assoc();
$stmt->close();

$pageTitle = "Edit User: " . htmlspecialchars($user['name']);
include('../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-user-edit text-primary me-2"></i>Edit User</h1>
                <a href="manage_users.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-primary border-3">
                <div class="card-body p-4">
                    <form action="../actions/update_user_action.php" method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($user['name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                             <div class="col-md-6">
                                <label class="form-label fw-bold">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="learner" <?= $user['role'] == 'learner' ? 'selected' : '' ?>>Learner</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                </select>
                                <div class="form-text text-danger">Careful: Granting 'admin' gives full system access.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total XP (Manual Adjustment)</label>
                                <input type="number" class="form-control" name="xp" min="0" required value="<?= $user['xp'] ?>">
                            </div>
                        </div>
                        
                        <div class="alert alert-info small mb-4">
                            <i class="fas fa-info-circle me-2"></i>Password resetting is not currently supported in this edit form.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Update User Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>