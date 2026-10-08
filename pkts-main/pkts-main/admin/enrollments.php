<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $enrollment_id = intval($_POST['enrollment_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');

    if ($enrollment_id > 0) {
        $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $enrollment_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch Enrollments
$selected_branch = $_GET['branch'] ?? 'All';
if ($selected_branch !== 'All') {
    $stmt = $conn->prepare("SELECT * FROM enrollments WHERE branch = ? ORDER BY id DESC");
    $stmt->bind_param("s", $selected_branch);
    $stmt->execute();
    $enrollments_res = $stmt->get_result();
} else {
    $enrollments_res = $conn->query("SELECT * FROM enrollments ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Enrollments & Health Clearance – PKTS Admin Portal</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/slidebar.php'; ?>

    <main class="admin-content has-sidebar">
        <header class="content-header" style="margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 1.8rem; margin: 0;">Online Enrollments & Medical Clearance</h1>
                <p style="color: #888; margin: 4px 0 0;">Review pre-assessment health notes, medical certificates, and liability waivers.</p>
            </div>
        </header>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <a href="enrollments.php?branch=All" class="btn" style="padding: 8px 16px; background: <?php echo $selected_branch === 'All' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">All Branches</a>
            <a href="enrollments.php?branch=Robinson+Metro+East" class="btn" style="padding: 8px 16px; background: <?php echo $selected_branch === 'Robinson Metro East' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Robinson Metro East</a>
            <a href="enrollments.php?branch=Vista+Mall+Antipolo" class="btn" style="padding: 8px 16px; background: <?php echo $selected_branch === 'Vista Mall Antipolo' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Vista Mall Antipolo</a>
            <a href="enrollments.php?branch=Marikina+Falcon" class="btn" style="padding: 8px 16px; background: <?php echo $selected_branch === 'Marikina Falcon' ? 'var(--red)' : '#333'; ?>; color: white; border-radius: 6px; text-decoration: none;">Marikina Falcon</a>
        </div>

        <div class="card" style="background: #1a1a1a; padding: 20px; border-radius: 12px;">
            <table style="width: 100%; border-collapse: collapse; color: white;">
                <thead>
                    <tr style="border-bottom: 1px solid #333; text-align: left; color: #888;">
                        <th style="padding: 12px;">Student & Guardian</th>
                        <th style="padding: 12px;">Branch & Schedule</th>
                        <th style="padding: 12px;">Health Notes / Med Cert</th>
                        <th style="padding: 12px;">Waiver</th>
                        <th style="padding: 12px;">Enrollment Status</th>
                        <th style="padding: 12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($enrollments_res && $enrollments_res->num_rows > 0): ?>
                        <?php while ($e = $enrollments_res->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #262626;">
                                <td style="padding: 12px;">
                                    <strong><?php echo htmlspecialchars($e['student_first_name'] . ' ' . $e['student_last_name']); ?></strong> (Age: <?php echo intval($e['student_age']); ?>)<br>
                                    <small style="color: #aaa;">Guardian: <?php echo htmlspecialchars($e['guardian_first_name'] . ' ' . $e['guardian_last_name']); ?> | <?php echo htmlspecialchars($e['phone']); ?></small>
                                </td>
                                <td style="padding: 12px; color: #ccc;">
                                    <strong style="color: var(--red);"><?php echo htmlspecialchars($e['branch']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($e['trial_date'] . ' ' . $e['time_slot']); ?></small>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if (!empty($e['pre_assessment_notes'])): ?>
                                        <p style="margin: 0 0 6px; font-size: 0.85rem; color: #fbbf24;"><i class="fas fa-notes-medical"></i> <?php echo htmlspecialchars($e['pre_assessment_notes']); ?></p>
                                    <?php else: ?>
                                        <span style="color: #777; font-size: 0.85rem;">No pre-existing notes</span><br>
                                    <?php endif; ?>

                                    <?php if (!empty($e['medical_certificate'])): ?>
                                        <a href="../<?php echo htmlspecialchars($e['medical_certificate']); ?>" target="_blank" style="color: #3b82f6; text-decoration: underline; font-size: 0.85rem;"><i class="fas fa-file-medical"></i> View Medical Certificate</a>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if ($e['liability_waiver_accepted']): ?>
                                        <span style="color: #22c55e;"><i class="fas fa-check-circle"></i> Accepted</span>
                                    <?php else: ?>
                                        <span style="color: #ef4444;"><i class="fas fa-times-circle"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; background: #333; color: white;">
                                        <?php echo strtoupper(htmlspecialchars($e['status'])); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <form action="enrollments.php" method="POST" style="display: flex; gap: 6px;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="enrollment_id" value="<?php echo $e['id']; ?>">
                                        <select name="status" style="background: #252525; color: white; border: 1px solid #444; padding: 4px 8px; border-radius: 4px;">
                                            <option value="pending" <?php echo $e['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="under_review" <?php echo $e['status'] === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                            <option value="approved" <?php echo $e['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $e['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit" style="background: var(--red); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; color: #888; padding: 30px;">No online enrollments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
