<?php
session_start();
require_once __DIR__ . '/config/database.php';

$error_msg = '';
$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_msg = 'Please cover both fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE email = ? AND status = 'Active' LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    if ($update_stmt) {
                        $update_stmt->bind_param("i", $user['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_msg = 'Incorrect password. Please try again.';
                }
            } else {
                $error_msg = 'Invalid email or account not found.';
            }
            $stmt->close();
        } else {
            $error_msg = 'An error occurred. Please try again later.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $username = trim($_POST['new_username'] ?? '');
    $new_email = trim($_POST['new_email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $role = trim($_POST['new_role'] ?? 'Admin');

    if (!empty($username) && !empty($new_email) && !empty($new_password)) {
        $chk = $conn->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
        $chk->bind_param("ss", $username, $new_email);
        $chk->execute();
        $res = $chk->get_result();
        if ($res->num_rows > 0) {
            $error_msg = "Username or email already exists.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password, role, status) VALUES (?, ?, ?, ?, 'Active')");
            $stmt->bind_param("ssss", $username, $new_email, $hashed, $role);
            if ($stmt->execute()) {
                $success_msg = "Admin account '$username' created! <a href='login.php'>Login now</a>";
            } else {
                $error_msg = "Failed to create admin user.";
            }
            $stmt->close();
        }
        $chk->close();
    } else {
        $error_msg = "Username, email, and password are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PKTS Karate</title>
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
        }

        .toggle-password:hover {
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
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: var(--red2);
        }

        .btn-submit:active {
            transform: scale(0.99);
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
        .btn-submit { animation: authFadeUp .5s ease both; animation-delay: .34s; }

        @media (prefers-reduced-motion: reduce) {
            .auth-split, .logo-section, .form-group, .btn-submit {
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
                <h1>Admin <span>Panel</span></h1>
                <p>Staff Login</p>
            </div>

<?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success_msg; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg) && !isset($_POST['create_admin'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <input type="hidden" id="showCreateForm" value="0">
            
            <form method="POST" action="login.php" autocomplete="off" id="loginForm">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control" placeholder="admin@pktskarate.com" required autofocus value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
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
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Admin Login
                </button>
            </form>

            <div id="createAdminForm" style="display: none; margin-top: 24px; padding: 20px; background: rgba(20,20,20,0.6); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="color: var(--red); margin-bottom: 16px; font-family: var(--font-display); font-size: 1.3rem;"><i class="fas fa-user-plus"></i> Create New Admin Account</h3>
                <?php if (!empty($error_msg) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error_msg); ?></span>
                    </div>
                <?php endif; ?>
                <form method="POST" action="login.php" autocomplete="off">
                    <input type="hidden" name="action" value="create_admin">
                    <input type="hidden" name="create_admin" value="1">
                    <div class="form-group">
                        <label for="new_username">Username</label>
                        <input type="text" id="new_username" name="new_username" class="form-control" placeholder="e.g. new_admin" required>
                    </div>
                    <div class="form-group">
                        <label for="new_email">Email Address</label>
                        <input type="email" id="new_email" name="new_email" class="form-control" placeholder="admin@pktskarate.com" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="••••••••" required>
                            <i class="fas fa-lock left-icon"></i>
                            <button type="button" class="toggle-password" id="toggleNewPassword" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new_role">Role</label>
                        <select name="new_role" id="new_role" class="form-control">
                            <option value="Admin">Administrator</option>
                            <option value="Dojo Manager">Dojo Manager</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-check-circle"></i> Create Admin Account
                    </button>
                </form>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <button type="button" id="toggleForm" class="btn btn-sm" style="background: var(--mid-dark); color: var(--white); border: 1px solid var(--border); padding: 10px 20px; border-radius: 20px; cursor: pointer; transition: background 0.2s;">
                    <i class="fas fa-user-plus"></i> Need to create an admin account?
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 16px;">
                <a href="../php/login.php" style="color: var(--steel); font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-user"></i> Go to customer portal?
                </a>
            </div>
        </section>
    </div>

    <script>
        const toggle = document.getElementById('togglePassword');
        const pwd = document.getElementById('password');
        if (toggle && pwd) {
            toggle.addEventListener('click', function () {
                const isHidden = pwd.getAttribute('type') === 'password';
                pwd.setAttribute('type', isHidden ? 'text' : 'password');
                this.querySelector('i').classList.toggle('fa-eye', !isHidden);
                this.querySelector('i').classList.toggle('fa-eye-slash', isHidden);
            });
        }

        const toggleNewPwd = document.getElementById('toggleNewPassword');
        const newPwd = document.getElementById('new_password');
        if (toggleNewPwd && newPwd) {
            toggleNewPwd.addEventListener('click', function () {
                const isHidden = newPwd.getAttribute('type') === 'password';
                newPwd.setAttribute('type', isHidden ? 'text' : 'password');
                this.querySelector('i').classList.toggle('fa-eye', !isHidden);
                this.querySelector('i').classList.toggle('fa-eye-slash', isHidden);
            });
        }

        const toggleBtn = document.getElementById('toggleForm');
        const loginForm = document.getElementById('loginForm');
        const createForm = document.getElementById('createAdminForm');
        if (toggleBtn && loginForm && createForm) {
            toggleBtn.addEventListener('click', function () {
                const isLoginVisible = loginForm.style.display !== 'none';
                if (isLoginVisible) {
                    loginForm.style.display = 'none';
                    createForm.style.display = 'block';
                } else {
                    createForm.style.display = 'none';
                    loginForm.style.display = 'block';
                }
            });
        }
    </script>
</body>
</html>