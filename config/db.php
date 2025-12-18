<?php
// configuration for database connection

// Try to get credentials from environment variables (Best Practice for Live Server)
// If these are not set, it will fall back to your local XAMPP settings.
$host = getenv('DB_HOST') ?: "localhost"; 
$user = getenv('DB_USER') ?: "zeinab.hamidou";
$password = getenv('DB_PASSWORD') ?: "@M@dou2001";
$database = getenv('DB_NAME') ?: "webtech_2025A_zeinab_hamidou";

// FOR LIVE SERVER (If you can't set environment variables):
// You can manually replace the values above on the LIVE SERVER ONLY.
// Example:
// $host = "localhost"; // usually localhost on live server too
// $user = "your_live_username";
// $password = "your_live_password";
// $database = "your_live_database_name";

// connect to database
$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    // Log the error internally but show a generic message to the user for security
    error_log("Connection failed: " . $conn->connect_error);
    die("Database Connection Failed. Please check your configuration.");
}

$conn->set_charset("utf8mb4");
?>