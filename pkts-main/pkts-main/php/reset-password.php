<?php
require_once __DIR__ . '/db.php';
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
$valid = false;
if ($email && $token) {
    $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? AND reset_token = ? AND reset_expires > NOW() LIMIT 1");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $valid = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}
$notice = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $valid) {
    $pw = $_POST['password'] ?? '';
    $pw2 = $_POST['confirm_password'] ?? '';
    if (!preg_match('/^[A-Z]/', $pw)) {
        $notice = ['type' => 'error', 'text' => 'Password must start with a capital letter.'];
    } elseif (strlen($pw) < 8) {
        $notice = ['type' => 'error', 'text' => 'Password must be at least 8 characters long.'];
    } elseif (!preg_match('/[^a-zA-Z0-9]/', $pw)) {
        $notice = ['type' => 'error', 'text' => 'Password must contain at least one special character (e.g. !@#$%^&*).'];
    } elseif ($pw !== $pw2) {
        $notice = ['type' => 'error', 'text' => 'Passwords do not match.'];
    } else {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE customers SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
        $upd->bind_param("ss", $hash, $email);
        $upd->execute();
        $upd->close();
        header("Location: login.php?reset=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - PKTS Karate</title>
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

        .error-alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
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

        <h2>Set New Password</h2>

        <?php if (!$valid): ?>
            <p class="subtext">Reset your PKTS Karate account password.</p>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>This reset link is invalid or has expired.</span>
            </div>
            <a href="login.php" class="back-link">← Back to login</a>
        <?php else: ?>
            <p class="subtext">Choose a new password for your account.</p>

            <?php if ($notice): ?>
                <div class="notice-alert <?php echo $notice['type']; ?>">
                    <i class="fas <?php echo $notice['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <span><?php echo $notice['text']; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>&email=<?php echo htmlspecialchars($email); ?>" autocomplete="off">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autofocus>
                        <i class="fas fa-lock left-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock left-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-key"></i> Set New Password
                </button>
            </form>

            <a href="login.php" class="back-link">← Back to login</a>
        <?php endif; ?>
    </div>

</body>
</html>
