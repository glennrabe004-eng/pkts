<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$msg = '';

// Handle Sensei Evaluation Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'evaluate_video') {
    $progress_id = intval($_POST['progress_id'] ?? 0);
    $readiness_status = trim($_POST['readiness_status'] ?? 'In Progress');
    $sensei_evaluation = trim($_POST['sensei_evaluation'] ?? '');

    if ($progress_id > 0) {
        $stmt = $conn->prepare("UPDATE student_progress SET readiness_status = ?, sensei_evaluation = ? WHERE id = ?");
        $stmt->bind_param("ssi", $readiness_status, $sensei_evaluation, $progress_id);
        $stmt->execute();
        $stmt->close();
        $msg = 'Sensei evaluation updated!';
    }
}

// Handle Digital Certificate Issuance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'issue_certificate') {
    $student_name = trim($_POST['student_name'] ?? '');
    $branch = trim($_POST['branch'] ?? 'Robinson Metro East');
    $belt_rank = trim($_POST['belt_rank'] ?? 'Yellow Belt');
    $cert_code = 'PKTS-' . strtoupper(substr($branch, 0, 3)) . '-' . rand(10000, 99999);
    $issue_date = date('Y-m-d');

    if (!empty($student_name)) {
        $stmt = $conn->prepare("INSERT INTO certificates (student_name, branch, belt_rank, certificate_code, issue_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $student_name, $branch, $belt_rank, $cert_code, $issue_date);
        if ($stmt->execute()) {
            $msg = 'Digital Certificate issued successfully! Code: ' . $cert_code;
        } else {
            $msg = 'Error issuing certificate: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch submissions
$progress_res = $conn->query("SELECT * FROM student_progress ORDER BY id DESC");
$certs_res = $conn->query("SELECT * FROM certificates ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensei Belt Review & Certificates – PKTS Admin</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .card-panel { background: #1a1a1a; padding: 24px; border-radius: 12px; margin-bottom: 24px; color: white; }
        .card-panel h2 { font-size: 1.3rem; margin: 0 0 16px; border-bottom: 1px solid #333; padding-bottom: 10px; color: var(--red); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-row input, .form-row select { background: #252525; color: white; border: 1px solid #444; padding: 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <?php include 'includes/slidebar.php'; ?>

    <main class="admin-content has-sidebar">
        <header class="content-header" style="margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 1.8rem; margin: 0;">Sensei Belt Review & Certificate Issuance</h1>
                <p style="color: #888; margin: 4px 0 0;">Evaluate student self-assessment videos and issue official digital promotion certificates.</p>
            </div>
        </header>

        <?php if ($msg): ?>
            <div style="padding: 12px 16px; background: #065f46; color: white; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- Issue Certificate Panel -->
        <div class="card-panel">
            <h2><i class="fas fa-award"></i> Issue Digital Belt Advancement Certificate</h2>
            <form action="progress.php" method="POST">
                <input type="hidden" name="action" value="issue_certificate">
                <div class="form-row">
                    <div>
                        <label style="font-size: 0.85rem; color: #aaa;">Student Full Name *</label>
                        <input type="text" name="student_name" required placeholder="e.g. Sophia Santos" style="width: 100%;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #aaa;">Branch *</label>
                        <select name="branch" style="width: 100%;">
                            <option value="Robinson Metro East">Robinson Metro East</option>
                            <option value="Vista Mall Antipolo">Vista Mall Antipolo</option>
                            <option value="Marikina Falcon">Marikina Falcon</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #aaa;">New Belt Rank *</label>
                        <select name="belt_rank" style="width: 100%;">
                            <option>Yellow Belt (9th Kyu)</option>
                            <option>Orange Belt (8th Kyu)</option>
                            <option>Green Belt (7th Kyu)</option>
                            <option>Blue Belt (6th Kyu)</option>
                            <option>Purple Belt (5th Kyu)</option>
                            <option>Brown Belt (3rd-1st Kyu)</option>
                            <option>Black Belt (1st Dan)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" style="background: var(--red); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-certificate"></i> Issue Digital Certificate
                </button>
            </form>
        </div>

        <!-- Student Self-Assessment Video Submissions -->
        <div class="card-panel">
            <h2><i class="fas fa-video"></i> Student Video Submissions & Evaluations</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #333; text-align: left; color: #888;">
                        <th style="padding: 10px;">Student</th>
                        <th style="padding: 10px;">Branch</th>
                        <th style="padding: 10px;">Current -> Target Belt</th>
                        <th style="padding: 10px;">Video Link</th>
                        <th style="padding: 10px;">Readiness</th>
                        <th style="padding: 10px;">Sensei Evaluation</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($progress_res && $progress_res->num_rows > 0): ?>
                        <?php while ($p = $progress_res->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #262626;">
                                <td style="padding: 10px;"><strong><?php echo htmlspecialchars($p['student_name']); ?></strong></td>
                                <td style="padding: 10px; color: var(--red);"><?php echo htmlspecialchars($p['branch']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($p['current_belt']); ?> &rarr; <strong style="color: #22c55e;"><?php echo htmlspecialchars($p['target_belt']); ?></strong></td>
                                <td style="padding: 10px;">
                                    <?php if (!empty($p['video_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($p['video_url']); ?>" target="_blank" style="color: #3b82f6; text-decoration: underline;"><i class="fas fa-external-link-alt"></i> Watch Video</a>
                                    <?php else: ?>
                                        <span style="color: #666;">No link</span>
                                    <?php endif; ?>
                                </td>
                                <form action="progress.php" method="POST">
                                    <input type="hidden" name="action" value="evaluate_video">
                                    <input type="hidden" name="progress_id" value="<?php echo $p['id']; ?>">
                                    <td style="padding: 10px;">
                                        <select name="readiness_status" style="background: #252525; color: white; border: 1px solid #444; padding: 4px; border-radius: 4px;">
                                            <option value="In Progress" <?php echo $p['readiness_status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="Ready for Advancement" <?php echo $p['readiness_status'] === 'Ready for Advancement' ? 'selected' : ''; ?>>Ready for Advancement</option>
                                            <option value="Passed" <?php echo $p['readiness_status'] === 'Passed' ? 'selected' : ''; ?>>Passed</option>
                                            <option value="Needs Practice" <?php echo $p['readiness_status'] === 'Needs Practice' ? 'selected' : ''; ?>>Needs Practice</option>
                                        </select>
                                    </td>
                                    <td style="padding: 10px;">
                                        <input type="text" name="sensei_evaluation" value="<?php echo htmlspecialchars($p['sensei_evaluation'] ?? ''); ?>" placeholder="Sensei feedback notes..." style="background: #252525; color: white; border: 1px solid #444; padding: 4px 8px; border-radius: 4px; width: 100%;">
                                    </td>
                                    <td style="padding: 10px;">
                                        <button type="submit" style="background: var(--red); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Save</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: #888; padding: 20px;">No video submissions yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>
