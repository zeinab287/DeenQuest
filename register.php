<?php 
include('includes/header.php'); 
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <?php 
        // check if 'error_message' exists in the session
        if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php 
                    // display the message
                    echo $_SESSION['error_message']; 
                    // clear the message right away so it doesn't show again if user refresh the page
                    unset($_SESSION['error_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                
                <h2 class="text-center text-success fw-bold mb-4">Create an Account</h2>
                
                <form action="actions/register_action.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="fullNameField" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullNameField" name="full_name" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="emailField" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="emailField" name="email" placeholder="name@example.com" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">I am a...</label>
                        <select class="form-select" id="roleSelect" name="role" required>
                            <option value="" selected disabled>Choose your role</option>
                            <option value="teen">Learner (Student)</option>
                            <option value="revert">Administrator</option>
                        </select>
                         <div class="form-text small text-muted">Select Administrator only if you are one!!</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="passwordField" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordField" name="password" placeholder="Create a password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmPasswordField" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPasswordField" name="confirm_password" placeholder="Type password again" required>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg fw-bold">Create Account</button>
                    </div>
                    
                </form>
                </div> <div class="card-footer text-center bg-white py-3 border-0">
                <p class="mb-0">Already have an account? <a href="login.php" class="text-success fw-bold text-decoration-none">Login here</a></p>
            </div>
        </div> </div>
</div>

<?php 
include('includes/footer.php'); 
?>