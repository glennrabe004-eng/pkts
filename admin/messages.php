<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'mark_read') {
        $msg_id = intval($_POST['message_id'] ?? 0);
        if ($msg_id > 0) {
            $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
            $stmt->bind_param("i", $msg_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    if ($action === 'delete_message') {
        $msg_id = intval($_POST['message_id'] ?? 0);
        if ($msg_id > 0) {
            $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $msg_id);
            $stmt->execute();
            $stmt->close();
            $success_msg = "Message deleted.";
        }
    }
}

$messages_stmt = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = [];
if ($messages_stmt) {
    while ($row = $messages_stmt->fetch_assoc()) {
        $messages[] = $row;
    }
    $messages_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - PKTS Karate Admin</title>
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
                    <h1><i class="fas fa-comments"></i> Messages</h1>
                    <p>View and respond to customer inquiries</p>
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
                    <h2>Inbox Messages</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Received</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($messages) > 0): ?>
                                    <?php foreach ($messages as $msg): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                            <td><?php echo htmlspecialchars($msg['subject'] ?? 'No Subject'); ?></td>
                                            <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50) . (strlen($msg['message']) > 50 ? '...' : '')); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                                            <td>
                                                <?php if ($msg['status'] === 'read'): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Read</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Unread</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i> Mark Read</button>
                                                </form>
                                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                                                    <input type="hidden" name="action" value="delete_message">
                                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                    <button type="submit" class="btn btn-sm" style="background: var(--red);"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">No messages found.</td></tr>
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