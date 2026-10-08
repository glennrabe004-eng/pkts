<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$stmt = $conn->query("SELECT time_slot, COUNT(*) as count FROM trial_bookings WHERE status = 'pending' GROUP BY time_slot");
$slots = [];
if ($stmt) {
    while ($row = $stmt->fetch_assoc()) {
        $slots[$row['time_slot']] = $row['count'];
    }
    $stmt->close();
}

echo json_encode(['slots' => $slots]);