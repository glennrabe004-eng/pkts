<?php
// Social login initiator. Usage: oauth.php?provider=google|facebook|apple
// NOTE: real credentials must be set in config/oauth.php before this works end-to-end.
require_once __DIR__ . '/db.php';
$config = require_once __DIR__ . '/config/oauth.php';

/**
 * Local/demo sign-in: creates or finds a demo customer and logs them in,
 * mimicking the real OAuth callback. For local testing only.
 */
function oauth_demo_login($provider) {
    global $conn;
    $map = [
        'google'   => ['Google', 'Demo', 'User'],
        'facebook' => ['Facebook', 'Demo', 'User'],
        'apple'    => ['Apple', 'Demo', 'User'],
    ];
    $n = $map[$provider] ?? ['Demo', 'Test', 'User'];
    $email = 'demo.' . preg_replace('/[^a-z]/', '', $provider) . '@pktskarate.local';

    $stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM customers WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $hash = password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT);
        $phone = '';
        $ins = $conn->prepare("INSERT INTO customers (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("sssss", $n[1], $n[2], $email, $hash, $phone);
        $ins->execute();
        $id = $ins->insert_id;
        $ins->close();
        $user = ['id' => $id, 'first_name' => $n[1], 'last_name' => $n[2], 'email' => $email];
    }

    session_regenerate_id(true);
    $_SESSION['customer_logged_in'] = true;
    $_SESSION['customer_id'] = $user['id'];
    $_SESSION['customer_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['customer_first_name'] = $user['first_name'];
    $_SESSION['customer_email'] = $email;

    header("Location: Homepage.php");
    exit;
}

$provider = trim($_GET['provider'] ?? '');
$demo = (!empty($_GET['demo']) || $provider === 'demo');

// Local/demo mode: if no real credentials are configured (placeholders),
// or demo is explicitly requested, bypass the external provider and log in a demo user.
$useDemo = $demo;
if (!$useDemo && array_key_exists($provider, $config) && strpos((string)$config[$provider]['client_id'], 'YOUR_') === 0) {
    $useDemo = true;
}
if (!$useDemo && !array_key_exists($provider, $config)) {
    header("Location: login.php?oauth_error=1");
    exit;
}

if ($useDemo) {
    oauth_demo_login($provider === 'demo' ? 'google' : $provider);
    exit; // never reached
}

$cfg = $config[$provider];

// Generate CSRF/state token and store in session
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$_SESSION['oauth_provider'] = $provider;

$params = http_build_query([
    'client_id'     => $cfg['client_id'],
    'redirect_uri'  => $cfg['redirect_uri'],
    'response_type' => 'code',
    'scope'         => $cfg['scope'],
    'state'         => $state,
]);

$sep = strpos($cfg['auth_url'], '?') !== false ? '&' : '?';
header("Location: " . $cfg['auth_url'] . $sep . $params);
exit;
