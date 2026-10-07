<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

// Handle self-assessment video submission
$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_video') {
    $student_name = trim($_POST['student_name'] ?? '');
    $branch = trim($_POST['branch'] ?? 'Robinson Metro East');
    $current_belt = trim($_POST['current_belt'] ?? 'White Belt (10th Kyu)');
    $target_belt = trim($_POST['target_belt'] ?? 'Yellow Belt (9th Kyu)');
    $video_url = trim($_POST['video_url'] ?? '');

    if (!empty($student_name) && !empty($video_url)) {
        $stmt = $conn->prepare("INSERT INTO student_progress (student_name, branch, current_belt, target_belt, video_url, readiness_status) VALUES (?, ?, ?, ?, ?, 'Submitted for Evaluation')");
        if ($stmt) {
            $stmt->bind_param("sssss", $student_name, $branch, $current_belt, $target_belt, $video_url);
            if ($stmt->execute()) {
                $msg = 'Your self-assessment video has been submitted for Sensei evaluation!';
                $msg_type = 'success';
            } else {
                $msg = 'Error submitting video: ' . $stmt->error;
                $msg_type = 'danger';
            }
            $stmt->close();
        }
    } else {
        $msg = 'Please fill out student name and self-assessment video link.';
        $msg_type = 'danger';
    }
}

// Fetch belt references
$belts_query = "SELECT * FROM belt_references ORDER BY id ASC";
$belts_res = $conn->query($belts_query);

// Fetch recent student progress submissions
$progress_query = "SELECT * FROM student_progress ORDER BY id DESC LIMIT 10";
$progress_res = $conn->query($progress_query);

