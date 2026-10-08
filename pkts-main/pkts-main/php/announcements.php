<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

$selected_branch = $_GET['branch'] ?? 'All';

if ($selected_branch !== 'All') {
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE target_branch = 'All' OR target_branch = ? ORDER BY id DESC");
    $stmt->bind_param("s", $selected_branch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dojo Announcements – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css">
  <style>
    body { font-family: 'Inter', sans-serif !important; background: #f8f9fa; }
    .announcement-hero {
      background: linear-gradient(rgba(10, 10, 10, 0.85), rgba(20, 20, 20, 0.9)), url("../background/BG1.png") center/cover no-repeat;
      color: white;
      padding: 120px 20px 60px;
      text-align: center;
    }
    .announcement-hero h1 { font-family: var(--font-display); font-size: 2.5rem; margin-bottom: 10px; }
    .announcement-container { max-width: 900px; margin: -40px auto 60px; padding: 0 20px; }
    .filter-bar { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
    .announcement-card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border-left: 5px solid var(--red); }
    .announcement-card h2 { font-family: var(--font-display); font-size: 1.4rem; margin: 0 0 10px; color: #111; }
    .announcement-meta { font-size: 0.85rem; color: #777; margin-bottom: 14px; display: flex; gap: 15px; align-items: center; }
    .branch-badge { background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 4px; font-weight: 600; }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>

  <section class="announcement-hero">
    <h1><span style="color: var(--red);">Dojo</span> Announcements</h1>
    <p>Stay updated with schedule changes, tournaments, and belt ranking announcements.</p>
  </section>

  <div class="announcement-container">

    <div class="filter-bar">
      <strong style="color: #333;"><i class="fas fa-filter" style="color: var(--red);"></i> Filter Branch:</strong>
      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="announcements.php?branch=All" class="btn" style="padding: 6px 16px; border-radius: 20px; background: <?php echo $selected_branch === 'All' ? 'var(--red)' : '#eee'; ?>; color: <?php echo $selected_branch === 'All' ? 'white' : '#333'; ?>; text-decoration: none; font-size: 0.9rem;">All Branches</a>
        <a href="announcements.php?branch=Robinson+Metro+East" class="btn" style="padding: 6px 16px; border-radius: 20px; background: <?php echo $selected_branch === 'Robinson Metro East' ? 'var(--red)' : '#eee'; ?>; color: <?php echo $selected_branch === 'Robinson Metro East' ? 'white' : '#333'; ?>; text-decoration: none; font-size: 0.9rem;">Robinson Metro East</a>
        <a href="announcements.php?branch=Vista+Mall+Antipolo" class="btn" style="padding: 6px 16px; border-radius: 20px; background: <?php echo $selected_branch === 'Vista Mall Antipolo' ? 'var(--red)' : '#eee'; ?>; color: <?php echo $selected_branch === 'Vista Mall Antipolo' ? 'white' : '#333'; ?>; text-decoration: none; font-size: 0.9rem;">Vista Mall Antipolo</a>
        <a href="announcements.php?branch=Marikina+Falcon" class="btn" style="padding: 6px 16px; border-radius: 20px; background: <?php echo $selected_branch === 'Marikina Falcon' ? 'var(--red)' : '#eee'; ?>; color: <?php echo $selected_branch === 'Marikina Falcon' ? 'white' : '#333'; ?>; text-decoration: none; font-size: 0.9rem;">Marikina Falcon</a>
      </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement-card">
          <h2><?php echo htmlspecialchars($row['title']); ?></h2>
          <div class="announcement-meta">
            <span class="branch-badge"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['target_branch']); ?></span>
            <span><i class="fas fa-user-shield"></i> Posted by: <?php echo htmlspecialchars($row['posted_by']); ?></span>
            <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></span>
          </div>
          <p style="color: #444; line-height: 1.6; margin: 0;"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: #888;">
        <i class="fas fa-bullhorn" style="font-size: 2.5rem; margin-bottom: 12px; color: #ccc;"></i>
        <p>No announcements found for this branch.</p>
      </div>
    <?php endif; ?>

  </div>

  <?php require_once __DIR__ . '/layout/footer.php'; ?>
</body>
</html>
