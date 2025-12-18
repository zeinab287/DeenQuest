<?php
// start session and check if user already logged in
session_start();

if (isset($_SESSION['user_id'])) {
    // if the user is already logged in, don't show the login form
    // redirect user based on their existing role.
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: learner_dashboard.php");
    }
    exit();
}

include('includes/header.php'); 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center p-4">
                    <h3 class="fw-bold mb-0"><i class="fas fa-sign-in-alt me-2"></i>Welcome Back!</h3>
                    <p class="mb-0 opacity-75">Please log in to continue your journey.</p>
                </div>
                <div class="card-body p-5">
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php 
                                echo $_SESSION['error_message']; 
                                unset($_SESSION['error_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="actions/login_action.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email"><i class="fas fa-envelope me-2 text-muted"></i>Email Address</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password"><i class="fas fa-lock me-2 text-muted"></i>Password</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg fw-bold p-3">
                                Log In <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light">
                    <div class="small">
                        <a href="register.php" class="text-decoration-none fw-bold text-success">
                            Need an account? Sign up!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>