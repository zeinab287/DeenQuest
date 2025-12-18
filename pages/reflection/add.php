<?php
session_start();
// security and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
$pageTitle = "Add New Reflection";
include('../../includes/header.php');
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-feather-alt text-success me-2"></i>Write a Reflection</h1>
                <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>My Reflections</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-success border-3">
                <div class="card-body p-4">
                    <form action="../../actions/add_reflection_action.php" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., My thoughts on this week's lessons">
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold">My Reflection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="6" required placeholder="What did you learn? How do you feel about your progress?"></textarea>
                            <div class="form-text">Take a moment to reflect on your learning journey.</div>
                        </div>

                        <div class="mb-4">
                            <label for="goals" class="form-label fw-bold">Goals for Next Week (Optional)</label>
                            <textarea class="form-control" id="goals" name="goals" rows="3" placeholder="e.g., Complete 2 quests, earn 500 XP, or master a specific topic."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg fw-bold" id="saveBtn" disabled>
                                <i class="fas fa-save me-2"></i>Save Reflection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const saveBtn = document.getElementById('saveBtn');

    function checkInputs() {
        // Check if both Title and Content have text
        if (titleInput.value.trim().length > 0 && contentInput.value.trim().length > 0) {
            saveBtn.disabled = false; 
        } else {
            saveBtn.disabled = true; 
        }
    }

    titleInput.addEventListener('input', checkInputs);
    contentInput.addEventListener('input', checkInputs);
</script>

<?php include('../../includes/footer.php'); ?>