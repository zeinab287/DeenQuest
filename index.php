<?php 
include('includes/header.php'); 
?>

<div class="row align-items-center justify-content-center text-center text-md-start">
    
    <div class="col-md-6 mb-4">
        <h1 class="display-4 fw-bold text-success">Welcome to DeenQuest</h1>
        <p class="lead fs-4">Learn Your Deen. Enjoy the Journey.</p>
        <p class="text-muted mb-4">
            We are trying to change how we learn. No more just memorizing without understanding. 
            DeenQuest uses quizzes, challenges, and rewards to make learning Islamic principles fun and meaningful.
        </p>
        
        <div class="d-grid gap-2 d-md-block">
            <a href="register.php" class="btn btn-success btn-lg px-4 me-md-2 fw-bold">Start Your Quest</a>
            <a href="login.php" class="btn btn-outline-success btn-lg px-4">Login</a>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="text-center text-success opacity-75">
           <i class="fas fa-mosque fa-10x"></i>
        </div>
    </div>
</div>
<div class="row mt-5 text-center">
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 p-3">
            <h3 class="text-success"><i class="fas fa-gamepad"></i></h3>
            <h5>Interactive Learning</h5>
            <p class="text-muted small">Move beyond boring memorization. We use engaging quizzes and mini-games to help you understand.</p>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 p-3">
             <h3 class="text-warning"><i class="fas fa-star"></i></h3>
            <h5>Earn Rewards (XP)</h5>
            <p class="text-muted small">Gain XP (experience points) and collect badges as you finish quests. See you on the leaderboard!</p>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 p-3">
             <h3 class="text-primary"><i class="fas fa-book-open"></i></h3>
            <h5>Private Reflections</h5>
            <p class="text-muted small">It's important to think about what you learn. Use your private space to write notes and set goals.</p>
        </div>
    </div>
</div>
<?php 
include('includes/footer.php'); 
?>