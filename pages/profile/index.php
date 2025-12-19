<?php
// start session and include header
session_start();
include('../../includes/header.php'); 
// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/db.php';
$user_id = $_SESSION['user_id'];
$current_xp = $_SESSION['xp']; 

// calculate rank and progress
$rank_title = "Beginner";
$next_rank_xp = 300; 
$rank_class = "text-primary"; 

if ($current_xp >= 600) {
    $rank_title = "Advanced Scholar";
    $next_rank_xp = 1000; 
    $rank_class = "text-warning";
} elseif ($current_xp >= 300) {
    $rank_title = "Intermediate Learner";
    $next_rank_xp = 600;
    $rank_class = "text-success";
}

// calculate progress percentage based on current XP and next rank XP
if ($next_rank_xp > $current_xp) {
    $progress_percent = ($current_xp / $next_rank_xp) * 100;
} else {
    $progress_percent = 100;
}


// fetch earned badges
$sql = "SELECT * FROM badge ORDER BY xp_required ASC";
$result = $conn->query($sql);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold border-bottom pb-2">
            <i class="fas fa-user-circle me-2 text-success"></i>My Profile
        </h2>
    </div>
</div>

<div class="row">
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body p-4">
                 <i class="fas fa-user-graduate fa-5x text-muted mb-3"></i>
                 
                 <h3 class="fw-bold"><?php echo htmlspecialchars($_SESSION['name']); ?></h3>
                 <p class="badge bg-secondary text-uppercase mb-4"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                 
                 <hr>
                 
                 <h5 class="text-muted mb-2">Current Rank</h5>
                 <h4 class="fw-bold <?php echo $rank_class; ?> mb-3">
                    <i class="fas fa-crown me-2"></i><?php echo $rank_title; ?>
                 </h4>
                 
                 <h2 class="fw-bold display-5 text-success"><?php echo $current_xp; ?> XP</h2>
                 <p class="small text-muted mb-2">Progress to next rank (Goal: <?php echo $next_rank_xp; ?> XP)</p>
                 <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: <?php echo $progress_percent; ?>%">
                         <?php echo round($progress_percent); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h4 class="mb-0 fw-bold"><i class="fas fa-medal me-2 text-warning"></i>Badges & Achievements</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    
                    <?php 
                    // loop through each badge found in the database
                    if ($result->num_rows > 0) {
                        while($badge = $result->fetch_assoc()) {
                            // dtermine if the badge is unlocked
                            // It's unlocked if user's XP is greater or equal to required XP
                            $isUnlocked = ($current_xp >= $badge['xp_required']);
                            
                            // set styles based on locked/unlocked status
                            if ($isUnlocked) {
                                $cardClass = "border-warning bg-light";
                                $iconClass = "text-warning";
                                $statusText = '<span class="badge bg-success">Unlocked!</span>';
                                $icon = "fa-trophy";
                            } else {
                                // greyed out style for locked badges
                                $cardClass = "border-light bg-white opacity-50";
                                $iconClass = "text-muted";
                                $statusText = '<span class="badge bg-secondary">Locked</span> <small>Needs ' . $badge['xp_required'] . ' XP</small>';
                                $icon = "fa-lock";
                            }
                            ?>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 shadow-sm <?php echo $cardClass; ?>">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3 text-center" style="width: 60px;">
                                            <i class="fas <?php echo $icon; ?> fa-3x <?php echo $iconClass; ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($badge['name']); ?></h5>
                                            <p class="small text-muted mb-2">
                                                <?php echo htmlspecialchars($badge['description']); ?>
                                            </p>
                                            <div><?php echo $statusText; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No badges defined yet.</p>";
                    }
                    ?>
                    
                </div> </div> </div> </div> </div>

<?php
$conn->close();
include('../../includes/footer.php');
?>