<?php
// start the session and include database connection
session_start();

require_once '../config/db.php';

// run this only if the form was actually submitted using POST.
// if someone tries to just type the address of this file in their browser, send them back.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../register.php");
    exit();
}

// getting and sanitizing form inputs
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$role = $_POST['role'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// check if passwords match
if ($password !== $confirm_password) {
    // store an error message in the session to show it on the register page
    $_SESSION['error_message'] = "Error: Passwords did not match.";
    // Send user back to the registration page
    header("Location: ../register.php");
    exit(); 
}


// check if the email already exists in the database
$checkQuery = "SELECT user_id FROM user WHERE email = ?";
$stmt = $conn->prepare($checkQuery);

if ($stmt) {
    // bind the email variable to the question mark. "s" means it's a String.
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // if num_rows is greater than 0, it means we found that email already.
    if ($stmt->num_rows > 0) {
        $_SESSION['error_message'] = "Error: That email address is already registered. Try logging in.";
        $stmt->close();
        header("Location: ../register.php");
        exit();
    }
    $stmt->close();
} else {
     // if the database query failed for some technical reason
     $_SESSION['error_message'] = "Database error during email check.";
     header("Location: ../register.php");
     exit();
}

// store hashed password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// prepare the insert query
$insertQuery = "INSERT INTO user (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);

if ($stmt) {
    // "ssss": name, email, hashed password, and role.
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    // try to execute the insert
    if ($stmt->execute()) {
        // SUCCESS! the user is in the database.
        $_SESSION['success_message'] = "Account created successfully! You can now login.";
        // Send user to the login page
        header("Location: ../login.php");
        exit();
    } else {
        // failed to insert
        $_SESSION['error_message'] = "Error creating account. Please try again.";
        header("Location: ../register.php");
        exit();
    }
    $stmt->close();
} else {
     $_SESSION['error_message'] = "Database prepare error.";
     header("Location: ../register.php");
     exit();
}

// Close connection
$conn->close();
?>