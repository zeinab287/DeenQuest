<?php
// Force error reporting to screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='font-family:monospace; padding:20px;'>";
echo "<h1>Server Debugger</h1>";

// 1. Check PHP Version
echo "<h2>1. Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current File: " . __FILE__ . "<br>";

// 2. Test Path Logic (from header.php)
echo "<h2>2. Path Logic Test</h2>";
$projectRoot = str_replace('\\', '/', __DIR__);
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$webRoot = str_replace($docRoot, '', $projectRoot);
if (substr($webRoot, 0, 1) !== '/') { $webRoot = '/' . $webRoot; }
if (substr($webRoot, -1) !== '/') { $webRoot .= '/'; }
echo "Calculated WebRoot: <strong>" . htmlspecialchars($webRoot) . "</strong><br>";

// 3. Database Connection
echo "<h2>3. Database Check</h2>";
$host = getenv('DB_HOST') ?: "localhost"; 
$user = getenv('DB_USER') ?: "zeinab.hamidou";
$password = getenv('DB_PASSWORD') ?: "@M@dou2001";
$database = getenv('DB_NAME') ?: "webtech_2025A_zeinab_hamidou";

echo "Attempting connection to <strong>$database</strong> as <strong>$user</strong>...<br>";

try {
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    echo "<span style='color:green'>&#10004; Database Connected Successfully!</span><br>";
    
    // 4. Table Case Check
    echo "<h2>4. Table Existence Check</h2>";
    
    $checkUser = $conn->query("SHOW TABLES LIKE 'User'");
    $hasUser = $checkUser && $checkUser->num_rows > 0;
    echo "Table 'User' (Capitalized): " . ($hasUser ? "<span style='color:orange'>EXISTS</span>" : "<span style='color:gray'>Not Found</span>") . "<br>";
    
    $checkuser = $conn->query("SHOW TABLES LIKE 'user'");
    $hasuser = $checkuser && $checkuser->num_rows > 0;
    echo "Table 'user' (lowercase): " . ($hasuser ? "<span style='color:green'>EXISTS</span>" : "<span style='color:red'>Not Found</span>") . "<br>";

    // Check row counts
    if ($hasUser) {
        $res = $conn->query("SELECT count(*) as c FROM User");
        $row = $res->fetch_assoc();
        echo "Rows in 'User': " . $row['c'] . "<br>";
    }
    if ($hasuser) {
        $res = $conn->query("SELECT count(*) as c FROM user");
        $row = $res->fetch_assoc();
        echo "Rows in 'user': " . $row['c'] . "<br>";
        
        // Check password hash format
        $res = $conn->query("SELECT user_id, email, password_hash FROM user LIMIT 1");
        if ($r = $res->fetch_assoc()) {
            echo "Sample User (lowercase): ID=" . $r['user_id'] . ", Email=" . $r['email'] . ", HashStart=" . substr($r['password_hash'], 0, 10) . "...<br>";
        }
    }

} catch (Exception $e) {
    echo "<span style='color:red'>&#10008; Connection Failed: " . $e->getMessage() . "</span><br>";
}

echo "</div>";
?>
