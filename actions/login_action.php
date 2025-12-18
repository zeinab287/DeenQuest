<?php
//handle user login authentication
session_start();
require_once '../config/db.php';

// check if the form was actually submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // validate data
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: ../login.php");
        exit();
    }

    // prepare SQL query to find user by email
    // select the role here so we can check it later.
    $sql = "SELECT user_id, name, password_hash, role, xp FROM User WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            // check if a user with that email exists
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // verify the password against the hash in the database
                if (password_verify($password, $user['password_hash'])) {
                    // successful authentication
                    
                    // store essential info in session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['xp'] = $user['xp']; 

                    // redirect based on role
                    if ($user['role'] === 'admin') {
                        // if user is an admin, send to admin dashboard
                        header("Location: ../admin_dashboard.php");
                    } else {
                        // if user is a learner, send to learner dashboard
                        header("Location: ../learner_dashboard.php");
                    }
                    exit();

                } else {
                    // incorrect password
                    $_SESSION['error_message'] = "Invalid email or password.";
                }
            } else {
                // user not found with that email
                $_SESSION['error_message'] = "Invalid email or password.";
            }
        } else {
             $_SESSION['error_message'] = "Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
    $conn->close();
}

// if authentication failed,redirect back to login page to show error
header("Location: ../login.php");
exit();
?>