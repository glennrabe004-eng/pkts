<?php
session_start();

// Protection: Only logged-in admins can access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$success_msg = '';
$error_msg = '';

// Process Admin Addition or Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_admin') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'Admin');

        if (!empty($username) && !empty($email) && !empty($password)) {
            // Check if username or email exists
            $chk = $conn->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
            $chk->bind_param("ss", $username, $email);
            $chk->execute();
            $res = $chk->get_result();
            if ($res->num_rows > 0) {
                $error_msg = "An administrator with that username or email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password, role, status) VALUES (?, ?, ?, ?, 'Active')");
                $stmt->bind_param("ssss", $username, $email, $hashed, $role);
                if ($stmt->execute()) {
                    $success_msg = "New administrator '$username' created successfully!";
                } else {
                    $error_msg = "Failed to create admin user.";
                }
                $stmt->close();
            }
            $chk->close();
        } else {
            $error_msg = "Username, email, and password are required.";
        }
    }

    if ($action === 'toggle_status') {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? 'Active';
        
        if ($admin_id > 0) {
            $stmt = $conn->prepare("UPDATE admin_users SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $admin_id);
            if ($stmt->execute()) {
                $success_msg = "Admin status updated to $new_status.";
            }
            $stmt->close();
        }
    }

    if ($action === 'delete_admin') {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        // Prevent deleting yourself
        if ($admin_id > 0) {
            $check_self = $conn->query("SELECT username FROM admin_users WHERE id = $admin_id")->fetch_assoc();
            if ($check_self && $check_self['username'] === $_SESSION['admin_username']) {
                $error_msg = "You cannot delete your own logged-in admin account!";
            } else {
                $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                if ($stmt->execute()) {
                    $success_msg = "Admin user removed.";
                }
                $stmt->close();
            }
        }
    }
}

// Fetch all registered admins
$admins_list = $conn->query("SELECT * FROM admin_users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Team & Privileges - PKTS Karate Admin</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 999; justify-content: center; align-items: center; padding: 20px; }
        .modal.active { display: flex; }
        .modal-content { background: var(--card-bg); border: 1px solid var(--border); width: 100%; max-width: 500px; border-radius: var(--r-lg); padding: 28px; box-shadow: var(--shadow-xl); max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
        .modal-header h2 { margin: 0; color: var(--white); font-family: var(--font-display); font-size: 1.5rem; text-transform: uppercase; letter-spacing: -0.5px; }
        .close-btn { background: none; border: none; font-size: 1.8rem; cursor: pointer; color: var(--steel); transition: color 0.3s; }
        .close-btn:hover { color: var(--white); }
        .admin-icon-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--red), var(--red-dark)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; border: 2px solid rgba(224, 32, 32, 0.5); }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
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
                    <h1><i class="fas fa-user-shield"></i> Admin Team & Privileges</h1>
                    <p>View, manage, and grant administrative access to Dojo staff and managers.</p>
                </div>
                
                <div class="admin-profile-chip">
                    <div class="admin-avatar-icon">
                        <i class="fas fa-user-ninja"></i>
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

            <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-user-plus"></i> Add New Administrator
                </button>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-users-cog"></i> Registered Administrator Accounts</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Admin Avatar</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Role / Title</th>
                                    <th>Last Active</th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($admins_list && $admins_list->num_rows > 0): ?>
                                    <?php while ($adm = $admins_list->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="admin-icon-avatar">
                                                    <i class="fas fa-user-shield"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($adm['username']); ?></strong>
                                                <?php if ($adm['username'] === $_SESSION['admin_username']): ?>
                                                    <span class="badge badge-danger" style="font-size:0.68rem; margin-left:6px;">YOU</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($adm['email']); ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($adm['role'] ?? 'Admin'); ?></span></td>
                                            <td>
                                                <small style="color:#aaa;">
                                                    <?php echo !empty($adm['last_login']) ? date('M d, Y h:i A', strtotime($adm['last_login'])) : 'Active Now'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo ($adm['status'] ?? 'Active') === 'Active' ? 'success' : 'warning'; ?>">
                                                    <?php echo htmlspecialchars($adm['status'] ?? 'Active'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($adm['username'] !== $_SESSION['admin_username']): ?>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="admin_id" value="<?php echo $adm['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo ($adm['status'] ?? 'Active') === 'Active' ? 'Suspended' : 'Active'; ?>">
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                        <span><?php echo ($adm['status'] ?? 'Active') === 'Active' ? 'Suspend' : 'Activate'; ?></span>
                                                    </button>
                                                    
                                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Remove admin access for <?php echo htmlspecialchars($adm['username']); ?>?');">
                                                        <input type="hidden" name="action" value="delete_admin">
                                                        <input type="hidden" name="admin_id" value="<?php echo $adm['id']; ?>">
                                                        <button type="submit" class="btn btn-sm" style="background: var(--red);"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:0.85rem;"><i class="fas fa-lock"></i> Current Account</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">No administrators found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Add New Admin -->
    <div class="modal" id="addAdminModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New Administrator</h2>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_admin">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="e.g. sensei_mark">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="mark@pktskarate.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>Role / Title</label>
                    <select name="role">
                        <option value="Admin">Administrator</option>
                        <option value="Dojo Manager">Dojo Manager</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">
                    <i class="fas fa-check-circle"></i> Create Administrator Account
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() { document.getElementById('addAdminModal').classList.add('active'); }
        function closeAddModal() { document.getElementById('addAdminModal').classList.remove('active'); }
    </script>
</body>
</html>
