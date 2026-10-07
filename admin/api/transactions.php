<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';

$stmt = $conn->query("
    SELECT 'order' as type, order_number as id, customer_name as title, CONCAT('₱', FORMAT(total_amount, 2)) as amount, UNIX_TIMESTAMP(created_at) as timestamp FROM orders 
    UNION ALL 
    SELECT 'booking' as type, id, CONCAT(guardian_first_name, ' ', guardian_last_name, ' (', time_slot, ')') as title, 'Free Trial' as amount, UNIX_TIMESTAMP(created_at) as timestamp FROM trial_bookings 
    UNION ALL 
    SELECT 'message' as type, id, CONCAT(name, ' - ', subject) as title, 'New Message' as amount, UNIX_TIMESTAMP(created_at) as timestamp FROM contact_messages 
    ORDER BY timestamp DESC 
    LIMIT 20
");

$transactions = [];
if ($stmt) {
    while ($row = $stmt->fetch_assoc()) {
        $transactions[] = $row;
    }
    $stmt->close();
}

echo json_encode(['transactions' => $transactions]);