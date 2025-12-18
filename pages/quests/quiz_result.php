<?php
// include heaader and check user session
session_start();
include('../../includes/header.php'); 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// check if quiz results exist in the session, if not, user shouldn't be here.
if (!isset($_SESSION['quiz_result'])) {
    header("Location: ../../dashboard.php");
    exit();
}

// get the data from the session and then delete it so it doesn't show again on refresh.
$result = $_SESSION['quiz_result'];
unset($_SESSION['quiz_result']); 

$score = $result['score'];
$total = $result['total'];
$xp_gained = $result['xp_gained'];

// calculate percentage 
$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;

// determine message based on score
if ($percentage == 100) {
    $message = "Masha'Allah! Perfect Score!";
    $icon = "fas fa-trophy text-warning";
    $bg_class = "bg-success";
} elseif ($percentage >= 50) {
    $message = "Great job! You passed.";
    $icon = "fas fa-thumbs-up text-success";
    $bg_class = "bg-success";
} else {
    $message = "Keep trying! Review the lesson and try again.";
    $icon = "fas fa-book-reader text-primary";
    $bg_class = "bg-secondary";
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">
        
        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-header <?php echo $bg_class; ?> text-white p-4">
                <h1><i class="<?php echo $icon; ?> bg-white rounded-circle p-3 fa-sm"></i></h1>
                <h2 class="fw-bold mt-3"><?php echo $message; ?></h2>
            </div>
            <div class="card-body p-5 bg-white">
                 <h3 class="display-4 fw-bold mb-3">
                     <?php echo $score; ?> / <?php echo $total; ?>
                 </h3>
                 <p class="text-muted lead mb-4">Correct Answers</p>

                 <?php if ($xp_gained > 0): ?>
                    <div class="alert alert-warning d-inline-block px-4 py-3 rounded-pill mb-4">
                        <h4 class="mb-0 fw-bold">
                            <i class="fas fa-star me-2"></i> +<?php echo $xp_gained; ?> XP Earned!
                        </h4>
                    </div>
                 <?php elseif ($score > 0): ?>
                    <p class="text-muted mb-4">(You have already earned XP for this quest previously.)</p>
                 <?php endif; ?>
                 
                 <div class="d-grid gap-2 col-8 mx-auto">
                     <a href="../../dashboard.php" class="btn btn-outline-success btn-lg fw-bold">
                        <i class="fas fa-home me-2"></i> Return to Dashboard
                     </a>
                 </div>
            </div>
        </div>

    </div>
</div>


<?php
include('../../includes/footer.php');
?>