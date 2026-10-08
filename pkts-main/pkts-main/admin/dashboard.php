<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$admins_stmt = $conn->prepare("SELECT COUNT(*) as count FROM admin_users");
$admins_stmt->execute();
$admin_result = $admins_stmt->get_result();
$admin_count = $admin_result->fetch_assoc()['count'] ?? 0;

$enrollments_stmt = $conn->query("SELECT COUNT(*) as count FROM enrollments");
$enrollments_count = $enrollments_stmt->fetch_assoc()['count'] ?? 0;

$video_eval_stmt = $conn->query("SELECT COUNT(*) as count FROM student_progress WHERE readiness_status = 'Submitted for Evaluation' OR readiness_status = 'In Progress'");
$pending_videos = $video_eval_stmt->fetch_assoc()['count'] ?? 0;

$announcements_stmt = $conn->query("SELECT COUNT(*) as count FROM announcements");
$announcements_count = $announcements_stmt->fetch_assoc()['count'] ?? 0;

$messages_stmt = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
$unread_messages = 0;
if ($messages_stmt && $row = $messages_stmt->fetch_assoc()) {
    $unread_messages = $row['count'] ?? 0;
    $messages_stmt->close();
}

$metro_count = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE branch = 'Robinson Metro East'")->fetch_assoc()['count'] ?? 0;
$antipolo_count = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE branch = 'Vista Mall Antipolo'")->fetch_assoc()['count'] ?? 0;
$falcon_count = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE branch = 'Marikina Falcon'")->fetch_assoc()['count'] ?? 0;

$recent_customers_stmt = $conn->query("SELECT id, first_name, last_name, email, phone, created_at FROM customers ORDER BY created_at DESC LIMIT 10");
$recent_customers = [];
while ($row = $recent_customers_stmt->fetch_assoc()) {
    $recent_customers[] = $row;
}
$recent_customers_stmt->close();

$recent_bookings_stmt = $conn->query("SELECT * FROM trial_bookings ORDER BY created_at DESC LIMIT 10");
$recent_bookings = [];
while ($row = $recent_bookings_stmt->fetch_assoc()) {
    $recent_bookings[] = $row;
}
$recent_bookings_stmt->close();

$recent_messages_stmt = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10");
$recent_messages = [];
while ($row = $recent_messages_stmt->fetch_assoc()) {
    $recent_messages[] = $row;
}
$recent_messages_stmt->close();

$recent_admins_stmt = $conn->query("SELECT id, username, email, role, status, created_at FROM admin_users ORDER BY created_at DESC LIMIT 10");
$recent_admins = [];
while ($row = $recent_admins_stmt->fetch_assoc()) {
    $recent_admins[] = $row;
}
$recent_admins_stmt->close();

$sensei_stmt = $conn->query("SELECT id, username, email, role, status, created_at FROM admin_users WHERE username LIKE '%sensei%' OR role = 'Instructor' ORDER BY created_at DESC LIMIT 10");
$sensei_staff = [];
while ($row = $sensei_stmt->fetch_assoc()) {
    $sensei_staff[] = $row;
}
$sensei_stmt->close();

