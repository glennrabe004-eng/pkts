<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $conn->query("SELECT time_slot, COUNT(*) as count FROM trial_bookings GROUP BY time_slot");
$slots = [];
if ($stmt) {
    while ($row = $stmt->fetch_assoc()) {
        $slots[$row['time_slot']] = $row['count'];
    }
    $stmt->close();
}

echo json_encode(['slots' => $slots]);