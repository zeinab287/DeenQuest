<?php
// Enable error reporting for debugging (Remove in production if needed, but useful now)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Try to get credentials from environment variables (Best Practice)
    $host = getenv('DB_HOST') ?: "localhost"; 
    $user = getenv('DB_USER') ?: "zeinab.hamidou";
    $password = getenv('DB_PASSWORD') ?: "@M@dou2001";
    $database = getenv('DB_NAME') ?: "webtech_2025A_zeinab_hamidou";

    // Attempt connection
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // This block runs if connection fails, preventing the HTTP 500 error
    
    // Log the actual error for admin
    error_log("Database Connection Error: " . $e->getMessage());

    // Show a user-friendly error page
    die("
    <div style='font-family: sans-serif; padding: 20px; border: 2px solid #dc3545; background: #fff8f8; color: #dc3545; max-width: 600px; margin: 20px auto; border-radius: 8px;'>
        <h2 style='margin-top:0'>Database Connection Failed</h2>
        <p><strong>Server Error:</strong> Access Denied or Database Not Found.</p>
        <p>This usually means the <strong>Password</strong> or <strong>Database Name</strong> in <code>config/db.php</code> is incorrect for this server.</p>
        <hr style='border: 0; border-top: 1px solid #dc3545; opacity: 0.3;'>
        <p style='font-size: 0.9em; color: #333;'>
            <strong>Diagnostic Info:</strong><br>
            Host: $host<br>
            User: $user<br>
            Database: $database<br>
            Message: " . $e->getMessage() . "
        </p>
    </div>
    ");
}
?>