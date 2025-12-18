<?php
// start session and check admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }
require_once '../config/db.php';

// fetch existing badge data
if (!isset($_GET['id'])) { header("Location: manage_badges.php"); exit(); }
$badgeId = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM Badge WHERE badge_id = ?");
$stmt->bind_param("i", $badgeId);
$stmt->execute();
$badge = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$badge) { header("Location: manage_badges.php"); exit(); }

$pageTitle = "Edit Badge";
include('../includes/header.php');

// list for gamification icon selection
$iconList = [
    'fa-medal', 'fa-trophy', 'fa-award', 'fa-crown', 'fa-star',
    'fa-shield-alt', 'fa-swords', 'fa-magic', 'fa-hat-wizard', 'fa-scroll',
    'fa-gem', 'fa-coins', 'fa-treasure-chest', 'fa-key', 'fa-lock-open',
    'fa-fire', 'fa-bolt', 'fa-leaf', 'fa-paw', 'fa-dragon',
    'fa-book-reader', 'fa-brain', 'fa-lightbulb', 'fa-graduation-cap', 'fa-certificate'
];

// ensure the current icon is in the list, if not, add it temporarily so it doesn't look broken
if (!in_array($badge['icon'], $iconList)) {
    array_unshift($iconList, $badge['icon']);
}
?>

<style>
    .icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 10px;
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
    .icon-option {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px;
        font-size: 1.5rem;
        color: #555;
        border: 2px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background-color: #f8f9fa;
    }
    .icon-option:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }
    .icon-option.selected {
        border-color: #0d6efd; 
        background-color: #cfe2ff;
        color: #084298;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-edit text-primary me-2"></i>Edit Badge</h1>
                <a href="manage_badges.php" class="btn btn-outline-secondary">Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-primary border-3">
                <div class="card-body p-4">
                    <form action="../actions/update_badge_action.php" method="POST" id="badgeForm">
                        <input type="hidden" name="badge_id" value="<?= $badge['badge_id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Badge Name</label>
                            <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($badge['name']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">XP Requirement</label>
                            <input type="number" class="form-control" name="xp_required" min="0" required value="<?= $badge['xp_required'] ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Change Badge Icon</label>
                            
                            <input type="hidden" name="icon" id="selectedIcon" value="<?= htmlspecialchars($badge['icon']) ?>" required>
                            
                            <div class="icon-grid">
                                <?php foreach ($iconList as $iconClass): ?>
                                    <?php 
                                        // Check if this is the currently selected icon in the DB
                                        $isSelected = ($iconClass === $badge['icon']) ? 'selected' : ''; 
                                    ?>
                                    <div class="icon-option <?= $isSelected ?>" data-icon="<?= $iconClass ?>">
                                        <i class="fas <?= $iconClass ?>"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="2" required><?= htmlspecialchars($badge['description']) ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Update Badge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconOptions = document.querySelectorAll('.icon-option');
    const hiddenInput = document.getElementById('selectedIcon');

    iconOptions.forEach(option => {
        option.addEventListener('click', function() {
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            hiddenInput.value = this.getAttribute('data-icon');
        });
    });
});
</script>

<?php include('../includes/footer.php'); ?>