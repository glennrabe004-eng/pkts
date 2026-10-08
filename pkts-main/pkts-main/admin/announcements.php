<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$msg = '';

// Handle Create Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_announcement') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $target_branch = trim($_POST['target_branch'] ?? 'All');
    $posted_by = $_SESSION['admin_username'] ?? 'Sensei Admin';

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, target_branch, posted_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $content, $target_branch, $posted_by);
        if ($stmt->execute()) {
            $msg = 'Announcement published successfully!';
        } else {
            $msg = 'Error publishing announcement: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle Delete Announcement
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if ($del_id > 0) {
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();
        $msg = 'Announcement deleted.';
    }
}

// Fetch Announcements
$announcements_res = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements – PKTS Admin</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .card-panel { background: #1a1a1a; padding: 24px; border-radius: 12px; margin-bottom: 24px; color: white; }
        .card-panel h2 { font-size: 1.3rem; margin: 0 0 16px; border-bottom: 1px solid #333; padding-bottom: 10px; color: var(--red); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-row input, .form-row select, textarea { background: #252525; color: white; border: 1px solid #444; padding: 10px; border-radius: 6px; width: 100%; box-sizing: border-box; }
    </style>
</head>
<body>
    <?php include 'includes/slidebar.php'; ?>

    <main class="admin-content has-sidebar">
        <header class="content-header" style="margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 1.8rem; margin: 0;">Multi-Branch Announcement System</h1>
                <p style="color: #888; margin: 4px 0 0;">Broadcast news, tournament achievements, and schedule updates to students and parents.</p>
            </div>
        </header>

        <?php if ($msg): ?>
            <div style="padding: 12px 16px; background: #065f46; color: white; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- Create Announcement Form -->
        <div class="card-panel">
            <h2><i class="fas fa-plus-circle"></i> Create New Dojo Announcement</h2>
            <form action="announcements.php" method="POST">
                <input type="hidden" name="action" value="create_announcement">
                <div class="form-row">
                    <div>
                        <label style="font-size: 0.85rem; color: #aaa;">Announcement Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Schedule Change for Robinson Metro East">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #aaa;">Target Branch *</label>
                        <select name="target_branch">
                            <option value="All">All Branches</option>
                            <option value="Robinson Metro East">Robinson Metro East</option>
                            <option value="Vista Mall Antipolo">Vista Mall Antipolo</option>
                            <option value="Marikina Falcon">Marikina Falcon</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.85rem; color: #aaa;">Announcement Content *</label>
                    <textarea name="content" rows="4" required placeholder="Write the details of the announcement here..."></textarea>
                </div>
                <button type="submit" style="background: var(--red); color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-bullhorn"></i> Publish Announcement
                </button>
            </form>
        </div>

        <!-- Announcements Feed List -->
        <div class="card-panel">
            <h2><i class="fas fa-list"></i> Active Announcements</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #333; text-align: left; color: #888;">
                        <th style="padding: 10px;">Title</th>
                        <th style="padding: 10px;">Target Branch</th>
                        <th style="padding: 10px;">Posted By</th>
                        <th style="padding: 10px;">Date Posted</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($announcements_res && $announcements_res->num_rows > 0): ?>
                        <?php while ($a = $announcements_res->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #262626;">
                                <td style="padding: 10px;">
                                    <strong><?php echo htmlspecialchars($a['title']); ?></strong><br>
                                    <small style="color: #aaa;"><?php echo htmlspecialchars(substr($a['content'], 0, 80)); ?>...</small>
                                </td>
                                <td style="padding: 10px; color: var(--red);"><?php echo htmlspecialchars($a['target_branch']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($a['posted_by']); ?></td>
                                <td style="padding: 10px; color: #aaa;"><?php echo date('M d, Y', strtotime($a['created_at'])); ?></td>
                                <td style="padding: 10px;">
                                    <a href="announcements.php?delete=<?php echo $a['id']; ?>" onclick="return confirm('Delete this announcement?')" style="color: #ef4444; text-decoration: none;">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: #888; padding: 20px;">No announcements posted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>
