<?php
// start session and include header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//tell the browser never to store a local copy of this page.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require_once 'config/db.php';

// ensure the user is logged in and is an admin.
// If not an admin, redirect them to the login page.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
include('includes/header.php');

// get user's name for the welcome message
$adminName = $_SESSION['name'] ?? 'Administrator';

?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-dark">Admin Dashboard</h1>
            <p class="lead text-muted">Welcome back, <?php echo htmlspecialchars($adminName); ?>. What would you like to manage today?</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h4 class="card-title fw-bold">Manage Users</h4>
                    <p class="card-text text-muted">View, edit, or delete user accounts.</p>
                    <a href="admin/manage_users.php" class="btn btn-primary stretched-link">Go to Users</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-map-signs fa-3x text-success"></i>
                    </div>
                    <h4 class="card-title fw-bold">Manage Quests</h4>
                    <p class="card-text text-muted">Create and edit quests and their content.</p>
                    <a href="admin/manage_quests.php" class="btn btn-success stretched-link">Go to Quests</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-gamepad fa-3x text-warning"></i>
                    </div>
                    <h4 class="card-title fw-bold">Manage Games</h4>
                    <p class="card-text text-muted">Setup and configure mini-games.</p>
                    <a href="admin/manage_games.php" class="btn btn-warning stretched-link">Go to Games</a>

                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-trophy fa-3x text-danger"></i>
                    </div>
                    <h4 class="card-title fw-bold">Manage Challenges</h4>
                    <p class="card-text text-muted">Organize weekly or monthly challenges.</p>
                    <a href="admin/manage_challenges.php" class="btn btn-danger stretched-link">Go to Challenges</a>
                </div>
            </div>
        </div>

        <h3 class="pb-2 mb-3 border-bottom mt-5">Gamification</h3>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-start border-4 border-warning shadow-sm position-relative hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fas fa-award fa-2x text-warning"></i>
                                </div>
                                <h4 class="card-title fw-bold mb-0">Badges</h4>
                            </div>
                            <p class="card-text text-muted">Create and manage achievement badges and their XP requirements.</p>
                            <a href="admin/manage_badges.php" class="btn btn-warning stretched-link fw-bold">Manage Badges</a>
                        </div>
                    </div>
                </div>
            </div>

    </div> </div> <style>
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<?php
include('includes/footer.php');
?>