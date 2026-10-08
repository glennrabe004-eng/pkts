<?php
require_once __DIR__ . '/db.php';

$error_msg = '';

$get_notice = null;
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    $get_notice = ['type' => 'success', 'text' => 'Your password has been reset. Please log in with your new password.'];
} elseif (isset($_GET['oauth_error']) && $_GET['oauth_error'] === '1') {
    $error_msg = 'Social login failed. Please try again or use your email and password.';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        $account_id = intval($_POST['account_id'] ?? 0);
        if ($account_id > 0) {
            $del_stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
            if ($del_stmt) {
                $del_stmt->bind_param("i", $account_id);
                if ($del_stmt->execute()) {
                    $get_notice = ['type' => 'success', 'text' => 'Registered account deleted successfully from database.'];
                } else {
                    $error_msg = 'Failed to delete account. Please try again.';
                }
                $del_stmt->close();
            }
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error_msg = 'Please cover both fields.';
        } else {
            $stmt = $conn->prepare("SELECT id, first_name, last_name, password, avatar FROM customers WHERE email = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($user = $result->fetch_assoc()) {
                    if (password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['customer_logged_in'] = true;
                        $_SESSION['customer_id'] = $user['id'];
                        $_SESSION['customer_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['customer_first_name'] = $user['first_name'];
                        $_SESSION['customer_email'] = $email;
                        if (!empty($user['avatar'])) {
                            $_SESSION['customer_avatar'] = $user['avatar'];
                        }

                        $update_stmt = $conn->prepare("UPDATE customers SET last_login = NOW() WHERE id = ?");
                        if ($update_stmt) {
                            $update_stmt->bind_param("i", $user['id']);
                            $update_stmt->execute();
                            $update_stmt->close();
                        }

                        header("Location: Homepage.php");
                        exit;
                    } else {
                        $error_msg = 'Incorrect password. Please try again.';
                    }
                } else {
                    $error_msg = 'This email is not registered.';
                }
                $stmt->close();
            } else {
                $error_msg = 'An error occurred. Please try again later.';
            }
        }
    }
}

