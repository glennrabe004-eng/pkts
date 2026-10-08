<?php
require_once __DIR__ . '/db.php';
$notice = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $notice = ['type' => 'error', 'text' => 'Please enter your email address.'];
    } else {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        if ($exists) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $upd = $conn->prepare("UPDATE customers SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $upd->bind_param("sss", $token, $expires, $email);
            $upd->execute();
            $upd->close();

            $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/reset-password.php?token=' . $token . '&email=' . urlencode($email);
            $subject = 'PKTS Karate - Password Reset';
            $message = "Hello,\n\nWe received a request to reset your PKTS Karate password.\nClick the link below (valid for 1 hour):\n\n" . $resetLink . "\n\nIf you didn't request this, you can ignore this email.";
            $headers = "From: no-reply@pktskarate.com\r\nContent-Type: text/plain; charset=UTF-8\r\n";
            @mail($email, $subject, $message, $headers);
        }
        // Always show the generic message (no account enumeration)
        $notice = ['type' => 'success', 'text' => 'If an account exists for ' . htmlspecialchars($email) . ', a password reset link has been sent.'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PKTS Karate</title>
    <link rel="icon" href="../logos/PKTS LOGO.png">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --ink: #0a0a0a;
            --red: #e02020;
            --red2: #b71c1c;
            --off: #f5f0eb;
            --steel: #8a8a8a;
            --mid: #1c1c1c;
            --border: #2a2a2a;
            --white: #ffffff;

            --font-display: 'Anton', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--ink);
            color: var(--white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('../background/BG1.png') center/cover no-repeat;
            opacity: 0.35;
            z-index: -1;
            pointer-events: none;
        }

        .bg-decor {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 40%;
            height: 60%;
            background: linear-gradient(135deg, var(--red) 0%, var(--red2) 100%);
            clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
            opacity: 0.15;
            z-index: 1;
            pointer-events: none;
        }

        .bg-decor-left {
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 30%;
            height: 50%;
            background: linear-gradient(135deg, var(--red) 0%, var(--red2) 100%);
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0% 100%);
            opacity: 0.05;
            z-index: 1;
            pointer-events: none;
        }

        .auth-card {
            background-color: rgba(20, 20, 20, 0.95);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 430px;
            z-index: 2;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--red) 0%, var(--red2) 100%);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-section img {
            height: 64px;
            margin-bottom: 10px;
        }

        .logo-section h1 {
            font-family: var(--font-display);
            font-size: 2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1;
        }

        .logo-section h1 span {
            color: var(--red);
        }

        .logo-section p {
            font-size: 0.8rem;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .auth-card h2 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 8px;
        }

        .auth-card .subtext {
            text-align: center;
            color: var(--steel);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 26px;
        }

        .notice-alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }

        .notice-alert.success {
            background-color: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.35);
            color: #2ecc71;
        }

        .notice-alert.error {
            background-color: rgba(224, 32, 32, 0.1);
            border: 1px solid rgba(224, 32, 32, 0.3);
            color: #ff5252;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.left-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--steel);
            font-size: 1rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            background-color: var(--ink);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 14px 14px 40px;
            color: var(--white);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(224, 32, 32, 0.15);
        }

        .form-control:focus ~ i.left-icon {
            color: var(--red);
        }

        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background-color: var(--red);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 16px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.15s;
            margin-top: 6px;
        }

        .btn-submit:hover {
            background-color: var(--red2);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .back-link {
            display: block;
            text-align: center;
            color: var(--red);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 22px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--white);
        }
    </style>
</head>
<body>

    <div class="bg-decor"></div>
    <div class="bg-decor-left"></div>

    <div class="auth-card">
        <div class="logo-section">
            <img src="../logos/PKTS LOGO.png" alt="PKTS Dojo Logo">
            <h1>PKTS <span>Karate</span></h1>
            <p>Customer Portal</p>
        </div>

        <h2>Reset Password</h2>
        <p class="subtext">Enter your account email and we'll send you a reset link.</p>

        <?php if ($notice): ?>
            <div class="notice-alert <?php echo $notice['type']; ?>">
                <i class="fas <?php echo $notice['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo $notice['text']; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="forgot-password.php" autocomplete="off">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="form-control" placeholder="enter your email" required autofocus value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <i class="fas fa-envelope left-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>

        <a href="login.php" class="back-link">← Back to login</a>
    </div>

</body>
</html>
