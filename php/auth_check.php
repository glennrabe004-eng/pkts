<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'signup.php', 'ty.php', 'ty2.php', 'contact.php', 'uniform.php', 'eqpmnt.php'];

if (!in_array($current_page, $public_pages)) {
    if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}
?>
