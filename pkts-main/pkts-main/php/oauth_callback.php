<?php
// Social login callback. Handles the OAuth code exchange and login.
// NOTE: real credentials must be set in config/oauth.php for this to complete successfully.
require_once __DIR__ . '/db.php';
$config = require_once __DIR__ . '/config/oauth.php';

$provider = $_SESSION['oauth_provider'] ?? trim($_GET['provider'] ?? '');

// Verify state to prevent CSRF
if (empty($_GET['state']) || empty($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], (string)$_GET['state'])) {
    header("Location: login.php?oauth_error=1");
    exit;
}
unset($_SESSION['oauth_state'], $_SESSION['oauth_provider']);

if (!array_key_exists($provider, $config) || empty($_GET['code'])) {
    header("Location: login.php?oauth_error=1");
    exit;
}

$cfg = $config[$provider];

// Helper: POST request and return decoded response
function oauth_http_post($url, $data, $isJson = false) {
    $body = $isJson ? json_encode($data) : http_build_query($data);
    $headers = ['Content-Type: ' . ($isJson ? 'application/json' : 'application/x-www-form-urlencoded')];
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => implode("\r\n", $headers),
            'content' => $body,
            'timeout' => 15,
        ],
    ];
    $ctx = stream_context_create($opts);
    $res = @file_get_contents($url, false, $ctx);
    if ($res === false) {
        return null;
    }
    return json_decode($res, true);
}

// Minimal JWT payload decoder (no signature verification needed server-side for email)
function oauth_jwt_payload($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) < 2) {
        return [];
    }
    $payload = base64_decode(strtr($parts[1], '-_', '+/'), true);
    if ($payload === false) {
        return [];
    }
    $decoded = json_decode($payload, true);
    return is_array($decoded) ? $decoded : [];
}

function oauth_base64url($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function oauth_apple_secret($cfg) {
    $key = trim($cfg['private_key'] ?? '');
    if ($key === '' && !empty($cfg['private_key_file']) && is_readable($cfg['private_key_file'])) {
        $key = file_get_contents($cfg['private_key_file']);
    }
    $key = trim((string)$key);
    if ($key === '') return null;
    if (strpos($key, '-----BEGIN') === false) {
        $key = "-----BEGIN PRIVATE KEY-----\n" . chunk_split($key, 64, "\n") . "-----END PRIVATE KEY-----";
    }
    $pkey = openssl_pkey_get_private($key);
    if ($pkey === false) return null;
    $header  = oauth_base64url(json_encode(['alg' => 'ES256', 'kid' => $cfg['key_id']]));
    $claims  = oauth_base64url(json_encode([
        'iss' => $cfg['team_id'],
        'iat' => time(),
        'exp' => time() + 3600,
        'aud' => 'https://appleid.apple.com',
        'sub' => $cfg['client_id'],
    ]));
    $payload = $header . '.' . $claims;
    $sig = '';
    if (!openssl_sign($payload, $sig, $pkey, OPENSSL_ALGO_SHA256)) return null;
    return $payload . '.' . oauth_base64url($sig);
}

// 1) Exchange authorization code for an access token
$clientSecret = $cfg['client_secret'];
if ($provider === 'apple') {
    $clientSecret = oauth_apple_secret($cfg);
    if ($clientSecret === null) {
        header("Location: login.php?oauth_error=1");
        exit;
    }
}
$tokenData = oauth_http_post($cfg['token_url'], [
    'client_id'     => $cfg['client_id'],
    'client_secret' => $clientSecret,
    'code'          => $_GET['code'],
    'redirect_uri'  => $cfg['redirect_uri'],
    'grant_type'    => 'authorization_code',
], false);

if (!is_array($tokenData) || empty($tokenData['access_token'])) {
    header("Location: login.php?oauth_error=1");
    exit;
}

$accessToken = $tokenData['access_token'];
$firstName = '';
$lastName = '';
$email = '';

// 2) Fetch user info based on provider
if ($provider === 'google') {
    $ui = @file_get_contents($cfg['userinfo_url'] . '?access_token=' . urlencode($accessToken));
    $user = $ui ? json_decode($ui, true) : null;
    if (is_array($user)) {
        $firstName = $user['given_name'] ?? '';
        $lastName = $user['family_name'] ?? '';
        $email = $user['email'] ?? '';
    }
} elseif ($provider === 'facebook') {
    $ui = @file_get_contents($cfg['userinfo_url'] . '&access_token=' . urlencode($accessToken));
    $user = $ui ? json_decode($ui, true) : null;
    if (is_array($user)) {
        $firstName = $user['first_name'] ?? '';
        $lastName = $user['last_name'] ?? '';
        $email = $user['email'] ?? '';
    }
} elseif ($provider === 'apple') {
    // Apple: email/sub come from the id_token; display name arrives only on first consent
    $idToken = $tokenData['id_token'] ?? '';
    $payload = $idToken ? oauth_jwt_payload($idToken) : [];
    $email = $payload['email'] ?? '';
    // Apple may POST the name on the initial authorization form
    if (!empty($_POST['user'])) {
        $appleUser = json_decode($_POST['user'], true);
        if (is_array($appleUser)) {
            $name = $appleUser['name'] ?? [];
            $firstName = $name['firstName'] ?? $firstName;
            $lastName = $name['lastName'] ?? $lastName;
        }
    }
    if (empty($email)) {
        $email = 'apple-user-' . ($payload['sub'] ?? bin2hex(random_bytes(8))) . '@pktskarate.local';
    }
}

// 3) Validate we got at least an email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?oauth_error=1");
    exit;
}

$email = strtolower(trim($email));
if (empty($firstName)) {
    $firstName = 'Customer';
}
if (empty($lastName)) {
    $lastName = '';
}

// 4) Find existing customer or create one
$stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM customers WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    // Create a new account with a random password
    $randomPw = password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT);
    $phone = '';
    $ins = $conn->prepare("INSERT INTO customers (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)");
    $ins->bind_param("sssss", $firstName, $lastName, $email, $randomPw, $phone);
    $ins->execute();
    $newId = $ins->insert_id;
    $ins->close();
    $user = [
        'id'         => $newId,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
    ];
}

// 5) Establish the same session as login.php
session_regenerate_id(true);
$_SESSION['customer_logged_in'] = true;
$_SESSION['customer_id'] = $user['id'];
$_SESSION['customer_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
$_SESSION['customer_first_name'] = $user['first_name'];
$_SESSION['customer_email'] = $email;

header("Location: Homepage.php");
exit;
