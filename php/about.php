<?php
session_start();
require_once __DIR__ . '/layout/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <meta name="description" content="Learn about PKTS Karate — our dedication, philosophy, and commitment to traditional Shotokan Karate in the Philippines.">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css">
</head>
<body>

  <!-- PAGE HERO -->
  <section class="page-hero">
    <div class="hero-bg"></div>
    <div class="hero-slash"></div>
    <div class="hero-stripe"></div>
    <div class="hero-inner">
      <div class="hero-eyebrow">Philippine Karate Training Society</div>
      <h1>About<br><span>PKTS</span></h1>
    </div>
  </section>

  <div class="about-wrapper">
    <div class="about-block">
      <div class="about-img-wrap">
        <img src="../background/OurDedication_About.jpg" alt="Our Dedication">
      </div>
      <div class="about-text">
        <p class="about-eyebrow"><i class="fas fa-fist-raised"></i> Our Dedication</p>
        <h2>Promoting Traditional Shotokan Karate</h2>
        <p>
          Philippine Karate Training Society (PKTS) is dedicated to the <strong>promotion and development of traditional Shotokan Karate</strong>
          in the Philippines. Founded on the principles of <strong>discipline, respect, and perseverance</strong>, PKTS provides a structured and supportive
          environment for individuals of all ages and skill levels to learn and excel in the art of Karate-do.
        </p>
      </div>
    </div>

    <div class="about-block about-block--reverse">
      <div class="about-img-wrap">
        <img src="../background/OurPhilisophy_About.jpg" alt="Our Philosophy">
      </div>
      <div class="about-text">
        <p class="about-eyebrow"><i class="fas fa-users"></i> Our Philosophy</p>
        <h2>More Than Just a Martial Art</h2>
        <p>
          We believe that Karate is <strong>more than just a martial art</strong>; it is a way of life that cultivates
          <strong>physical fitness, mental fortitude, and character development</strong>. Our experienced and certified instructors
          are committed to imparting not only the techniques and skills of Karate but also the rich philosophy and ethical values that underpin it.
        </p>
      </div>
    </div>

    <div class="about-block">
      <div class="about-img-wrap">
        <img src="../background/OurCommitment_About.jpg" alt="Our Commitment">
      </div>
      <div class="about-text">
        <p class="about-eyebrow"><i class="fas fa-graduation-cap"></i> Our Commitment</p>
        <h2>Empowering Your Journey</h2>
        <p>
          Through <strong>rigorous training, regular practice, and a spirit of continuous improvement</strong>, PKTS aims to empower its members
          to achieve their full potential, both on and off the dojo. Whether you are a beginner taking your first steps in martial arts
          or an experienced practitioner seeking to refine your skills, PKTS offers a <strong>welcoming and challenging community</strong> to help you on your journey.
        </p>
      </div>
    </div>
  </div>

  <div class="offers-section">
    <div class="offers-inner">
      <div class="section-eyebrow">What We Offer</div>
      <div class="section-title">Training That Transforms</div>
      <div class="offers-grid">
        <div class="offer-card">
          <div class="offer-icon"><i class="fas fa-dumbbell"></i></div>
          <h4>Physical Development</h4>
          <p>Full-body workout enhancing strength, stamina, flexibility, balance, and coordination for a healthy lifestyle.</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon"><i class="fas fa-brain"></i></div>
          <h4>Mental Growth</h4>
          <p>Develop discipline, focus, confidence, and mental resilience through structured training and practice.</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon"><i class="fas fa-shield-alt"></i></div>
          <h4>Self-Defense Skills</h4>
          <p>Learn practical techniques for self-protection and build confidence to handle challenging situations.</p>
        </div>
        <div class="offer-card">
          <div class="offer-icon"><i class="fas fa-heart"></i></div>
          <h4>Character Building</h4>
          <p>Cultivate respect, integrity, humility, and build positive relationships within our supportive community.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="cta-banner">
    <div class="cta-text">
      <h2>Interested in Joining?</h2>
      <p>First trial class is free — no experience needed. All ages and skill levels welcome.</p>
    </div>
    <div class="cta-actions">
      <a href="register.php" class="btn-white"><i class="fas fa-calendar-check"></i> Book Free Trial</a>
      <a href="contact.php" class="btn-outline-white">Contact Us</a>
    </div>
  </div>

  <?php require_once __DIR__ . '/layout/footer.php'; ?>

  <script src="../js/hp.js"></script>
</body>
</html>