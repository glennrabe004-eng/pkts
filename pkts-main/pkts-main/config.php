<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

$host_header = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$is_local = (strpos($host_header, 'localhost') !== false || strpos($host_header, '127.0.0.1') !== false || php_sapi_name() === 'cli');

if (!defined('DB_HOST')) {
    define('DB_HOST', $is_local ? '127.0.0.1' : 'sql210.infinityfree.com');
    define('DB_USER', $is_local ? 'root' : 'if0_42734174');
    define('DB_PASS', $is_local ? '' : 'b5d4WgU0g7Mc3Y');
    define('DB_NAME', $is_local ? 'pkts_karate' : 'if0_42734174_pkts_karate');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $is_local ? 'http://localhost:8000' : 'https://pktskarate.ifree.page');
    define('SITE_URL', $is_local ? 'http://localhost:8000' : 'https://pktskarate.ifree.page');
}

date_default_timezone_set('Asia/Manila');

try {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error && !$is_local) {
        $conn = new mysqli('127.0.0.1', 'root', '', 'pkts_karate');
    }
} catch (Throwable $e) {
    if (!$is_local) {
        $conn = new mysqli('127.0.0.1', 'root', '', 'pkts_karate');
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

$conn->set_charset("utf8mb4");