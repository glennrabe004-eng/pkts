<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <meta name="description" content="Get in touch with PKTS Karate Dojo in Pasig, Metro Manila. Visit us, call us, or find us on the map.">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css" />
  <style>
    .contact-page {
      background: #f5f5f5;
    }

    .contact-page .section {
      padding: 48px 20px;
    }

    .contact-page .container {
      max-width: 1100px;
      margin: 0 auto;
    }

    .contact-page .page-title {
      font-family: 'Anton', sans-serif;
      font-size: clamp(2rem, 5vw, 3rem);
      color: #0a0a0a;
      margin: 0 0 8px;
    }

    .contact-page .page-subtitle {
      font-size: 1.05rem;
      color: #333333;
      margin: 0 0 28px;
    }

    .contact-page .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }

    .contact-page .info-card {
      background: #ffffff;
      border: 1px solid #e3e3e3;
      border-radius: 12px;
      padding: 22px;
    }

    .contact-page .info-card .info-icon {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #e02020;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      margin-bottom: 12px;
    }

    .contact-page .info-card h3 {
      font-family: 'Anton', sans-serif;
      font-size: 1.15rem;
      color: #0a0a0a;
      margin: 0 0 8px;
    }

    .contact-page .info-card p,
    .contact-page .info-card a {
      color: #1a1a1a;
      font-size: 0.95rem;
      line-height: 1.55;
      text-decoration: none;
    }

    .contact-page .info-card a:hover {
      text-decoration: underline;
      color: #b71c1c;
    }

    .contact-page .dojo-box {
      background: #ffffff;
      border: 1px solid #e3e3e3;
      border-radius: 12px;
      padding: 22px;
      margin-top: 20px;
    }

    .contact-page .dojo-box h3 {
      font-family: 'Anton', sans-serif;
      font-size: 1.25rem;
      color: #0a0a0a;
      margin: 0 0 10px;
    }

    .contact-page .dojo-box p {
      color: #1a1a1a;
      margin: 0 0 10px;
    }

    .contact-page .hero {
      position: relative;
      padding: calc(var(--nav-h) + 60px) 20px 80px;
      background: linear-gradient(rgba(10, 10, 10, 0.5), rgba(10, 10, 10, 0.6)), url("../background/BG1.png") center/cover no-repeat;
      text-align: center;
      color: #ffffff;
      min-height: 380px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .contact-page .hero .hero-inner {
      max-width: 720px;
    }

    .contact-page .hero .eyebrow {
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #ff4757;
      margin-bottom: 12px;
    }

    .contact-page .hero h1 {
      font-family: 'Anton', sans-serif;
      font-size: clamp(2.2rem, 6vw, 3.2rem);
      color: #ffffff;
      margin: 0 0 16px;
    }

    .contact-page .hero p {
      font-size: 1.05rem;
      color: #e8e8e8;
      margin-bottom: 28px;
    }

    .contact-page .hero .hero-actions {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .contact-page .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: #e02020;
      color: #ffffff;
      text-decoration: none;
      font-weight: 700;
      border-radius: 50px;
      font-size: 0.95rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .contact-page .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(224, 32, 32, 0.4);
    }

    .contact-page .btn-ghost {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
      text-decoration: none;
      font-weight: 600;
      border-radius: 50px;
      font-size: 0.95rem;
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: background 0.2s ease;
    }

    .contact-page .btn-ghost:hover {
      background: rgba(255, 255, 255, 0.25);
    }

    .contact-page .map-section {
      padding: 60px 20px;
    }

    .contact-page .map-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 28px;
    }

    .contact-page .map-title h3 {
      font-family: 'Anton', sans-serif;
      font-size: clamp(1.4rem, 4vw, 1.8rem);
      color: #0a0a0a;
      margin: 0;
    }

    .contact-page .map-title p {
      color: #333333;
      margin: 4px 0 0;
      font-size: 0.95rem;
    }

    .contact-page .map-eyebrow {
      font-size: 0.8rem;
      color: #e02020;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 6px;
    }

    .contact-page .find-location-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 24px;
      background: #e02020;
      color: #ffffff;
      text-decoration: none;
      font-weight: 600;
      border-radius: 50px;
      transition: transform 0.2s ease;
    }

    .contact-page .find-location-btn:hover {
      transform: translateY(-2px);
    }

    .contact-page .map-container {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      min-height: 360px;
    }

    .contact-page .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
    }

    .contact-page .location-overlay {
      position: absolute;
      bottom: 20px;
      left: 20px;
      background: #ffffff;
      padding: 16px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      z-index: 10;
      max-width: 260px;
    }

    .contact-page .location-overlay h4 {
      font-family: 'Anton', sans-serif;
      font-size: 1.2rem;
      color: #e02020;
      margin: 0 0 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .contact-page .location-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      background: #fff7e6;
      color: #e02020;
      border-radius: 6px;
      font-size: 0.85rem;
      margin: 2px 0;
    }

    .contact-page .schedule-box {
      background: #ffffff;
      border: 1px solid #e3e3e3;
      border-radius: 12px;
      padding: 22px;
      margin-top: 20px;
    }

    .contact-page .schedule-box h3 {
      font-family: 'Anton', sans-serif;
      font-size: 1.15rem;
      color: #0a0a0a;
      margin: 0 0 12px;
    }

    .contact-page .schedule-box p {
      color: #1a1a1a;
      margin: 4px 0;
      line-height: 1.6;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>

  <div class="contact-page">
    <!-- ── HERO WITH BACKGROUND IMAGE ── -->
    <section class="hero">
      <div class="hero-inner">
        <div class="eyebrow">JKS · PKTS Karate Dojo · Pasig, Metro Manila</div>
        <h1>Get In <span style="color: #ff4757;">Touch</span></h1>
        <p>We're here to answer your questions and help you get started with karate training.</p>
        <div class="hero-actions">
          <a href="tel:09277869304" class="btn-primary"><i class="fas fa-phone"></i> Call Us</a>
          <a href="mailto:ryo08@gmail.com" class="btn-ghost"><i class="fas fa-envelope"></i> Email Us</a>
        </div>
      </div>
    </section>

    <!-- ── CONTACT INFO ── -->
    <section class="section">
      <div class="container">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">We’re here to answer your questions and help you get started with karate training.</p>

        <div class="info-grid">
          <div class="info-card">
            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
            <h3>Visit Us</h3>
            <p>Robinson Metro East, Studio Level 2,<br>Barangay Marikina-Infanta Hwy,<br>Pasig, 1600 Metro Manila</p>
          </div>
          <div class="info-card">
            <div class="info-icon"><i class="fas fa-envelope"></i></div>
            <h3>Email Us</h3>
            <p><a href="mailto:ryo08@gmail.com">ryo08@gmail.com</a></p>
          </div>
          <div class="info-card">
            <div class="info-icon"><i class="fas fa-phone"></i></div>
            <h3>Call Us</h3>
            <p><a href="tel:09277869304">0927-786-9304</a><br><a href="tel:09171834520">0917-183-4520</a></p>
          </div>
          <div class="info-card">
            <div class="info-icon"><i class="fas fa-clock"></i></div>
            <h3>Hours</h3>
            <p>Monday-Friday: 4:00 PM - 9:00 PM<br>Saturday-Sunday: 8:00 AM - 6:00 PM</p>
          </div>
        </div>

        <div class="dojo-box">
          <h3>PKTS Dojo</h3>
          <p>Robinson Metro East<br>Pasig, Metro Manila</p>
          <p>🥋 Training Every Day</p>
          <p>More locations coming soon to serve you better.</p>
        </div>

        <div class="schedule-box">
          <h3>Training Hours</h3>
          <p><strong>Monday - Friday:</strong> 4:00 PM - 9:00 PM</p>
          <p><strong>Saturday - Sunday:</strong> 8:00 AM - 6:00 PM</p>
        </div>
      </div>
    </section>

    <!-- ── LOCATION MAP ── -->
    <div class="map-section">
      <div class="container">
        <div class="map-header">
          <div class="map-title">
            <div class="map-eyebrow">Our Dojo</div>
            <h3>Find Our Dojo</h3>
            <p>Visit us at our training facility in Pasig, Metro Manila</p>
          </div>
          <a href="https://www.google.com/maps/place/Robinsons+Metro+East/@14.6196165,121.0999832,17z" target="_blank" class="find-location-btn">
            <i class="fas fa-location-arrow"></i> Get Directions
          </a>
        </div>

        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.639223086763!2d121.0999832!3d14.619616499999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b838d3c7c0af%3A0x640d523d11677ff!2sRobinsons%20Metro%20East!5e0!3m2!1sen!2sph!4v1761311372401!5m2!1sen!2sph"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen></iframe>

          <div class="location-overlay">
            <h4><i class="fas fa-crosshairs"></i> PKTS Dojo</h4>
            <div class="location-badge"><i class="fas fa-map-marker-alt"></i> Robinson Metro East</div>
            <div class="location-badge"><i class="fas fa-map-marker-alt"></i> Pasig, Metro Manila</div>
            <div class="location-badge"><i class="fas fa-clock"></i> Training Every Day 🥋</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/layout/footer.php'; ?>
  <script src="../js/hp.js"></script>
</body>
</html>
