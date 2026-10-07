<?php
require_once __DIR__ . '/config.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(150) NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        avatar VARCHAR(255) NULL,
        status VARCHAR(50) DEFAULT 'Active',
        last_login DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS trial_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guardian_first_name VARCHAR(100) NOT NULL,
        guardian_last_name VARCHAR(100) NOT NULL,
        student_first_name VARCHAR(100) NOT NULL,
        student_last_name VARCHAR(100) NOT NULL,
        student_age INT NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        street VARCHAR(255) NOT NULL,
        city VARCHAR(100) NOT NULL,
        province VARCHAR(100) NOT NULL,
        zip VARCHAR(10) NOT NULL,
        trial_date DATE NOT NULL,
        time_slot VARCHAR(150) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        secondary_image VARCHAR(255) NULL,
        description TEXT NULL,
        sizes VARCHAR(255) DEFAULT '140 CM, 150 CM, 160 CM, 170 CM, 180 CM',
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(100) NOT NULL UNIQUE,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(150) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_address TEXT NOT NULL,
        customer_notes TEXT,
        payment_method VARCHAR(100) NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        payment_status VARCHAR(50) DEFAULT 'unpaid',
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        size VARCHAR(50) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NULL,
        subject VARCHAR(255) NULL,
        message TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guardian_first_name VARCHAR(100) NOT NULL,
        guardian_last_name VARCHAR(100) NOT NULL,
        student_first_name VARCHAR(100) NOT NULL,
        student_last_name VARCHAR(100) NOT NULL,
        student_age INT NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        street VARCHAR(255) NOT NULL,
        city VARCHAR(100) NOT NULL,
        province VARCHAR(100) NOT NULL,
        zip VARCHAR(10) NOT NULL,
        branch VARCHAR(100) NOT NULL DEFAULT 'Robinson Metro East',
        medical_certificate VARCHAR(255) NULL,
        pre_assessment_notes TEXT NULL,
        liability_waiver_accepted TINYINT(1) DEFAULT 1,
        trial_date DATE NOT NULL,
        time_slot VARCHAR(150) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        payment_status VARCHAR(50) DEFAULT 'unpaid',
        payment_method VARCHAR(50) DEFAULT 'Cash',
        fee_amount DECIMAL(10,2) DEFAULT 1500.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS student_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_name VARCHAR(255) NOT NULL,
        branch VARCHAR(100) NOT NULL,
        current_belt VARCHAR(50) NOT NULL DEFAULT 'White Belt',
        target_belt VARCHAR(50) NOT NULL DEFAULT 'Yellow Belt',
        video_url VARCHAR(255) NULL,
        sensei_evaluation TEXT NULL,
        readiness_status VARCHAR(50) DEFAULT 'In Progress',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_name VARCHAR(255) NOT NULL,
        branch VARCHAR(100) NOT NULL,
        belt_rank VARCHAR(50) NOT NULL,
        certificate_code VARCHAR(100) NOT NULL UNIQUE,
        issue_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        target_branch VARCHAR(100) DEFAULT 'All',
        posted_by VARCHAR(100) DEFAULT 'Sensei Admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS belt_references (
        id INT AUTO_INCREMENT PRIMARY KEY,
        belt_name VARCHAR(50) NOT NULL UNIQUE,
        kata_required VARCHAR(255) NOT NULL,
        kumite_required VARCHAR(255) NOT NULL,
        minimum_training_months INT DEFAULT 3,
        description TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

