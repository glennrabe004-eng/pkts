<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_check.php';

$customerId = $_SESSION['customer_id'] ?? null;
if (!$customerId) {
    header("Location: login.php");
    exit;
}

$profile_columns = [
    'dob' => 'DATE DEFAULT NULL',
    'avatar' => 'VARCHAR(255) DEFAULT NULL',
    'last_login' => 'TIMESTAMP NULL DEFAULT NULL',
    'status' => "VARCHAR(50) DEFAULT 'Active'",
    'language' => "VARCHAR(10) DEFAULT 'en'",
    'currency' => "VARCHAR(10) DEFAULT 'PHP'",
    'newsletter' => 'TINYINT(1) DEFAULT 1',
    'loyalty_points' => 'INT DEFAULT 150',
    'size_preference' => 'VARCHAR(100) DEFAULT NULL',
    'favorite_categories' => 'VARCHAR(255) DEFAULT NULL',
    'notification_preferences' => "VARCHAR(255) DEFAULT 'email,sms'",
    'two_factor' => 'TINYINT(1) DEFAULT 0',
    'login_history' => 'TEXT DEFAULT NULL',
    'shipping_addresses' => 'TEXT DEFAULT NULL',
    'billing_addresses' => 'TEXT DEFAULT NULL',
    'payment_methods' => 'TEXT DEFAULT NULL',
    'wishlist' => 'TEXT DEFAULT NULL',
    'ticket_history' => 'TEXT DEFAULT NULL',
    'message_history' => 'TEXT DEFAULT NULL',
    'linked_social' => 'TEXT DEFAULT NULL'
];

foreach ($profile_columns as $col => $definition) {
    $chk = $conn->query("SHOW COLUMNS FROM customers LIKE '$col'");
    if ($chk && $chk->num_rows === 0) {
        $conn->query("ALTER TABLE customers ADD COLUMN $col $definition");
    }
}

