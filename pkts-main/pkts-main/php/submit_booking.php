<?php
require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $guardian_first = trim($_POST['guardian_first'] ?? '');
    $guardian_last  = trim($_POST['guardian_last'] ?? '');
    $student_first  = trim($_POST['student_first'] ?? '');
    $student_last   = trim($_POST['student_last'] ?? '');
    $student_age    = intval($_POST['student_age'] ?? 0);
    $email          = trim($_POST['email'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $street         = trim($_POST['street'] ?? '');
    $city           = trim($_POST['city'] ?? '');
    $province       = trim($_POST['province'] ?? '');
    $zip            = trim($_POST['zip'] ?? '');
    $branch         = trim($_POST['branch'] ?? 'Robinson Metro East');
    $pre_assessment = trim($_POST['pre_assessment_notes'] ?? '');
    $waiver         = isset($_POST['liability_waiver']) ? 1 : 0;
    $trial_date     = trim($_POST['trial_date'] ?? '');
    $time_slot      = trim($_POST['time_slot'] ?? '');

    if (empty($guardian_first) || empty($guardian_last) || empty($student_first) || empty($student_last) || !$student_age || empty($email) || empty($phone) || empty($trial_date) || empty($time_slot)) {
        http_response_code(400);
        echo "<p class='error'>Error: Please complete all required fields.</p>";
        exit;
    }

    // Handle Medical Certificate Upload
    $med_cert_path = null;
    if (isset($_FILES['medical_certificate']) && $_FILES['medical_certificate']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/med_certs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['medical_certificate']['name'], PATHINFO_EXTENSION);
        $filename = 'med_cert_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['medical_certificate']['tmp_name'], $target_file)) {
            $med_cert_path = 'uploads/med_certs/' . $filename;
        }
    }

    // Insert into enrollments
    $stmt = $conn->prepare("INSERT INTO enrollments (guardian_first_name, guardian_last_name, student_first_name, student_last_name, student_age, email, phone, street, city, province, zip, branch, medical_certificate, pre_assessment_notes, liability_waiver_accepted, trial_date, time_slot, status, payment_status, payment_method, fee_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', 'Cash', 1500.00)");
    
    if ($stmt) {
        $stmt->bind_param("ssssisssssssssissss", 
            $guardian_first, $guardian_last, $student_first, $student_last, $student_age, 
            $email, $phone, $street, $city, $province, $zip, $branch, 
            $med_cert_path, $pre_assessment, $waiver, $trial_date, $time_slot
        );
        if ($stmt->execute()) {
            $stmt->close();

            // Also keep trial_bookings updated
            $tb_stmt = $conn->prepare("INSERT INTO trial_bookings (guardian_first_name, guardian_last_name, student_first_name, student_last_name, student_age, email, phone, street, city, province, zip, trial_date, time_slot, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            if ($tb_stmt) {
                $tb_stmt->bind_param("ssssissssssss", $guardian_first, $guardian_last, $student_first, $student_last, $student_age, $email, $phone, $street, $city, $province, $zip, $trial_date, $time_slot);
                $tb_stmt->execute();
                $tb_stmt->close();
            }

            echo "<p class='success'>Enrollment & trial booking submitted successfully! Our Sensei team will review your health pre-assessment and confirm your slot.</p>";
            @mail($email, 'PKTS Gakusei Enrollment Confirmation', "Dear {$guardian_first},\n\nYour enrollment application for {$student_first} at {$branch} is received!\nTrial Date: {$trial_date}\nTime: {$time_slot}\n\nThank you for choosing PKTS Karate Dojo!", "From: PKTS Karate <no-reply@pktskarate.com>");
        } else {
            http_response_code(500);
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }
    } else {
        http_response_code(500);
        echo "<p class='error'>Prepare error: " . $conn->error . "</p>";
    }
} else {
    header("Location: register.php");
    exit;
}
?>
