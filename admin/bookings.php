<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$success_msg = '';
$error_msg = '';

$bookings_stmt = $conn->query("SELECT * FROM trial_bookings ORDER BY created_at DESC");
$bookings = [];
while ($row = $bookings_stmt->fetch_assoc()) {
    $bookings[] = $row;
}
$bookings_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_booking') {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        $new_status = trim($_POST['new_status'] ?? 'confirmed');
        
        if ($booking_id > 0) {
            $stmt = $conn->prepare("UPDATE trial_bookings SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $booking_id);
            if ($stmt->execute()) {
                $success_msg = "Booking status updated.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Bookings - PKTS Karate Admin</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include './includes/slidebar.php'; ?>

        <main class="admin-content">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="top-bar-admin">
                <div class="page-header">
                    <h1><i class="fas fa-calendar-check"></i> Trial Bookings</h1>
                    <p>Manage and review trial class registrations</p>
                </div>
                
                <div class="admin-profile-chip">
                    <div class="admin-avatar-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="admin-chip-info">
                        <span class="admin-chip-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                        <span class="admin-chip-role">Active Admin</span>
                    </div>
                </div>
            </div>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Trial Bookings</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th>Preferred Class</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($bookings) > 0): ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($booking['guardian_first_name'] . ' ' . $booking['guardian_last_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['student_age']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                            <td>
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="action" value="update_booking">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
<select name="new_status" class="form-control" style="display: inline-block; width: auto; border-radius: 20px; background: var(--mid-dark); color: var(--white); border: 1px solid var(--border); padding: 6px 12px; font-size: 0.85rem;">
                                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirm</option>
                                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancel</option>
                            </select>
                            <button type="submit" class="btn btn-sm">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="9" class="text-center text-muted">No trial bookings found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>