<?php
// Diagnostic test - upload this first to check MySQL connectivity
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>InfinityFree MySQL Connection Test</h3>";
echo "<hr>";

// Test 1: PHP & extensions
echo "PHP version: " . phpversion() . "<br>";
echo "mysqli extension: " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "<br>";
echo "pdo_mysql extension: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "<br><hr>";

// Test 2: MySQL connection
$host = 'sql210.infinityfree.com';
$user = 'if0_42734174';
$pass = 'b5d4WgU0g7Mc3Y';
$db   = 'if0_42734174_pkts_karate';

echo "Connecting to: $host<br>";
echo "Database: $db<br>";
echo "Username: $user<br>";
echo "Password: " . ($pass === '' ? '(empty)' : '(set)') . "<br><hr>";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<span style='color:red;font-weight:bold;'>CONNECTION FAILED</span><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "Errno: " . $conn->connect_errno . "<br>";
} else {
    echo "<span style='color:green;font-weight:bold;'>CONNECTED SUCCESSFULLY</span><br>";
    
    // Test 3: Check tables
    $tables = ['admin_users', 'customers', 'products', 'orders', 'order_items', 
               'trial_bookings', 'contact_messages', 'site_settings'];
    echo "<br>Tables in database:<br>";
    foreach ($tables as $t) {
        $r = $conn->query("SHOW TABLES LIKE '$t'");
        $exists = ($r && $r->num_rows > 0) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>';
        echo "  - $t: $exists<br>";
    }
}

echo "<hr>";
echo "<small>Delete this file after testing.</small>";
?>
