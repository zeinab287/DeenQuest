<?php
// start the session and secure admin access
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }
$pageTitle = "Add New Badge";
include('../includes/header.php');

// add Font Awesome class names.
$iconList = [
    'fa-medal', 'fa-trophy', 'fa-award', 'fa-crown', 'fa-star',
    'fa-shield-alt', 'fa-swords', 'fa-magic', 'fa-hat-wizard', 'fa-scroll',
    'fa-gem', 'fa-coins', 'fa-treasure-chest', 'fa-key', 'fa-lock-open',
    'fa-fire', 'fa-bolt', 'fa-leaf', 'fa-paw', 'fa-dragon',
    'fa-book-reader', 'fa-brain', 'fa-lightbulb', 'fa-graduation-cap', 'fa-certificate'
];
?>

<style>
    /* styles for the visual icon picker grid */
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
    /* style for the selected icon */
    .icon-option.selected {
        border-color: #ffc107; 
        background-color: #fff3cd;
        color: #856404;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-plus-circle text-warning me-2"></i>Add New Badge</h1>
                <a href="manage_badges.php" class="btn btn-outline-secondary">Back to List</a>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 border-top border-warning border-3">
                <div class="card-body p-4">
                    <form action="../actions/add_badge_action.php" method="POST" id="badgeForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Badge Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g., Quiz Master">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">XP Requirement</label>
                            <input type="number" class="form-control" name="xp_required" min="0" required placeholder="e.g., 1000">
                            <div class="form-text">Total XP needed to unlock automatically.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Badge Icon</label>
                            
                            <input type="hidden" name="icon" id="selectedIcon" required>
                            
                            <div class="icon-grid">
                                <?php foreach ($iconList as $iconClass): ?>
                                    <div class="icon-option" data-icon="<?= $iconClass ?>">
                                        <i class="fas <?= $iconClass ?>"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text text-danger" id="iconError" style="display:none;">Please select an icon.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="2" required placeholder="What is this achievement for?"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold">Create Badge</button>
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
    const iconError = document.getElementById('iconError');
    const form = document.getElementById('badgeForm');

    // Handle click on icon options
    iconOptions.forEach(option => {
        option.addEventListener('click', function() {
            // remove 'selected' class from all other options
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            
            // add 'selected' class to clicked option
            this.classList.add('selected');
            
            // update the hidden input value
            hiddenInput.value = this.getAttribute('data-icon');
            
            // hide error message if showing
            iconError.style.display = 'none';
        });
    });

    // form validation just before submit
    form.addEventListener('submit', function(e) {
        if (hiddenInput.value === '') {
            // Stop form submission
            e.preventDefault(); 
            // Show error message
            iconError.style.display = 'block'; 
            // Scroll to the error
            iconError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>

<?php include('../includes/footer.php'); ?>