// Fetch issued certificates
$certs_query = "SELECT * FROM certificates ORDER BY id DESC LIMIT 10";
$certs_res = $conn->query($certs_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gakusei Belt Progress & Certificates – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css">
  <style>
    body { font-family: 'Inter', sans-serif !important; background: #f4f6f8; }
    .progress-hero {
      background: linear-gradient(rgba(10, 10, 10, 0.85), rgba(20, 20, 20, 0.9)), url("../background/BG1.png") center/cover no-repeat;
      color: white;
      padding: 120px 20px 60px;
      text-align: center;
    }
    .progress-hero h1 { font-family: var(--font-display); font-size: 2.5rem; margin-bottom: 10px; }
    .progress-container { max-width: 1100px; margin: -40px auto 60px; padding: 0 20px; }
    .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px; }
    .card-title { font-family: var(--font-display); font-size: 1.5rem; color: #111; margin-bottom: 20px; border-bottom: 2px solid var(--red-light); padding-bottom: 10px; }
    .belt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .belt-card { border: 1px solid #eee; border-left: 6px solid var(--red); border-radius: 8px; padding: 16px; background: #fafafa; }
    .belt-card h3 { margin: 0 0 8px; font-size: 1.1rem; }
    .belt-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 10px; background: #e02020; color: white; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    .btn-submit { background: var(--red); color: white; border: none; padding: 12px 24px; border-radius: 30px; font-weight: 700; cursor: pointer; }
    .btn-submit:hover { background: #b91c1c; }
    .alert { padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
    .alert-success { background: #d1fae5; color: #065f46; }
    .alert-danger { background: #fee2e2; color: #991b1b; }
    .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    .data-table th { background: #f8f9fa; font-weight: 600; }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>

  <section class="progress-hero">
    <h1><span style="color: var(--red);">Gakusei</span> Progress & Belt System</h1>
    <p>Track belt requirements, submit self-assessment videos for Sensei evaluation, and view earned certificates.</p>
  </section>

  <div class="progress-container">

    <?php if ($msg): ?>
      <div class="alert alert-<?php echo $msg_type; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- Self Assessment Video Submission -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-video" style="color: var(--red);"></i> Submit Self-Assessment Video for Sensei Evaluation</h2>
      <p style="color: #666; margin-bottom: 20px;">Upload a link to your practice video (YouTube, Google Drive, Vimeo, etc.) for your Sensei to review your readiness for the next belt promotion exam.</p>

      <form action="progress.php" method="POST">
        <input type="hidden" name="action" value="submit_video">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
          <div class="form-group">
            <label>Student Full Name *</label>
            <input type="text" name="student_name" required placeholder="e.g. Marco Dela Cruz">
          </div>
          <div class="form-group">
            <label>Dojo Branch *</label>
            <select name="branch" required>
              <option value="Robinson Metro East">Robinson Metro East</option>
              <option value="Vista Mall Antipolo">Vista Mall Antipolo</option>
              <option value="Marikina Falcon">Marikina Falcon</option>
            </select>
          </div>
          <div class="form-group">
            <label>Current Belt Rank</label>
            <select name="current_belt">
              <option>White Belt (10th Kyu)</option>
              <option>Yellow Belt (9th Kyu)</option>
              <option>Orange Belt (8th Kyu)</option>
              <option>Green Belt (7th Kyu)</option>
              <option>Blue Belt (6th Kyu)</option>
              <option>Purple Belt (5th Kyu)</option>
              <option>Brown Belt (3rd-1st Kyu)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Target Belt Rank</label>
            <select name="target_belt">
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

        <div class="form-group">
          <label>Self-Assessment Video Link (URL) *</label>
          <input type="url" name="video_url" required placeholder="https://youtube.com/watch?v=... or Google Drive Link">
        </div>

        <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Submit Video for Review</button>
      </form>
    </div>

    <!-- Belt Reference Table -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-layer-group" style="color: var(--red);"></i> Belt Reference & Promotion Requirements</h2>
      <div class="belt-grid">
        <?php if ($belts_res && $belts_res->num_rows > 0): ?>
          <?php while ($b = $belts_res->fetch_assoc()): ?>
            <div class="belt-card">
              <h3><?php echo htmlspecialchars($b['belt_name']); ?></h3>
              <span class="belt-badge">Min. <?php echo intval($b['minimum_training_months']); ?> Months Training</span>
              <p><strong>Kata:</strong> <?php echo htmlspecialchars($b['kata_required']); ?></p>
              <p><strong>Kumite:</strong> <?php echo htmlspecialchars($b['kumite_required']); ?></p>
              <p style="font-size: 0.85rem; color: #555; margin-top: 8px;"><?php echo htmlspecialchars($b['description']); ?></p>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No belt references found.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Video Evaluations & Progress -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-tasks" style="color: var(--red);"></i> Student Evaluations & Status</h2>
      <table class="data-table">
        <thead>
          <tr>
            <th>Student Name</th>
            <th>Branch</th>
            <th>Current Belt</th>
            <th>Target Belt</th>
            <th>Readiness Status</th>
            <th>Sensei Evaluation</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($progress_res && $progress_res->num_rows > 0): ?>
            <?php while ($p = $progress_res->fetch_assoc()): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($p['student_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['branch']); ?></td>
                <td><?php echo htmlspecialchars($p['current_belt']); ?></td>
                <td><?php echo htmlspecialchars($p['target_belt']); ?></td>
                <td>
                  <span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; background: #e0f2fe; color: #0369a1;">
                    <?php echo htmlspecialchars($p['readiness_status']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($p['sensei_evaluation'] ?? 'Pending evaluation'); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center; color:#888;">No evaluation records yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Digital Certificates -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-certificate" style="color: var(--red);"></i> Issued Digital Certificates</h2>
      <table class="data-table">
        <thead>
          <tr>
            <th>Certificate Code</th>
            <th>Student Name</th>
            <th>Branch</th>
            <th>Belt Rank</th>
            <th>Issue Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($certs_res && $certs_res->num_rows > 0): ?>
            <?php while ($c = $certs_res->fetch_assoc()): ?>
              <tr>
                <td><code><?php echo htmlspecialchars($c['certificate_code']); ?></code></td>
                <td><strong><?php echo htmlspecialchars($c['student_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($c['branch']); ?></td>
                <td><?php echo htmlspecialchars($c['belt_rank']); ?></td>
                <td><?php echo htmlspecialchars($c['issue_date']); ?></td>
                <td>
                  <button onclick="window.print()" style="padding: 4px 10px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-print"></i> Print / View
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center; color:#888;">No certificates issued yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

  <?php require_once __DIR__ . '/layout/footer.php'; ?>
</body>
</html>
