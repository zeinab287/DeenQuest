<?php
session_start();
// security and role Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';
$userId = $_SESSION['user_id'];

// fetch user's reflections
$sql = "SELECT * FROM reflection WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My Reflections & Goals";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold text-success"><i class="fas fa-book-open me-3"></i>My Reflections & Goals</h1>
            <p class="text-muted lead mb-0">Look back on your journey and track your future goals.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="add.php" class="btn btn-success fw-bold shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Add New Reflection
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card shadow-sm border-0 hover-lift">

                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                          <h5 class="fw-bold text-success mb-0 text-truncate me-3"><?= htmlspecialchars($row['title']) ?></h5>
                            <div class="d-flex align-items-center">
                              <small class="text-muted fw-bold me-3">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?= date('M d, Y', strtotime($row['created_at'])) ?>
                              </small>

                              <form action="../../actions/delete_reflection_action.php" method="POST" class="d-inline">
                                <input type="hidden" name="reflection_id" value="<?= $row['reflection_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Delete Reflection" onclick="return confirm('Are you sure you want to delete this reflection? This cannot be undone.');">
                                  <i class="fas fa-trash-alt"></i>
                                </button>
                              </form>
                            </div>
                          </div>    
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-feather-alt me-2 text-success"></i>Reflection</h6>
                                <p class="card-text text-muted" style="white-space: pre-line;"><?= htmlspecialchars($row['content']) ?></p>
                            </div>
                            
                            <?php if (!empty($row['goals'])): ?>
                                <div>
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-bullseye me-2 text-danger"></i>Goals for Next Week</h6>
                                    <p class="card-text text-muted" style="white-space: pre-line;"><?= htmlspecialchars($row['goals']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
             <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-book-open fa-3x mb-3 opacity-50"></i>
                    <p class="fw-bold mb-1">You haven't written any reflections yet.</p>
                    <p class="small mb-3">Take a moment to document your thoughts and set some goals!</p>
                    <a href="add.php" class="btn btn-sm btn-success">Write Your First Reflection</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important; }
</style>

<?php
$stmt->close();
$conn->close();
include('../../includes/footer.php');
?>