if (!isset($_SESSION['login_logged'])) {
    $conn->query("UPDATE customers SET last_login = CURRENT_TIMESTAMP WHERE id = " . $customerId);
    $_SESSION['login_logged'] = true;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_personal') {
        $f_name = trim($_POST['first_name'] ?? '');
        $l_name = trim($_POST['last_name'] ?? '');
        $ph = trim($_POST['phone'] ?? '');
        $usr_dob = trim($_POST['dob'] ?? '');
        $lang = trim($_POST['language'] ?? 'en');
        $curr = trim($_POST['currency'] ?? 'PHP');

        if (!empty($f_name) && !empty($l_name)) {
            $stmt = $conn->prepare("UPDATE customers SET first_name = ?, last_name = ?, phone = ?, dob = ?, language = ?, currency = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("ssssssi", $f_name, $l_name, $ph, $usr_dob, $lang, $curr, $customerId);
                if ($stmt->execute()) {
                    $_SESSION['customer_first_name'] = $f_name;
                    header("Location: profile.php?msg=personal_updated&tab=personal");
                    exit;
                }
                $stmt->close();
            }
        }
        header("Location: profile.php?msg=error&tab=personal");
        exit;
    }
    
    if ($action === 'update_preferences') {
        $size = trim($_POST['size_preference'] ?? '');
        $fav_cats = trim($_POST['favorite_categories'] ?? '');
        $news = isset($_POST['newsletter']) ? 1 : 0;
        
        $notifs_array = [];
        if (isset($_POST['notif_email'])) $notifs_array[] = 'email';
        if (isset($_POST['notif_sms'])) $notifs_array[] = 'sms';
        if (isset($_POST['notif_push'])) $notifs_array[] = 'push';
        $notifs = implode(',', $notifs_array);

        $stmt = $conn->prepare("UPDATE customers SET size_preference = ?, favorite_categories = ?, newsletter = ?, notification_preferences = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssisi", $size, $fav_cats, $news, $notifs, $customerId);
            if ($stmt->execute()) {
                header("Location: profile.php?msg=preferences_updated&tab=preferences");
                exit;
            }
            $stmt->close();
        }
        header("Location: profile.php?msg=error&tab=preferences");
        exit;
    }
    
    if ($action === 'change_password') {
        $curr_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        
        if (!empty($curr_pass) && !empty($new_pass)) {
            $stmt = $conn->prepare("SELECT password FROM customers WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $customerId);
                $stmt->execute();
                $stmt->bind_result($db_pass);
                $stmt->fetch();
                $stmt->close();
                
                if (password_verify($curr_pass, $db_pass)) {
                    $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE customers SET password = ? WHERE id = ?");
                    if ($stmt) {
                        $stmt->bind_param("si", $new_hash, $customerId);
                        if ($stmt->execute()) {
                            header("Location: profile.php?msg=password_updated&tab=security");
                            exit;
                        }
                        $stmt->close();
                    }
                } else {
                    header("Location: profile.php?msg=password_incorrect&tab=security");
                    exit;
                }
            }
        }
        header("Location: profile.php?msg=error&tab=security");
        exit;
    }

    if ($action === 'link_social') {
        $provider = trim($_POST['provider'] ?? '');
        $identifier = trim($_POST['identifier'] ?? '');

        $allowed = ['google', 'facebook'];
        if (in_array($provider, $allowed)) {
            $domain_valid = false;
            if (!empty($identifier) && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $domain = strtolower(explode('@', $identifier)[1] ?? '');
                if ($provider === 'google' && preg_match('/gmail\.com$/', $domain)) {
                    $domain_valid = true;
                } elseif ($provider === 'facebook' && preg_match('/facebook\.com$/', $domain)) {
                    $domain_valid = true;
                }
            }

            if (!$domain_valid) {
                header("Location: profile.php?msg=social_wrong_account&tab=security");
                exit;
            }

            $current = json_decode($user['linked_social'] ?? '{}', true);
            if (!is_array($current)) {
                $current = [];
            }
            $current[$provider] = $identifier;
            $json_str = json_encode($current);

            $stmt = $conn->prepare("UPDATE customers SET linked_social = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $json_str, $customerId);
                if ($stmt->execute()) {
                    header("Location: profile.php?msg=social_linked&tab=security");
                    exit;
                }
                $stmt->close();
            }
        }
        header("Location: profile.php?msg=error&tab=security");
        exit;
    }

    if ($action === 'unlink_social') {
        $provider = trim($_POST['provider'] ?? '');
        $allowed = ['google', 'facebook'];
        if (in_array($provider, $allowed)) {
            $current = json_decode($user['linked_social'] ?? '{}', true);
            if (!is_array($current)) {
                $current = [];
            }
            $current[$provider] = null;
            $json_str = json_encode($current);

            $stmt = $conn->prepare("UPDATE customers SET linked_social = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $json_str, $customerId);
                if ($stmt->execute()) {
                    header("Location: profile.php?msg=social_unlinked&tab=security");
                    exit;
                }
                $stmt->close();
            }
        }
        header("Location: profile.php?msg=error&tab=security");
        exit;
    }

    if ($action === 'add_address') {
        $addr_name = trim($_POST['address_name'] ?? 'Home');
        $addr_val = trim($_POST['address_value'] ?? '');
        $is_default = isset($_POST['is_default']) ? true : false;
        
        if (!empty($addr_val)) {
            $stmt = $conn->prepare("SELECT shipping_addresses FROM customers WHERE id = ?");
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $stmt->bind_result($curr_addr);
            $stmt->fetch();
            $stmt->close();

            $addresses = json_decode($curr_addr ?? '[]', true);
            if ($is_default) {
                foreach ($addresses as &$a) {
                    $a['default'] = false;
                }
            }
            $addresses[] = [
                'id' => time(),
                'name' => $addr_name,
                'address' => $addr_val,
                'default' => $is_default
            ];
            
            $json_str = json_encode($addresses);
            $stmt = $conn->prepare("UPDATE customers SET shipping_addresses = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $json_str, $customerId);
                if ($stmt->execute()) {
                    header("Location: profile.php?msg=address_added&tab=addresses");
                    exit;
                }
                $stmt->close();
            }
        }
        header("Location: profile.php?msg=error&tab=addresses");
        exit;
    }
    
    if ($action === 'upload_avatar') {
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar_file'];
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $file['size'] < 2 * 1024 * 1024) {
                $dest_dir = __DIR__ . '/../uploads';
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, 0777, true);
                }
                $filename = 'avatar_' . $customerId . '_' . time() . '.' . $ext;
                $dest_path = $dest_dir . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    $avatar_relative_path = '../uploads/' . $filename;
                    $stmt = $conn->prepare("UPDATE customers SET avatar = ? WHERE id = ?");
                    if ($stmt) {
                        $stmt->bind_param("si", $avatar_relative_path, $customerId);
                        $stmt->execute();
                        $stmt->close();
                        header("Location: profile.php?msg=avatar_updated&tab=personal");
                        exit;
                    }
                }
            }
        }
        header("Location: profile.php?msg=error&tab=personal");
        exit;
    }
    
    if ($action === 'add_payment') {
        $p_type = trim($_POST['payment_type'] ?? 'Credit Card');
        
        $brand_name = $p_type;
        $last4_digits = '';
        $expiry_val = '';
        
        if ($p_type === 'Credit Card' || $p_type === 'Bancnet') {
            $raw_number = trim($_POST['card_number'] ?? '');
            $expiry_val = trim($_POST['card_expiry'] ?? '');
            $cleaned = preg_replace('/\s+/', '', $raw_number);
            $last4_digits = substr($cleaned, -4);
        } else {
            $raw_identifier = trim($_POST['account_identifier'] ?? '');
            if (filter_var($raw_identifier, FILTER_VALIDATE_EMAIL)) {
                $parts = explode('@', $raw_identifier);
                $name = $parts[0];
                $domain = $parts[1] ?? '';
                $masked_name = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
                $last4_digits = $masked_name . '@' . $domain;
            } else {
                $cleaned = preg_replace('/[^0-9]/', '', $raw_identifier);
                if (strlen($cleaned) >= 4) {
                    $last4_digits = '•••• ' . substr($cleaned, -4);
                } else {
                    $last4_digits = $raw_identifier;
                }
            }
        }
        
        $stmt = $conn->prepare("SELECT payment_methods FROM customers WHERE id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $stmt->bind_result($curr_pay);
        $stmt->fetch();
        $stmt->close();
        
        $payment_methods = json_decode($curr_pay ?? '[]', true);
        $is_default = empty($payment_methods);
        
        $payment_methods[] = [
            'id' => time(),
            'brand' => $brand_name,
            'last4' => $last4_digits,
            'exp' => !empty($expiry_val) ? $expiry_val : 'N/A',
            'default' => $is_default
        ];
        
        $json_str = json_encode($payment_methods);
        $stmt = $conn->prepare("UPDATE customers SET payment_methods = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $json_str, $customerId);
            if ($stmt->execute()) {
                header("Location: profile.php?msg=payment_added&tab=payment-billing");
                exit;
            }
            $stmt->close();
        }
        header("Location: profile.php?msg=error&tab=payment-billing");
        exit;
    }
}

