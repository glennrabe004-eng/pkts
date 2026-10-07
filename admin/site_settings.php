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
    if (isset($_POST['action']) && $_POST['action'] === 'save_settings') {
        $settings_input = $_POST['settings'] ?? [];
        
        $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($settings_input as $key => $val) {
            $key_clean = trim($key);
            $val_clean = trim($val);
            $stmt->bind_param("ss", $key_clean, $val_clean);
            $stmt->execute();
        }
        $stmt->close();
        
        $success_msg = "Website details & content settings saved successfully!";
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'add_custom_setting') {
        $new_key = trim($_POST['custom_key'] ?? '');
        $new_val = trim($_POST['custom_value'] ?? '');
        
        if (!empty($new_key)) {
            $key_slug = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $new_key));
            $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->bind_param("ss", $key_slug, $new_val);
            if ($stmt->execute()) {
                $success_msg = "Custom detail '$key_slug' added to website settings!";
            }
            $stmt->close();
        } else {
            $error_msg = "Please specify a setting key name.";
        }
    }
}

$settings_result = $conn->query("SELECT * FROM site_settings");
$current_settings = [];
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Website Details - PKTS Karate Admin</title>
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
                    <h1><i class="fas fa-edit"></i> Edit Website Details & Content</h1>
                    <p>Customize titles, contact info, announcements, hero text, and website content.</p>
                </div>
                
                <div class="admin-profile-chip">
                    <div class="admin-avatar-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="admin-chip-info">
                        <span class="admin-chip-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <span class="admin-chip-role">Active Admin</span>
                    </div>
                </div>
            </div>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2>General Website Information</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_settings">
                        
                        <div class="form-group">
                            <label for="site_title"><i class="fas fa-heading"></i> Website Title</label>
                            <input type="text" id="site_title" name="settings[site_title]" class="form-control" value="<?php echo htmlspecialchars($current_settings['site_title'] ?? 'PKTS Karate Dojo - Pasig City'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="announcement_banner"><i class="fas fa-bullhorn"></i> Announcement Banner</label>
                            <input type="text" id="announcement_banner" name="settings[announcement_banner]" class="form-control" value="<?php echo htmlspecialchars($current_settings['announcement_banner'] ?? ''); ?>" placeholder="e.g. Free Trial Class Available for New Students!">
                        </div>

                        <div class="form-group">
                            <label for="hero_headline"><i class="fas fa-star"></i> Hero Headline</label>
                            <input type="text" id="hero_headline" name="settings[hero_headline]" class="form-control" value="<?php echo htmlspecialchars($current_settings['hero_headline'] ?? ''); ?>" placeholder="TRAIN WITH PURPOSE. MASTER THE ART.">
                        </div>

                        <div class="form-group">
                            <label for="hero_subtext"><i class="fas fa-align-left"></i> Hero Subheadline</label>
                            <textarea id="hero_subtext" name="settings[hero_subtext]" rows="3" class="form-control"><?php echo htmlspecialchars($current_settings['hero_subtext'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="dojo_address"><i class="fas fa-map-marker-alt"></i> Dojo Address</label>
                            <input type="text" id="dojo_address" name="settings[dojo_address]" class="form-control" value="<?php echo htmlspecialchars($current_settings['dojo_address'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_phone"><i class="fas fa-phone"></i> Contact Phone</label>
                            <input type="text" id="contact_phone" name="settings[contact_phone]" class="form-control" value="<?php echo htmlspecialchars($current_settings['contact_phone'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="contact_email"><i class="fas fa-envelope"></i> Contact Email</label>
                            <input type="email" id="contact_email" name="settings[contact_email]" class="form-control" value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save"></i> Save Website Changes
                        </button>
                    </form>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-plus"></i> Add Custom Website Detail</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="settings-form">
                        <input type="hidden" name="action" value="add_custom_setting">
                        
                        <div class="form-group">
                            <label for="custom_key">Detail Key</label>
                            <input type="text" id="custom_key" name="custom_key" class="form-control" required placeholder="e.g. facebook_url, dojo_hours, class_schedule_note">
                        </div>
                        
                        <div class="form-group">
                            <label for="custom_value">Detail Value</label>
                            <input type="text" id="custom_value" name="custom_value" class="form-control" required placeholder="Enter the content here...">
                        </div>
                        
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-plus-circle"></i> Add Custom Website Detail
                        </button>
                    </form>
                </div>
            </div>

            <?php foreach ($current_settings as $key => $val): ?>
                <?php 
                $standard_keys = ['site_title', 'announcement_banner', 'hero_headline', 'hero_subtext', 'dojo_address', 'contact_phone', 'contact_email'];
                if (!in_array($key, $standard_keys)):
                ?>
                    <div class="custom-setting-card">
                        <label class="card-header">
                            <span><i class="fas fa-tag"></i> <code><?php echo htmlspecialchars($key); ?></code></span>
                        </label>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="save_settings">
                                <input type="hidden" name="settings[<?php echo htmlspecialchars($key); ?>]" value="<?php echo htmlspecialchars($val); ?>">
                                <button type="submit" class="btn btn-sm">Update Value</button>
                            </form>
                        </div>
                    </div>
                <?php 
                endif;
                endforeach; 
                ?>

        </main>
    </div>
</body>
</html>