$recent_transactions_stmt = $conn->query("
    SELECT 'order' as type, order_number as id, customer_name as title, CONCAT('₱', FORMAT(total_amount, 2)) as amount, created_at as timestamp FROM orders 
    UNION ALL 
    SELECT 'booking' as type, id, CONCAT(guardian_first_name, ' ', guardian_last_name, ' (', time_slot, ')') as title, 'Free Trial', created_at as timestamp FROM trial_bookings 
    UNION ALL 
    SELECT 'message' as type, id, CONCAT(name, ' - ', subject) as title, 'New Message', created_at as timestamp FROM contact_messages 
    ORDER BY timestamp DESC 
    LIMIT 20
");
$recent_transactions = [];
if ($recent_transactions_stmt) {
    while ($row = $recent_transactions_stmt->fetch_assoc()) {
        $recent_transactions[] = $row;
    }
    $recent_transactions_stmt->close();
}

$recent_orders_stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = [];
while ($row = $recent_orders_stmt->fetch_assoc()) {
    $recent_orders[] = $row;
}
$recent_orders_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PKTS Karate Admin</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .top-bar-admin {
            justify-content: center;
            text-align: center;
        }
        .top-bar-admin .page-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        .top-bar-admin .page-header h1 {
            margin: 0;
        }
        .top-bar-admin .page-header p {
            margin: 0;
            color: var(--steel);
            font-size: 1rem;
        }
        .admin-profile-chip {
            justify-content: center;
            margin-top: 16px;
        }
    </style>
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
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                    <p>Welcome back, Batman_Admin!</p>
                </div>
                
                <div class="admin-profile-chip">
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='admins.php'">
                        <i class="fas fa-user-plus"></i> Add Admin
                    </button>
                    <div class="admin-avatar-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="admin-chip-info">
                        <span class="admin-chip-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                        <span class="admin-chip-role">Active Admin</span>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <div class="main-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon primary">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $admin_count; ?></h3>
                                <p>Admin Users</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon secondary">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $products_count; ?></h3>
                                <p>Products</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon success">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $orders_count; ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon warning">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $pending_bookings; ?></h3>
                                <p>Pending Bookings</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon danger">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $unread_messages; ?></h3>
                                <p>Unread Messages</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon secondary">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $customer_count; ?></h3>
                                <p>Registered Customers</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon tertiary">
                                <i class="fas fa-dnas"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?php echo $sensei_count; ?></h3>
                                <p>Sensei Staff</p>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-bolt"></i> Live Transaction Feed</h2>
                            <span class="live-indicator" style="display: inline-flex; align-items: center; gap: 6px; margin-left: auto; color: #22c55e; font-size: 0.85rem;">
                                <span class="live-dot" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 1.5s infinite;"></span>
                                Live
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="transaction-feed" id="transactionFeed">
                                <?php if (count($recent_transactions) > 0): ?>
                                    <?php foreach ($recent_transactions as $tx): ?>
                                        <div class="transaction-item" data-type="<?php echo htmlspecialchars($tx['type']); ?>" data-timestamp="<?php echo strtotime($tx['timestamp']); ?>">
                                            <div class="tx-icon tx-<?php echo $tx['type']; ?>">
                                                <?php if ($tx['type'] === 'order'): ?>
                                                    <i class="fas fa-shopping-cart"></i>
                                                <?php elseif ($tx['type'] === 'booking'): ?>
                                                    <i class="fas fa-calendar-plus"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-envelope"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="tx-details">
                                                <div class="tx-title"><?php echo htmlspecialchars($tx['title']); ?></div>
                                                <div class="tx-time"><?php echo date('H:i:s', strtotime($tx['timestamp'])); ?></div>
                                            </div>
                                            <div class="tx-amount"><?php echo htmlspecialchars($tx['amount'] ?? '-'); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">No recent transactions</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2><i class="fas fa-list"></i> Recent Orders</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($recent_orders) > 0): ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                                            <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center text-muted">No orders found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-sections">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h2><i class="fas fa-user-friends"></i> Recent Customers</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Member Since</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_customers) > 0): ?>
                                                <?php foreach ($recent_customers as $customer): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center text-muted">No customers found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="card-header">
                                <h2><i class="fas fa-calendar-check"></i> Recent Trial Bookings</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_bookings) > 0): ?>
                                                <?php foreach ($recent_bookings as $booking): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($booking['guardian_first_name'] . ' ' . $booking['guardian_last_name']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($booking['trial_date'])); ?></td>
                                                        <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                                        <td><span class="badge badge-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning'); ?>"><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></span></td>
                                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center text-muted">No bookings found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </div>

                    <div class="table-sections">
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h2><i class="fas fa-envelope"></i> Recent Contact Messages</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_messages) > 0): ?>
                                                <?php foreach ($recent_messages as $msg): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($msg['subject'] ?? 'No subject'); ?></td>
                                                        <td><span class="badge badge-<?php echo $msg['status'] === 'read' ? 'success' : ($msg['status'] === 'replied' ? 'info' : 'warning'); ?>"><?php echo ucfirst(htmlspecialchars($msg['status'])); ?></span></td>
                                                        <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center text-muted">No messages found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="card-header">
                                <h2><i class="fas fa-user-shield"></i> Administrators</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recent_admins) > 0): ?>
                                                <?php foreach ($recent_admins as $admin): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($admin['role']); ?></span></td>
                                                        <td><span class="badge badge-<?php echo $admin['status'] === 'Active' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($admin['status']); ?></span></td>
                                                        <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center text-muted">No administrators found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="card-header">
                                <h2><i class="fas fa-dna"></i> Sensei Staff</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($sensei_staff) > 0): ?>
                                                <?php foreach ($sensei_staff as $sensei): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($sensei['username']); ?></td>
                                                        <td><?php echo htmlspecialchars($sensei['email']); ?></td>
                                                        <td><span class="badge badge-success"><?php echo htmlspecialchars($sensei['role']); ?></span></td>
                                                        <td><span class="badge badge-<?php echo $sensei['status'] === 'Active' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($sensei['status']); ?></span></td>
                                                        <td><?php echo date('M d, Y', strtotime($sensei['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center text-muted">No sensei staff found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    (function() {
        const feed = document.getElementById('transactionFeed');
        if (!feed) return;
        
        function formatTime(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleTimeString('en-US', { hour12: false });
        }
        
        function updateTimes() {
            const items = feed.querySelectorAll('.transaction-item');
            items.forEach(item => {
                const timestamp = parseInt(item.dataset.timestamp);
                const timeEl = item.querySelector('.tx-time');
                if (timeEl) {
                    timeEl.textContent = formatTime(timestamp);
                }
            });
        }
        
        function renderTransactions(transactions) {
            if (!feed) return;
            if (transactions.length === 0) {
                feed.innerHTML = '<div class="text-center text-muted py-3">No recent transactions</div>';
                return;
            }
            
            let html = '';
            transactions.forEach(tx => {
                const icon = tx.type === 'order' ? 'fa-shopping-cart' : 
                             tx.type === 'booking' ? 'fa-calendar-plus' : 'fa-envelope';
                const iconClass = `tx-${tx.type}`;
                
                html += `<div class="transaction-item" data-type="${tx.type}" data-timestamp="${tx.timestamp}">
                    <div class="tx-icon ${iconClass}"><i class="fas ${icon}"></i></div>
                    <div class="tx-details">
                        <div class="tx-title">${tx.title}</div>
                        <div class="tx-time">${formatTime(tx.timestamp)}</div>
                    </div>
                    <div class="tx-amount">${tx.amount}</div>
                </div>`;
            });
            feed.innerHTML = html;
        }
        
        async function fetchTransactions() {
            try {
                const response = await fetch('api/transactions.php');
                if (response.ok) {
                    const data = await response.json();
                    renderTransactions(data.transactions);
                }
            } catch (e) {
                console.log('Auto-refresh not available');
            }
        }
        
        setInterval(updateTimes, 1000);
        setInterval(fetchTransactions, 5000);
        updateTimes();
    })();
    </script>
</body>
</html>