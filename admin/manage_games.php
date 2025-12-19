<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

require_once '../config/db.php';
$pageTitle = "Manage Games";
include('../includes/header.php');

// fetch all games from database
$sql = "SELECT * FROM game ORDER BY subject ASC, difficulty_level ASC";
$result = $conn->query($sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-gamepad text-warning me-2"></i>Manage Games</h1>
        <a href="add_game.php" class="btn btn-warning fw-bold">
            <i class="fas fa-plus-circle me-2"></i>Add New Game
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-warning text-dark">
                        <tr>
                            <th scope="col" class="ps-4">Title</th>
                            <th scope="col">Type</th>
                            <th scope="col">Subject</th>
                            <th scope="col" class="text-center">Level</th>
                            <th scope="col" class="text-center">XP</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold d-block"><?= htmlspecialchars($row['title']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars(substr($row['description'], 0, 50)) ?>...</small>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['type']) ?></span></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['subject']) ?></span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-3">Lvl <?= $row['difficulty_level'] ?></span></td>
                                    <td class="text-center text-success fw-bold">+<?= $row['xp_reward'] ?></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_game.php?id=<?= $row['game_id'] ?>" class="btn btn-outline-primary" title="Edit Info"><i class="fas fa-edit"></i></a>
                                            <a href="../actions/delete_game_action.php?id=<?= $row['game_id'] ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this game definition?');"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-gamepad fa-3x mb-3 opacity-50"></i>
                                    <p class="fw-bold mb-1">No games found.</p>
                                    <p class="small mb-0">Click the "Add New Game" button to define one.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../includes/footer.php');
?>