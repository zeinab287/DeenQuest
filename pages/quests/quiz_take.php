<?php
// user session authentication and header inclusion
session_start();
include('../../includes/header.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// get quest ID from URL and validate it
if (!isset($_GET['quest_id']) || !is_numeric($_GET['quest_id'])) {
    header("Location: ../../dashboard.php");
    exit();
}
$quest_id = $_GET['quest_id'];

require_once '../../config/db.php';

// fetch quest title for display
$questSql = "SELECT title FROM Quest WHERE quest_id = ?";
$stmtQ = $conn->prepare($questSql);
$stmtQ->bind_param("i", $quest_id);
$stmtQ->execute();
$questResult = $stmtQ->get_result();
if($questResult->num_rows === 0) { die("Quest not found"); }
$questData = $questResult->fetch_assoc();
$stmtQ->close();


// fetch questions for this quest ID
$sql = "SELECT * FROM QuizQuestion WHERE quest_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quest_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <h2 class="text-success fw-bold mb-4">
            Quiz: <?php echo htmlspecialchars($questData['title']); ?>
        </h2>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                
                <form action="../../actions/quiz_grade.php" method="POST">
                    
                    <input type="hidden" name="quest_id" value="<?php echo $quest_id; ?>">

                    <?php 
                    // check if there are questions
                    if ($result->num_rows > 0) {
                        $qCount = 1;
                        // loop through each question found in the database
                        while($question = $result->fetch_assoc()) 
                        {
                            ?>
                            
                            <div class="mb-5 ml-4">
                                <h5 class="fw-bold mb-3">
                                    <span class="badge bg-secondary me-2">Q<?php echo $qCount; ?></span>
                                    <?php echo htmlspecialchars($question['question_text']); ?>
                                </h5>

                                <div class="form-check mb-2 ms-4">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="a" id="q<?php echo $question['question_id']; ?>_a" required>
                                    <label class="form-check-label" for="q<?php echo $question['question_id']; ?>_a">
                                        <?php echo htmlspecialchars($question['option_a']); ?>
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2 ms-4">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="b" id="q<?php echo $question['question_id']; ?>_b">
                                    <label class="form-check-label" for="q<?php echo $question['question_id']; ?>_b">
                                        <?php echo htmlspecialchars($question['option_b']); ?>
                                    </label>
                                </div>

                                <div class="form-check mb-2 ms-4">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="c" id="q<?php echo $question['question_id']; ?>_c">
                                    <label class="form-check-label" for="q<?php echo $question['question_id']; ?>_c">
                                        <?php echo htmlspecialchars($question['option_c']); ?>
                                    </label>
                                </div>
                            </div>
                            <?php
                            $qCount++;
                        } 
                        ?>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg fw-bold">Submit Answers</button>
                        </div>

                    <?php 
                    } else {
                        echo "<p class='text-muted'>No questions found for this quest yet.</p>";
                    }
                    $stmt->close();
                    ?>

                </form>
            </div>
        </div>

    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>