echo "Creating tables...\n";
foreach ($tables as $sql) {
    if ($conn->query($sql)) {
        echo "✓ Table created\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$admin_user = 'admin';
$admin_email = 'admin@pktskarate.com';
$admin_role = 'Super Admin';
$admin_status = 'Active';
$admin_stmt = $conn->prepare("INSERT IGNORE INTO admin_users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
$admin_stmt->bind_param("sssss", $admin_user, $admin_pass, $admin_email, $admin_role, $admin_status);
$admin_stmt->execute();
$admin_stmt->close();

$manager_pass = password_hash('manager123', PASSWORD_DEFAULT);
$mgr_user = 'dojo_manager';
$mgr_email = 'manager@pktskarate.com';
$mgr_role = 'Dojo Manager';
$mgr_status = 'Active';
$manager_stmt = $conn->prepare("INSERT IGNORE INTO admin_users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
$manager_stmt->bind_param("sssss", $mgr_user, $manager_pass, $mgr_email, $mgr_role, $mgr_status);
$manager_stmt->execute();
$manager_stmt->close();

$products = [
    ['Kata Gi Canvas Red and Blue', 'Uniform', 6500.00, 'PROD/Uniform1.jpeg', 'High performance heavy weight canvas kata gi with red/blue shoulder embroidery.', '140 CM, 150 CM, 160 CM, 170 CM, 180 CM, 185 CM, 190 CM'],
    ['Kumite Gi Super Light Air Cool Red and Blue', 'Uniform', 5000.00, 'PROD/Uniform2.jpeg', 'Ultra lightweight air-cool breathable kumite uniform for fast tournament sparring.', 'S, M, L, XL, XXL'],
    ['Shureido Competition Kata Gi', 'Uniform', 7200.00, 'PROD/Uniform3.jpeg', 'Master grade Japanese cut karate uniform engineered for crisp snap and durability.', '150 CM, 160 CM, 170 CM, 180 CM'],
    ['WKF Approved Shin Guard & Instep', 'Equipment', 2800.00, 'PROD/Eqp1.jpg', 'Ergonomic high-density foam padding for maximum shin and foot protection.', 'S, M, L, XL'],
    ['Red and Blue Karate Sparring Mitts', 'Equipment', 2200.00, 'PROD/Eqp2.jpg', 'Official WKF style sparring gloves with secure wrist wrap support.', 'S, M, L'],
    ['Head Guard Protector with Face Shield', 'Equipment', 3500.00, 'PROD/Eqp3.jpeg', 'Shock absorbing head protection with optional clear face guard for youth/adults.', 'M, L'],
    ['PKTS Official Dojo Duffle Bag', 'Bags', 2400.00, 'PROD/Bag1.jpg', 'Spacious water-resistant gear bag with dedicated gi compartment and ventilation.', 'Standard'],
    ['PKTS Pro Karate Equipment Backpack', 'Bags', 2900.00, 'PROD/Bag2.jpg', 'Multi-zipper heavy-duty gear backpack with external belt holder straps.', 'Standard']
];

echo "Seeding products...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO products (name, category, price, image, description, sizes) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($products as $p) {
    $stmt->bind_param("ssssss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
    $stmt->execute();
}
$stmt->close();

$messages = [
    ['Juan Dela Cruz', 'juan@gmail.com', '+63 917 555 1234', 'Trial Class Schedule Inquiry', 'Good day Sensei, I would like to ask if you have weekend classes available for my 8 year old son?', 'unread'],
    ['Maria Santos', 'maria.santos@yahoo.com', '+63 918 444 9876', 'Adult Beginners Karate', 'Hello! Do you offer beginner classes for adults with no prior martial arts background?', 'read'],
    ['Carlos Reyes', 'carlos.reyes@hotmail.com', '+63 920 333 5555', 'Equipment Size Inquiry', 'Hi, is the Kata Gi Canvas uniform available in 170 CM size at the Pasig dojo branch?', 'replied']
];

echo "Seeding messages...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($messages as $m) {
    $stmt->bind_param("ssssss", $m[0], $m[1], $m[2], $m[3], $m[4], $m[5]);
    $stmt->execute();
}
$stmt->close();

$settings = [
    ['site_title', 'PKTS Karate Dojo - Gakusei Progress System'],
    ['dojo_address', 'Robinson Metro East, Pasig, Metro Manila, 1600'],
    ['contact_phone', '+63 917 123 4567'],
    ['contact_email', 'info@pktskarate.com'],
    ['hero_headline', 'TRAIN WITH PURPOSE. MASTER THE ART.'],
    ['hero_subtext', 'Authentic Japan Karate Shoto-Federation (JKS) Martial Arts Training.'],
    ['announcement_banner', '🥋 Free Trial Class & Health Pre-Assessment Available across Metro East, Vista Mall Antipolo, and Marikina Falcon branches!']
];

echo "Seeding settings...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
foreach ($settings as $s) {
    $stmt->bind_param("ss", $s[0], $s[1]);
    $stmt->execute();
}
$stmt->close();

$belts = [
    ['White Belt (10th Kyu)', 'Taikyoku Shodan', 'Gohon Kumite (Basic 5-step)', 2, 'Entry level belt. Focus on basic stances (Zenkutsu-dachi), punches (Chudan Tsuki), and blocks (Gedan Barai).'],
    ['Yellow Belt (9th Kyu)', 'Heian Shodan', 'Gohon Kumite (Age-uke, Soto-uke)', 3, 'Second level. Mastery of Heian Shodan kata and basic stepping blocks.'],
    ['Orange Belt (8th Kyu)', 'Heian Nidan', 'Kihon Ippon Kumite', 4, 'Third level. Focus on body shifting, knife-hand block (Shuto-uke), and front kick (Mae-geri).'],
    ['Green Belt (7th Kyu)', 'Heian Sandan', 'Kihon Ippon Kumite (Advanced)', 5, 'Fourth level. Introduction of elbow strikes (Empi-uchi) and back stance (Kokutsu-dachi).'],
    ['Blue Belt (6th Kyu)', 'Heian Yondan', 'Jiyu Ippon Kumite', 6, 'Fifth level. Dynamic kicks including side snap kick (Yoko-geri Keage) and back kick.'],
    ['Purple Belt (5th Kyu)', 'Heian Godan', 'Jiyu Ippon Kumite (Semi-free)', 6, 'Sixth level. Intermediate level required for competitive sparring entry.'],
    ['Brown Belt (3rd-1st Kyu)', 'Tekki Shodan & Bassai Dai', 'Jiyu Kumite (Free Sparring)', 12, 'Advanced level before Dan rank. High mental focus and speed required.'],
    ['Black Belt (1st Dan)', 'Kanku Dai & Jion', 'WKF Competition Sparring', 24, 'Mastery level. Certified by Japan Karate Shoto-Federation (JKS).']
];

echo "Seeding belt references...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO belt_references (belt_name, kata_required, kumite_required, minimum_training_months, description) VALUES (?, ?, ?, ?, ?)");
foreach ($belts as $b) {
    $stmt->bind_param("sssis", $b[0], $b[1], $b[2], $b[3], $b[4]);
    $stmt->execute();
}
$stmt->close();

$sample_enrollments = [
    ['Juan', 'Dela Cruz', 'Marco', 'Dela Cruz', 8, 'marco.parents@gmail.com', '09175551234', 'Marikina Heights', 'Marikina', 'Metro Manila', '1800', 'Marikina Falcon', NULL, 'Cleared by pediatrician for physical activity.', 1, '2026-10-15', '10:00 AM - 11:30 AM', 'approved', 'paid', 'Cash', 1500.00],
    ['Maria', 'Santos', 'Sophia', 'Santos', 10, 'sophia.santos@yahoo.com', '09184449876', 'Antipolo Hills', 'Antipolo', 'Rizal', '1870', 'Vista Mall Antipolo', NULL, 'Fit to train, no allergies.', 1, '2026-10-16', '02:00 PM - 03:30 PM', 'approved', 'paid', 'GCash', 1500.00],
    ['Robert', 'Reyes', 'Kenji', 'Reyes', 12, 'kenji.reyes@gmail.com', '09203335555', 'Felix Ave', 'Pasig', 'Metro Manila', '1600', 'Robinson Metro East', NULL, 'Pre-assessment completed. Good stamina.', 1, '2026-10-18', '04:00 PM - 05:30 PM', 'pending', 'unpaid', 'Cash', 1500.00]
];

echo "Seeding enrollments...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO enrollments (guardian_first_name, guardian_last_name, student_first_name, student_last_name, student_age, email, phone, street, city, province, zip, branch, medical_certificate, pre_assessment_notes, liability_waiver_accepted, trial_date, time_slot, status, payment_status, payment_method, fee_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($sample_enrollments as $e) {
    $stmt->bind_param("ssssisssssssssisssssd", $e[0], $e[1], $e[2], $e[3], $e[4], $e[5], $e[6], $e[7], $e[8], $e[9], $e[10], $e[11], $e[12], $e[13], $e[14], $e[15], $e[16], $e[17], $e[18], $e[19], $e[20]);
    $stmt->execute();
}
$stmt->close();

$sample_announcements = [
    ['16th Korea Open Championship Winners & Recognition', 'Congratulations to all PKTS karateka who competed at the 16th Korea Open International Karate-Do Championships in Busan! Certificate presentation this Saturday across all branches.', 'All', 'Sensei Admin'],
    ['Vista Mall Antipolo Schedule Adjustment', 'Please be advised that Sunday morning training at Vista Mall Antipolo will start at 9:30 AM instead of 9:00 AM.', 'Vista Mall Antipolo', 'Sensei Admin'],
    ['Upcoming Belt Ranking Assessment', 'Self-assessment video submissions for the upcoming Belt Promotion exam are now open. Please upload your video link in the Gakusei Student Portal.', 'All', 'Sensei Admin']
];

echo "Seeding announcements...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO announcements (title, content, target_branch, posted_by) VALUES (?, ?, ?, ?)");
foreach ($sample_announcements as $a) {
    $stmt->bind_param("ssss", $a[0], $a[1], $a[2], $a[3]);
    $stmt->execute();
}
$stmt->close();

echo "\n=== Database Setup Complete ===\n";
echo "Admin login: admin / admin123\n";
echo "Manager login: dojo_manager / manager123\n";