$registered_accounts = [];
$res_acc = $conn->query("SELECT id, first_name, last_name, email FROM customers ORDER BY id DESC LIMIT 10");
if ($res_acc) {
    while ($row = $res_acc->fetch_assoc()) {
        $registered_accounts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - PKTS Karate</title>
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
            background: linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35)), url('../background/BG1.png') center/cover no-repeat;
            background-color: #0a0a0a;
            background-attachment: fixed;
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
            display: none;
        }

        /* Ambient background decoration */
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

        .auth-split {
            width: 100%;
            max-width: 440px;
            display: block;
            background: rgba(20, 20, 20, 0.96);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.55);
            z-index: 2;
            position: relative;
            animation: authCardIn .55s cubic-bezier(.34, 1.2, .64, 1) both;
        }

        .auth-split::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--red) 0%, var(--red2) 100%);
        }

        .auth-form-side {
            padding: 42px 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-section img {
            height: 70px;
            margin-bottom: 12px;
        }

        .logo-section h1 {
            font-family: var(--font-display);
            font-size: 2.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1;
        }

        .logo-section h1 span {
            color: var(--red);
        }

        .logo-section p {
            font-size: 0.85rem;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .notice-alert.success {
            background-color: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.35);
            color: #2ecc71;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-alert {
            background-color: rgba(224, 32, 32, 0.1);
            border: 1px solid rgba(224, 32, 32, 0.3);
            color: #ff5252;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
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
            padding: 14px 44px 14px 40px;
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

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--steel);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s;
            z-index: 10;
        }

        .toggle-password:hover {
            color: var(--red);
        }

        .reg-accounts-container {
            margin-top: 20px;
            padding: 14px;
            background: rgba(10, 10, 10, 0.7);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .reg-accounts-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .reg-accounts-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 160px;
            overflow-y: auto;
        }

        .reg-account-badge {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: rgba(30, 30, 30, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            transition: background 0.2s, border-color 0.2s;
            gap: 10px;
        }

        .reg-account-badge:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .reg-account-info {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            flex: 1;
            min-width: 0;
        }

        .reg-account-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .reg-account-email {
            font-size: 0.78rem;
            color: var(--steel);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .reg-account-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .btn-acc-action {
            padding: 4px 8px;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 5px;
            border: 1px solid var(--border);
            background: rgba(20, 20, 20, 0.8);
            color: var(--white);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }



        .btn-acc-action.remove {
            color: #f39c12;
            border-color: rgba(243, 156, 18, 0.3);
        }

        .btn-acc-action.remove:hover {
            background: rgba(243, 156, 18, 0.2);
        }

        .btn-acc-action.delete {
            color: var(--red);
            border-color: rgba(224, 32, 32, 0.3);
        }

        .btn-acc-action.delete:hover {
            background: rgba(224, 32, 32, 0.2);
        }

        .form-options {
            margin: 4px 0 18px;
            display: flex;
            justify-content: flex-end;
        }

        .forgot-link {
            color: var(--red);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
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
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: var(--red2);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0 16px;
            color: var(--steel);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .social-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 46px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--ink);
            color: var(--white);
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
        }

        .social-btn:hover {
            transform: translateY(-2px);
        }

        .social-btn.facebook {
            background: #1877F2;
            border-color: #1877F2;
        }

        .social-btn.google {
            background: #ffffff;
            color: #3c4043;
            border-color: #dadce0;
        }

        .social-btn.apple {
            background: #000000;
            border-color: #000000;
            color: #fff;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--steel);
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--red);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-footer a:hover {
            color: var(--white);
        }

        @keyframes authCardIn {
            from { opacity: 0; transform: translateY(28px) scale(.97); }
            to   { opacity: 1; transform: none; }
        }

        @keyframes authFadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: none; }
        }

        .logo-section { animation: authFadeUp .5s ease both; }
        .form-group { animation: authFadeUp .5s ease both; }
        .form-group:nth-of-type(1) { animation-delay: .12s; }
        .form-group:nth-of-type(2) { animation-delay: .2s; }
        .form-options { animation: authFadeUp .5s ease both; animation-delay: .28s; }
        .btn-submit { animation: authFadeUp .5s ease both; animation-delay: .34s; }
        .divider { animation: authFadeUp .5s ease both; animation-delay: .42s; }
        .social-row { animation: authFadeUp .5s ease both; animation-delay: .5s; }
        .auth-footer { animation: authFadeUp .5s ease both; animation-delay: .58s; }

        @media (prefers-reduced-motion: reduce) {
            .auth-split, .logo-section, .form-group, .form-options, .btn-submit, .divider, .social-row, .auth-footer {
                animation: none;
            }
        }

        @media (max-width: 820px) {
            .auth-form-side {
                padding: 34px 26px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-decor"></div>
    <div class="bg-decor-left"></div>

    <div class="auth-split">
        <section class="auth-form-side">
            <div class="logo-section">
                <img src="../logos/PKTS LOGO.png" alt="PKTS Dojo Logo">
                <h1>PKTS <span>Karate</span></h1>
                <p>Customer Portal</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php elseif ($get_notice): ?>
                <div class="notice-alert <?php echo $get_notice['type']; ?>">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($get_notice['text']); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control" placeholder="enter your email" required autofocus value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <i class="fas fa-envelope left-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock left-icon"></i>
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="form-options">
                        <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <?php if (!empty($registered_accounts)): ?>
                <div class="reg-accounts-container">
                    <div class="reg-accounts-title">
                        <span><i class="fas fa-users"></i> Registered Accounts (<span id="accCountText"><?php echo count($registered_accounts); ?></span>)</span>
                    </div>
                    <div class="reg-accounts-list">
                        <?php foreach ($registered_accounts as $acc): ?>
                            <div class="reg-account-badge" id="acc-item-<?php echo $acc['id']; ?>">
                                <div class="reg-account-info" onclick="selectAccount('<?php echo htmlspecialchars($acc['email'], ENT_QUOTES); ?>')">
                                    <span class="reg-account-name"><?php echo htmlspecialchars($acc['first_name'] . ' ' . $acc['last_name']); ?></span>
                                    <span class="reg-account-email"><?php echo htmlspecialchars($acc['email']); ?></span>
                                </div>
                                <div class="reg-account-actions">
                                    <button type="button" class="btn-acc-action remove" onclick="removeAccountFromView(event, <?php echo $acc['id']; ?>)" title="Remove from list">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                    <form method="POST" action="login.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete account <?php echo htmlspecialchars($acc['email'], ENT_QUOTES); ?> from database?');">
                                        <input type="hidden" name="action" value="delete_account">
                                        <input type="hidden" name="account_id" value="<?php echo $acc['id']; ?>">
                                        <button type="submit" class="btn-acc-action delete" title="Delete permanently">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="auth-footer">
                <p>New to PKTS? <a href="signup.php">Create Account</a></p>
                <p style="margin-top: 16px; text-align: center;"><a href="../admin/login.php" style="color: var(--red); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-user-shield"></i> User Admin Login</a></p>
            </div>
        </section>
    </div>

    <script>
        const toggle = document.getElementById('togglePassword');
        const pwd = document.getElementById('password');
        if (toggle && pwd) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const isHidden = pwd.getAttribute('type') === 'password';
                pwd.setAttribute('type', isHidden ? 'text' : 'password');
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
                }
            });
        }

        function selectAccount(email) {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.value = email;
                const pwdInput = document.getElementById('password');
                if (pwdInput) {
                    pwdInput.focus();
                }
            }
        }

        function removeAccountFromView(event, id) {
            if (event) event.stopPropagation();
            const item = document.getElementById('acc-item-' + id);
            if (item) {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    item.remove();
                    const list = document.querySelectorAll('.reg-account-badge');
                    const titleCount = document.getElementById('accCountText');
                    if (titleCount) {
                        titleCount.innerText = list.length;
                    }
                }, 300);
            }
        }
    </script>
</body>
</html>
