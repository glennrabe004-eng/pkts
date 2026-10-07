<?php
require_once __DIR__ . '/db.php';

$error_msg = '';
$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($phone)) {
        $error_msg = 'Please fill out all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[A-Z]/', $password)) {
        $error_msg = 'Password must start with a capital letter.';
    } elseif (strlen($password) < 8) {
        $error_msg = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $error_msg = 'Password must contain at least one special character (e.g. !@#$%^&*).';
    } else {
        // Check uniqueness of email
        $stmt_check = $conn->prepare("SELECT id FROM customers WHERE email = ? LIMIT 1");
        if ($stmt_check) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $error_msg = 'This email is already registered.';
                $stmt_check->close();
            } else {
                $stmt_check->close();
                // Hash password
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

                // Insert customer
                $stmt_insert = $conn->prepare("INSERT INTO customers (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)");
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sssss", $first_name, $last_name, $email, $hashed_pass, $phone);
                    if ($stmt_insert->execute()) {
                        $success_msg = 'Account registered successfully! Redirecting to login...';
                        header("refresh:2;url=login.php");
                    } else {
                        $error_msg = 'Failed to create account. Please try again.';
                    }
                    $stmt_insert->close();
                } else {
                    $error_msg = 'Database error. Please try again.';
                }
            }
        } else {
            $error_msg = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Signup - PKTS Karate</title>
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

        .auth-container {
            width: 100%;
            max-width: 480px;
            z-index: 2;
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
            height: 60px;
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
            margin-bottom: 20px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-alert {
            background-color: rgba(46, 125, 50, 0.1);
            border: 1px solid rgba(46, 125, 50, 0.3);
            color: #81c784;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .name-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--steel);
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            background-color: var(--ink);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 42px 12px 38px;
            color: var(--white);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(224, 32, 32, 0.15);
        }

        .form-control:focus + i {
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
            padding: 14px;
            font-size: 0.95rem;
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

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--steel);
            font-size: 0.88rem;
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
    </style>
</head>
<body>

    <div class="bg-decor"></div>
    <div class="bg-decor-left"></div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-section">
                <img src="../logos/PKTS LOGO.png" alt="PKTS Dojo Logo">
                <h1>Create <span>Account</span></h1>
                <p>Register as a PKTS Member</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="signup.php" autocomplete="off">
                <div class="name-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Juan" required value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" oninput="this.value=this.value.replace(/[0-9]/g,'')">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Dela Cruz" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" oninput="this.value=this.value.replace(/[0-9]/g,'')">
                            <i class="fas fa-user-friends"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control" placeholder="juan@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrapper">
                        <input type="tel" id="phone" name="phone" maxlength="11" class="form-control" placeholder="09171234567" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <i class="fas fa-phone"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="password-requirements" style="margin-top: 10px; font-size: 0.78rem; color: var(--steel); background: rgba(10, 10, 10, 0.5); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--border);">
                        <p style="margin-bottom: 6px; font-weight: 600; color: var(--off);">Password Requirements:</p>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <span id="reqCap"><i class="fas fa-times-circle" style="margin-right: 6px; color: var(--steel);"></i> First letter must be Capital (A-Z)</span>
                            <span id="reqLen"><i class="fas fa-times-circle" style="margin-right: 6px; color: var(--steel);"></i> Must be at least 8 characters long</span>
                            <span id="reqSpec"><i class="fas fa-times-circle" style="margin-right: 6px; color: var(--steel);"></i> Must contain a special character (!@#$%^&*)</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
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

        if (pwd) {
            pwd.addEventListener('input', function() {
                const val = this.value;
                const cap = /^[A-Z]/.test(val);
                const len = val.length >= 8;
                const spec = /[^a-zA-Z0-9]/.test(val);

                const elCap = document.getElementById('reqCap');
                const elLen = document.getElementById('reqLen');
                const elSpec = document.getElementById('reqSpec');

                if (elCap) {
                    elCap.style.color = cap ? '#2ecc71' : 'var(--steel)';
                    elCap.querySelector('i').className = cap ? 'fas fa-check-circle' : 'fas fa-times-circle';
                    elCap.querySelector('i').style.color = cap ? '#2ecc71' : 'var(--steel)';
                }
                if (elLen) {
                    elLen.style.color = len ? '#2ecc71' : 'var(--steel)';
                    elLen.querySelector('i').className = len ? 'fas fa-check-circle' : 'fas fa-times-circle';
                    elLen.querySelector('i').style.color = len ? '#2ecc71' : 'var(--steel)';
                }
                if (elSpec) {
                    elSpec.style.color = spec ? '#2ecc71' : 'var(--steel)';
                    elSpec.querySelector('i').className = spec ? 'fas fa-check-circle' : 'fas fa-times-circle';
                    elSpec.querySelector('i').style.color = spec ? '#2ecc71' : 'var(--steel)';
                }
            });
        }
    </script>
</body>
</html>
