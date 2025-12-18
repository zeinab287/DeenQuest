<?php
// start session and check user role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header("Location: ../../login.php"); exit();
}
require_once '../../config/db.php';

$pageTitle = "Mini Games Library";
include('../../includes/header.php');

// fetch all distinct subjects from game table
$subjects = [];
$subSql = "SELECT DISTINCT subject FROM Game WHERE subject IS NOT NULL AND subject != '' ORDER BY subject ASC";
$subResult = $conn->query($subSql);
if ($subResult) {
    while ($row = $subResult->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }
}

// fetch all distinct game types from the 'type' column
$gameTypes = [];
$typeSql = "SELECT DISTINCT type FROM Game WHERE type IS NOT NULL AND type != '' ORDER BY type ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult) {
    while ($row = $typeResult->fetch_assoc()) {
        $gameTypes[] = $row['type'];
    }
}

// start with a base WHERE clause that is always true
$whereClauses = ["1=1"];
$params = [];
$types = "";

// check for subject filter
$selectedSubject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
if (!empty($selectedSubject)) {
    $whereClauses[] = "subject = ?";
    $params[] = $selectedSubject;
    $types .= "s"; 
}

// check for game type filter
$selectedType = isset($_GET['type']) ? trim($_GET['type']) : '';
if (!empty($selectedType)) {
    $whereClauses[] = "type = ?";
    $params[] = $selectedType;
    $types .= "s"; // 
}

// combine clauses
$whereSql = implode(" AND ", $whereClauses);

// the main query to fetch matching games based on filters
$sql = "SELECT * FROM Game WHERE $whereSql ORDER BY title ASC";

$stmt = $conn->prepare($sql);
// dynamically bind parameters if any filters are active
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center bg-white p-4 rounded shadow-sm mx-0">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <h1 class="fw-bold text-success mb-1"><i class="fas fa-gamepad me-2"></i>Mini Games Library</h1>
            <p class="text-muted mb-0">Filter to find the perfect game for your learning session!</p>
        </div>
        
        <div class="col-lg-6">
            <form action="index.php" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label for="subject" class="form-label fw-bold small text-muted">Filter by Subject</label>
                        <select name="subject" id="subject" class="form-select bg-light border-0">
                            <option value="">All Subjects</option>
                            <?php foreach ($subjects as $sub): ?>
                                <option value="<?= htmlspecialchars($sub) ?>" <?= ($selectedSubject === $sub) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="type" class="form-label fw-bold small text-muted">Filter by Game Type</label>
                        <select name="type" id="type" class="form-select bg-light border-0">
                            <option value="">All Game Types</option>
                            <?php foreach ($gameTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= ($selectedType === $type) ? 'selected' : '' ?>>
                                    <?= ucfirst(htmlspecialchars($type)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100 fw-bold"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($game = $result->fetch_assoc()): ?>
                <?php
                    // set color based on difficulty level
                    $diffLevel = intval($game['difficulty_level']);
                    $diffColor = 'success'; // 1 to 2 are easy
                    $diffLabel = 'Easy';
                    
                    if ($diffLevel >= 3 && $diffLevel <= 4) {
                        $diffColor = 'warning';
                        $diffLabel = 'Medium';
                    } elseif ($diffLevel >= 5) {
                        $diffColor = 'danger';
                        $diffLabel = 'Hard';
                    }
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-<?= $diffColor ?> bg-opacity-10 text-<?= $diffColor ?> px-3 rounded-pill">
                                    <?= $diffLabel ?> (<?= $diffLevel ?>)
                                </span>
                                <small class="text-muted fw-bold">
                                    <i class="fas fa-star text-warning"></i> <?= number_format($game['xp_reward']) ?> XP
                                </small>
                            </div>
                            
                            <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($game['title']) ?></h5>
                            
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border me-1">
                                    <i class="fas fa-book me-1 text-muted"></i><?= htmlspecialchars($game['subject'] ?? 'General') ?>
                                </span>
                                <span class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-25">
                                    <i class="fas fa-puzzle-piece me-1 text-info"></i><?= ucfirst(htmlspecialchars($game['type'] ?? 'Game')) ?>
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small"><?= htmlspecialchars($game['description']) ?></p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-4 pt-0">
                            <a href="play.php?game_id=<?= $game['game_id'] ?>" class="btn btn-success w-100 fw-bold stretched-link">
                                <i class="fas fa-play me-2"></i>Play Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-ghost fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="fw-bold text-muted">No games found!</h3>
                <p class="text-muted">Try changing your filters to see more games.</p>
                <a href="index.php" class="btn btn-outline-success mt-2 fw-bold">Clear All Filters</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* hover effect for game cards */
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 1rem 2rem rgba(0,0,0,0.15) !important; }
</style>

<?php 
$stmt->close();
$conn->close();
include('../../includes/footer.php'); 
?>