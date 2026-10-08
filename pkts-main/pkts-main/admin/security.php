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
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_msg = "All fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error_msg = "New passwords do not match.";
        } else {
            $admin_id = $_SESSION['admin_id'] ?? 0;
            $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $stmt->bind_result($db_pass);
            $stmt->fetch();
            $stmt->close();
            
            if (password_verify($current_password, $db_pass)) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_hash, $admin_id);
                if ($stmt->execute()) {
                    $success_msg = "Password changed successfully!";
                } else {
                    $error_msg = "Failed to change password.";
                }
                $stmt->close();
            } else {
                $error_msg = "Current password is incorrect.";
            }
        }
    }
    
    if ($action === 'toggle_two_factor') {
        $admin_id = $_SESSION['admin_id'] ?? 0;
        $stmt = $conn->prepare("UPDATE admin_users SET two_factor = 1 WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();
        $success_msg = "Two-factor authentication enabled.";
    }
}

$admin_stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
$admin_stmt->bind_param("i", $_SESSION['admin_id']);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();
$current_admin = $admin_result->fetch_assoc();
$admin_stmt->close();

$all_admins_stmt = $conn->query("SELECT id, username, email, role, status, last_login, created_at FROM admin_users ORDER BY id ASC");
$all_admins = [];
if ($all_admins_stmt) {
    while ($row = $all_admins_stmt->fetch_assoc()) {
        $all_admins[] = $row;
    }
    $all_admins_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Security - PKTS Karate Admin</title>
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
                    <h1><i class="fas fa-lock"></i> Admin Security</h1>
                    <p>Manage security settings and passwords</p>
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

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>All Admins Secure</h3>
                        <p>Security compliance verified</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $current_admin && $current_admin['last_login'] ? date('M d, Y', strtotime($current_admin['last_login'])) : 'Never'; ?></h3>
                        <p>Last Login</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Change Password</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="settings-form" style="max-width: 400px;">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label for="current_password"><i class="fas fa-lock"></i> Current Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter current password" required>
                                <i class="fas fa-lock left-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password"><i class="fas fa-key"></i> New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
                                <i class="fas fa-key left-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-key"></i> Confirm New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                                <i class="fas fa-key left-icon"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Security Settings</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="two_factor"><i class="fas fa-phone"></i> Two-Factor Authentication</label>
                        <p class="text-muted" style="font-size: 0.9rem; margin-top: 6px;">
                            Add an extra layer of security to your account.
                        </p>
                        <button class="btn btn-sm btn-primary" style="margin-top: 10px;" onclick="alert('2FA feature coming soon!')">
                            <i class="fas fa-mobile-alt"></i> Enable 2FA
                        </button>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label><i class="fas fa-shield-alt"></i> Security Recommendations</label>
                        <ul class="text-muted" style="font-size: 0.9rem; margin-top: 8px; line-height: 1.6;">
                            <li>Use a strong password with at least 8 characters including letters, numbers, and symbols</li>
                            <li>Enable two-factor authentication for additional security</li>
                            <li>Never share your admin credentials with anyone</li>
                            <li>Log out when finished using the admin panel</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Admin Accounts</h2>
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
                                    <th>Last Login</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($all_admins) > 0): ?>
                                    <?php foreach ($all_admins as $admin): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($admin['username']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($admin['role']); ?></span></td>
                                            <td>
                                                <?php if ($admin['username'] === $_SESSION['admin_username']): ?>
                                                    <span class="badge badge-success">YOU</span>
                                                <?php endif; ?>
                                                <span class="badge badge-<?php echo $admin['status'] === 'Active' ? 'success' : 'warning'; ?>">
                                                    <?php echo htmlspecialchars($admin['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">No admin accounts found.</td></tr>
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