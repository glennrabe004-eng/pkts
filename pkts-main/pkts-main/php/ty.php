<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You - PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/hp.css">
  <style>
    body {
      font-family: sans-serif;
      background-color: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh; padding-top: 96px;
      text-align: center;
      opacity: 1;
    }

    .thank-you-container {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 500px;
      margin: 0 20px;
    }

    .thank-you-container h1 {
      color: #102688;
      font-size: clamp(1.8rem, 5vw, 2.5rem);
    }

    .thank-you-container p {
      margin-top: 10px;
      font-size: 1.2em;
    }

    .thank-you-container a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #961616;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .thank-you-container a:hover {
      background-color: #b71c1c;
    }

    .nav { background: rgba(10,10,10,.92); }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>
  <div class="thank-you-container">
    <h1>Thank You for Your Purchase!</h1>
    <p>Your order has been received and stored securely in our database.</p>
    <a href="Homepage.php" target="_self">Return to Home</a>
  </div>
</body>
</html>