if (isset($_GET['del_addr'])) {
    $del_id = intval($_GET['del_addr']);
    
    $stmt = $conn->prepare("SELECT shipping_addresses FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->bind_result($curr_addr);
    $stmt->fetch();
    $stmt->close();

    $addresses = json_decode($curr_addr ?? '[]', true);
    $filtered = [];
    foreach ($addresses as $a) {
        if (intval($a['id']) !== $del_id) {
            $filtered[] = $a;
        }
    }
    
    if (!empty($filtered)) {
        $has_default = false;
        foreach ($filtered as $f) {
            if ($f['default']) $has_default = true;
        }
        if (!$has_default) {
            $filtered[0]['default'] = true;
        }
    }
    
    $json_str = json_encode($filtered);
    $stmt = $conn->prepare("UPDATE customers SET shipping_addresses = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $json_str, $customerId);
        if ($stmt->execute()) {
            header("Location: profile.php?msg=address_deleted&tab=addresses");
            exit;
        }
        $stmt->close();
    }
}

if (isset($_GET['del_payment'])) {
    $del_pay_id = intval($_GET['del_payment']);
    
    $stmt = $conn->prepare("SELECT payment_methods FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->bind_result($curr_pay);
    $stmt->fetch();
    $stmt->close();

    $payment_methods = json_decode($curr_pay ?? '[]', true);
    $filtered_pay = [];
    foreach ($payment_methods as $pm) {
        if (intval($pm['id']) !== $del_pay_id) {
            $filtered_pay[] = $pm;
        }
    }
    
    if (!empty($filtered_pay)) {
        $has_def = false;
        foreach ($filtered_pay as $f) {
            if ($f['default']) $has_def = true;
        }
        if (!$has_def) {
            $filtered_pay[0]['default'] = true;
        }
    }
    
    $json_str = json_encode($filtered_pay);
    $stmt = $conn->prepare("UPDATE customers SET payment_methods = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $json_str, $customerId);
        if ($stmt->execute()) {
            header("Location: profile.php?msg=payment_deleted&tab=payment-billing");
            exit;
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, created_at, last_login, dob, avatar, status, language, currency, newsletter, loyalty_points, size_preference, favorite_categories, notification_preferences, two_factor, login_history, shipping_addresses, billing_addresses, payment_methods, wishlist, ticket_history, message_history, linked_social FROM customers WHERE id = ?");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$first_name = $user['first_name'] ?? '';
$last_name = $user['last_name'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$created_at = $user['created_at'] ?? '';
$dob = $user['dob'] ?? '';
$avatar = $user['avatar'] ?? '';
$status = $user['status'] ?? 'Active';
$language = $user['language'] ?? 'en';
$currency = $user['currency'] ?? 'PHP';
$newsletter = (int)($user['newsletter'] ?? 1);
$loyalty_points = (int)($user['loyalty_points'] ?? 150);
$size_preference = $user['size_preference'] ?? '';
$favorite_categories = $user['favorite_categories'] ?? '';
$notification_preferences = explode(',', $user['notification_preferences'] ?? 'email,sms');
$two_factor = (int)($user['two_factor'] ?? 0);

if (!empty($avatar)) {
    $_SESSION['customer_avatar'] = $avatar;
}

$login_history = json_decode($user['login_history'] ?? '[]', true);
if (empty($login_history)) {
    $login_history = [
        ['time' => date('Y-m-d H:i:s'), 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'device' => 'Chrome (Windows 11)']
    ];
    $conn->query("UPDATE customers SET login_history = '" . $conn->real_escape_string(json_encode($login_history)) . "' WHERE id = " . $customerId);
}

$shipping_addresses = json_decode($user['shipping_addresses'] ?? '[]', true);
if (empty($shipping_addresses)) {
    $shipping_addresses = [
        ['id' => 1, 'name' => 'Pasig Address', 'address' => 'Robinson Metro East, Pasig, Metro Manila, 1600', 'default' => true]
    ];
    $conn->query("UPDATE customers SET shipping_addresses = '" . $conn->real_escape_string(json_encode($shipping_addresses)) . "' WHERE id = " . $customerId);
}

$billing_addresses = json_decode($user['billing_addresses'] ?? '[]', true);
if (empty($billing_addresses)) {
    $billing_addresses = [
        ['id' => 1, 'name' => 'Billing Address', 'address' => 'Robinson Metro East, Pasig, Metro Manila, 1600', 'default' => true]
    ];
    $conn->query("UPDATE customers SET billing_addresses = '" . $conn->real_escape_string(json_encode($billing_addresses)) . "' WHERE id = " . $customerId);
}

$payment_methods = json_decode($user['payment_methods'] ?? '[]', true);
if (empty($payment_methods)) {
    $payment_methods = [
        ['id' => 1, 'brand' => 'Visa', 'last4' => '4322', 'exp' => '12/28', 'default' => true]
    ];
    $conn->query("UPDATE customers SET payment_methods = '" . $conn->real_escape_string(json_encode($payment_methods)) . "' WHERE id = " . $customerId);
}

$wishlist_items = !empty($user['wishlist']) ? explode(',', $user['wishlist']) : ['Kata Gi Canvas Red and Blue', 'Kumite Gi Super Light Air Cool Red and Blue'];
$ticket_history = json_decode($user['ticket_history'] ?? '[]', true);
if (empty($ticket_history)) {
    $ticket_history = [
        ['id' => 'TCK-20485', 'subject' => 'Dojo class schedule inquiry', 'status' => 'Resolved', 'date' => date('Y-m-d', strtotime('-5 days'))]
    ];
    $conn->query("UPDATE customers SET ticket_history = '" . $conn->real_escape_string(json_encode($ticket_history)) . "' WHERE id = " . $customerId);
}

$message_history = json_decode($user['message_history'] ?? '[]', true);
if (empty($message_history)) {
    $message_history = [
        ['sender' => 'support', 'msg' => 'Welcome to JKS Pasig PKTS Karate Dojo! What is your query?', 'time' => date('H:i', strtotime('-5 days'))],
        ['sender' => 'customer', 'msg' => 'I wanted to ask if uniform size 150cm fits a 10 year old.', 'time' => date('H:i', strtotime('-5 days'))],
        ['sender' => 'support', 'msg' => 'Yes, 150cm is designed for kids roughly 9-11 years old. You can purchase directly.', 'time' => date('H:i', strtotime('-5 days'))]
    ];
    $conn->query("UPDATE customers SET message_history = '" . $conn->real_escape_string(json_encode($message_history)) . "' WHERE id = " . $customerId);
}

$linked_social = json_decode($user['linked_social'] ?? '{"google":"google_account@gmail.com","facebook":null}', true);

$orders_stmt = $conn->prepare("SELECT id, order_number, total_amount, payment_method, payment_status, status, created_at FROM orders WHERE customer_email = ? ORDER BY id DESC");
$orders_stmt->bind_param("s", $email);
$orders_stmt->execute();
$orders_res = $orders_stmt->get_result();
$orders_list = [];
while ($row = $orders_res->fetch_assoc()) {
    $orders_list[] = $row;
}
$orders_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css">
  <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>
  <div class="bg-dashboard">
    <div class="profile-container">
      <aside class="profile-sidebar">
        <div class="profile-avatar-container">
          <?php if (!empty($avatar)): ?>
            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="profile-avatar-img" id="profile-avatar-tag">
          <?php else: ?>
            <div class="profile-avatar-initials"><?php echo htmlspecialchars(strtoupper($first_name[0] ?? '' . $last_name[0] ?? 'U')); ?></div>
          <?php endif; ?>
          <form action="profile.php" method="POST" enctype="multipart/form-data" id="avatarForm">
            <input type="hidden" name="action" value="upload_avatar">
            <input type="file" name="avatar_file" id="avatarInput" style="display: none;" onchange="document.getElementById('avatarForm').submit();">
            <button type="button" class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click();">
              <i class="fas fa-camera"></i>
            </button>
          </form>
        </div>
        <h3 class="profile-user-name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
        <p class="profile-user-meta">Member since: <?php echo date('F Y', strtotime($created_at)); ?></p>
        <nav class="profile-menu">
          <button class="profile-menu-item active" data-target="personal-info">
            <i class="fas fa-user"></i> Personal Info
          </button>
          <button class="profile-menu-item" data-target="account-details">
            <i class="fas fa-id-card"></i> Account Details
          </button>
          <button class="profile-menu-item" data-target="addresses">
            <i class="fas fa-map-marker-alt"></i> Address Book
          </button>
          <button class="profile-menu-item" data-target="payment-billing">
            <i class="fas fa-credit-card"></i> Payment & Credits
          </button>
          <button class="profile-menu-item" data-target="orders-tab">
            <i class="fas fa-shopping-bag"></i> Order History
          </button>
          <button class="profile-menu-item" data-target="preferences">
            <i class="fas fa-sliders-h"></i> Preferences
          </button>
          <button class="profile-menu-item" data-target="security">
            <i class="fas fa-shield-alt"></i> Security & Access
          </button>
          <button class="profile-menu-item" data-target="support">
            <i class="fas fa-headset"></i> Support & Chat
          </button>
        </nav>
      </aside>
      <main class="profile-content">
        <section id="personal-info" class="profile-section active">
          <h2 class="profile-section-title">Personal <span>Information</span></h2>
          <p class="profile-section-subtitle">Manage your credentials, birthday and identity info.</p>
          <form action="profile.php" method="POST">
            <input type="hidden" name="action" value="update_personal">
            <div class="form-grid">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
              </div>
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
              </div>
              <div class="form-group">
                <label for="email">Email (Primary ID)</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
              </div>
              <div class="form-group">
                <label for="dob">Date of Birth (For Age & Promos)</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
              </div>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="user_<?php echo htmlspecialchars($customerId); ?>" disabled>
              </div>
            </div>
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
          </form>
        </section>
      </main>
    </div>
  </div>
  <?php require_once __DIR__ . '/layout/footer.php'; ?>
  <script src="../js/hp.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const menuItems = document.querySelectorAll(".profile-menu-item");
      const sections = document.querySelectorAll(".profile-section");
      const urlParams = new URLSearchParams(window.location.search);
      const activeTab = urlParams.get('tab');
      if (activeTab) {
        let matched = false;
        menuItems.forEach((btn) => {
          if (btn.getAttribute("data-target") === activeTab || btn.getAttribute("data-target") === activeTab + "-info" || btn.getAttribute("data-target") === activeTab + "-tab") {
            menuItems.forEach(b => b.classList.remove("active"));
            sections.forEach(s => s.classList.remove("active"));
            btn.classList.add("active");
            const targetId = btn.getAttribute("data-target");
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
              targetSection.classList.add("active");
              matched = true;
            }
          }
        });
      }
      menuItems.forEach((btn) => {
        btn.addEventListener("click", () => {
          const target = btn.getAttribute("data-target");
          menuItems.forEach((b) => b.classList.remove("active"));
          sections.forEach((s) => s.classList.remove("active"));
          btn.classList.add("active");
          const targetSection = document.getElementById(target);
          if (targetSection) {
            targetSection.classList.add("active");
          }
          const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + target;
          window.history.pushState({ path: newUrl }, '', newUrl);
        });
      });
    });
  </script>
</body>
</html>