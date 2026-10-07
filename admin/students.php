<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle Payment Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_payment') {
    $enrollment_id = intval($_POST['enrollment_id'] ?? 0);
    $payment_status = trim($_POST['payment_status'] ?? 'unpaid');
    $payment_method = trim($_POST['payment_method'] ?? 'Cash');

    if ($enrollment_id > 0) {
        $stmt = $conn->prepare("UPDATE enrollments SET payment_status = ?, payment_method = ? WHERE id = ?");
        $stmt->bind_param("ssi", $payment_status, $payment_method, $enrollment_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Branch Filter
$selected_branch = $_GET['branch'] ?? 'All';

if ($selected_branch !== 'All') {
    $stmt = $conn->prepare("SELECT * FROM enrollments WHERE branch = ? ORDER BY id DESC");
    $stmt->bind_param("s", $selected_branch);
    $stmt->execute();
    $students_res = $stmt->get_result();
} else {
    $students_res = $conn->query("SELECT * FROM enrollments ORDER BY id DESC");
}

// Metrics
$metro_count = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE branch = 'Robinson Metro East'")->fetch_assoc()['c'];
$antipolo_count = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE branch = 'Vista Mall Antipolo'")->fetch_assoc()['c'];
$falcon_count = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE branch = 'Marikina Falcon'")->fetch_assoc()['c'];
$total_paid = $conn->query("SELECT SUM(fee_amount) as total FROM enrollments WHERE payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records & Payments – PKTS Admin Portal</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .metric-card { background: #1a1a1a; padding: 20px; border-radius: 12px; border-left: 4px solid var(--red); color: white; }
        .metric-card h3 { margin: 0; font-size: 0.9rem; color: #aaa; }
        .metric-card .value { font-size: 1.8rem; font-weight: 700; margin-top: 8px; color: white; }
        .filter-header { display: flex; justify-content: space-between; align-items: center; background: #1a1a1a; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        .badge-paid { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge-unpaid { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    </style>
</head>
<body>
    <?php include 'includes/slidebar.php'; ?>

    <main class="admin-content has-sidebar">
        <header class="content-header">
            <div>
                <h1 style="font-size: 1.8rem; margin: 0;">Consolidated Student Records & Payments</h1>
                <p style="color: #888; margin: 4px 0 0;">Multi-branch tracking across Metro East, Vista Mall Antipolo, and Marikina Falcon.</p>
            </div>
        </header>

        <div class="metrics-grid">
            <div class="metric-card">
                <h3>Robinson Metro East</h3>
                <div class="value"><?php echo $metro_count; ?> Students</div>
            </div>
            <div class="metric-card">
                <h3>Vista Mall Antipolo</h3>
                <div class="value"><?php echo $antipolo_count; ?> Students</div>
            </div>
            <div class="metric-card">
                <h3>Marikina Falcon</h3>
                <div class="value"><?php echo $falcon_count; ?> Students</div>
            </div>
            <div class="metric-card" style="border-left-color: #22c55e;">
                <h3>Total Revenue Collected</h3>
                <div class="value">₱<?php echo number_format($total_paid, 2); ?></div>
            </div>
        </div>

        <div class="filter-header">
            <div style="color: white; font-weight: 600;"><i class="fas fa-building" style="color: var(--red);"></i> Filter Branch:</div>
            <div style="display: flex; gap: 10px;">
                <a href="students.php?branch=All" class="btn" style="padding: 6px 14px; background: <?php echo $selected_branch === 'All' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">All Branches</a>
                <a href="students.php?branch=Robinson+Metro+East" class="btn" style="padding: 6px 14px; background: <?php echo $selected_branch === 'Robinson Metro East' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Metro East</a>
                <a href="students.php?branch=Vista+Mall+Antipolo" class="btn" style="padding: 6px 14px; background: <?php echo $selected_branch === 'Vista Mall Antipolo' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Antipolo</a>
                <a href="students.php?branch=Marikina+Falcon" class="btn" style="padding: 6px 14px; background: <?php echo $selected_branch === 'Marikina Falcon' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Falcon</a>
            </div>
        </div>

        <div class="card" style="background: #1a1a1a; padding: 20px; border-radius: 12px;">
            <table style="width: 100%; border-collapse: collapse; color: white;">
                <thead>
                    <tr style="border-bottom: 1px solid #333; text-align: left; color: #888;">
                        <th style="padding: 12px;">Student</th>
                        <th style="padding: 12px;">Guardian / Contact</th>
                        <th style="padding: 12px;">Branch</th>
                        <th style="padding: 12px;">Age</th>
                        <th style="padding: 12px;">Fee</th>
                        <th style="padding: 12px;">Payment Status</th>
                        <th style="padding: 12px;">Update Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students_res && $students_res->num_rows > 0): ?>
                        <?php while ($s = $students_res->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #262626;">
                                <td style="padding: 12px;"><strong><?php echo htmlspecialchars($s['student_first_name'] . ' ' . $s['student_last_name']); ?></strong></td>
                                <td style="padding: 12px; color: #ccc;">
                                    <?php echo htmlspecialchars($s['guardian_first_name'] . ' ' . $s['guardian_last_name']); ?><br>
                                    <small style="color: #777;"><?php echo htmlspecialchars($s['phone']); ?></small>
                                </td>
                                <td style="padding: 12px; color: var(--red);"><?php echo htmlspecialchars($s['branch']); ?></td>
                                <td style="padding: 12px;"><?php echo intval($s['student_age']); ?> yrs</td>
                                <td style="padding: 12px;">₱<?php echo number_format($s['fee_amount'], 2); ?></td>
                                <td style="padding: 12px;">
                                    <span class="status-badge badge-<?php echo $s['payment_status'] === 'paid' ? 'paid' : 'unpaid'; ?>">
                                        <?php echo htmlspecialchars($s['payment_status'] . ' (' . $s['payment_method'] . ')'); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <form action="students.php" method="POST" style="display: flex; gap: 6px; align-items: center;">
                                        <input type="hidden" name="action" value="update_payment">
                                        <input type="hidden" name="enrollment_id" value="<?php echo $s['id']; ?>">
                                        <select name="payment_status" style="background: #252525; color: white; border: 1px solid #444; padding: 4px 8px; border-radius: 4px;">
                                            <option value="paid" <?php echo $s['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="unpaid" <?php echo $s['payment_status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                        </select>
                                        <select name="payment_method" style="background: #252525; color: white; border: 1px solid #444; padding: 4px 8px; border-radius: 4px;">
                                            <option value="Cash" <?php echo $s['payment_method'] === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="GCash" <?php echo $s['payment_method'] === 'GCash' ? 'selected' : ''; ?>>GCash</option>
                                        </select>
                                        <button type="submit" style="background: var(--red); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: #888; padding: 30px;">No student records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
