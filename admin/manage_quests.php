<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // if not an admin, kick them out to the login page
    header("Location: ../login.php");
    exit();
}

// include necessary files
require_once '../config/db.php';
$pageTitle = "Manage Quests";
include('../includes/header.php');

// fetch all quests from database
$sql = "SELECT quest_id, title, subject, level, xp_reward FROM Quest ORDER BY level ASC, subject ASC";
$result = $conn->query($sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-map-signs text-success me-2"></i>Manage Quests</h1>
        <a href="add_quest.php" class="btn btn-success fw-bold">
            <i class="fas fa-plus-circle me-2"></i>Add New Quest
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-success text-white">
                        <tr>
                            <th scope="col" class="ps-4">ID</th>
                            <th scope="col">Title</th>
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
                                    <td class="ps-4 fw-bold text-muted">#<?= $row['quest_id'] ?></td>
                                    <td>
                                        <span class="fw-bold d-block"><?= htmlspecialchars($row['title']) ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['subject']) ?></span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-3">Lvl <?= $row['level'] ?></span></td>
                                    <td class="text-center text-warning fw-bold">+<?= $row['xp_reward'] ?></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                          <a href="manage_quiz.php?quest_id=<?= $row['quest_id'] ?>" class="btn btn-outline-warning" title="Manage Quiz"><i class="fas fa-question-circle"></i></a>
                                          <a href="edit_quest.php?id=<?= $row['quest_id'] ?>" class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                          <a href="../actions/delete_quest_action.php?id=<?= $row['quest_id'] ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this quest?');"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p class="fw-bold mb-1">No quests found.</p>
                                    <p class="small mb-0">Click the "Add New Quest" button to get started.</p>
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
// close connection and include footer
$conn->close();
include('../includes/footer.php');
?>