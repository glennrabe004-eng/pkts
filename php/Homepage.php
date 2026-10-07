<?php
session_start();
// Homepage.php - main landing page
// Auth check is skipped for public access to homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PKTS – Karate for Kids & Teens</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <meta name="description" content="Unlock your child's potential with PKTS Karate classes! Develop discipline, boost confidence, and improve fitness.">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css">
  <style>
    body {
      font-family: 'Inter', 'Roboto', sans-serif !important;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/layout/header.php'; ?>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-slash"></div>
    <div class="hero-stripe"></div>
    <div class="hero-inner">
      <div class="hero-eyebrow">JKS · PKTS Karate Dojo · Pasig, Metro Manila</div>
      <h1>Unlock Their<br><span>Potential</span><br>With Karate</h1>
      <p class="hero-sub">Empower your child with discipline, confidence, and fitness through expert-led Karate training — for kids and teens.</p>
      <div class="hero-actions">
        <a href="register.php" class="btn-primary"><i class="fas fa-calendar-check"></i> Book a Free Trial</a>
        <a href="about.php" class="btn-ghost">Learn More <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <span class="stat-num">10<span>+</span></span>
          <span class="stat-label">Years Training</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">200<span>+</span></span>
          <span class="stat-label">Students Trained</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">16<span>th</span></span>
          <span class="stat-label">Korea Open Participants</span>
        </div>
      </div>
    </div>
  </section>

  <!-- WHY PKTS -->
  <div class="section">
    <div class="section-eyebrow">Why PKTS</div>
    <div class="section-title">More Than Just Karate</div>
    <div class="why-grid">
      <div class="why-card">
        <div class="why-icon"><i class="fas fa-fist-raised"></i></div>
        <h4>Discipline & Focus</h4>
        <p>Our structured curriculum builds strong mental habits that carry over into school, sports, and everyday life.</p>
      </div>
      <div class="why-card">
        <div class="why-icon"><i class="fas fa-shield-alt"></i></div>
        <h4>Real Self-Defense</h4>
        <p>Students learn practical JKS karate techniques tested in international competition, not just forms.</p>
      </div>
      <div class="why-card">
        <div class="why-icon"><i class="fas fa-medal"></i></div>
        <h4>Competition-Ready</h4>
        <p>From local tournaments to international championships in Busan, our students compete and win on the world stage.</p>
      </div>
    </div>
  </div>

  <!-- GALLERY -->
  <div class="section" style="padding-top: 0">
    <div class="section-eyebrow">Our Dojo</div>
    <div class="section-title">Training in Action</div>
    <div class="gallery-grid">
      <div class="gallery-main">
        <img src="../background/dojo.jpg" alt="PKTS Dojo Training">
      </div>
      <div class="gallery-thumb">
        <img src="../background/Dojo1.jpg" alt="Kids Karate Class">
      </div>
      <div class="gallery-thumb">
        <img src="../background/Dojo2.jpg" alt="Teens Karate Class">
      </div>
    </div>
  </div>

  <!-- ACHIEVEMENT -->
  <div class="section" style="padding-top: 0">
    <div class="section-eyebrow">2024 Achievement</div>
    <div class="section-title">Our Success in Korea</div>
    <div class="achievement-wrap">
      <div class="achievement-img">
        <img src="../background/korea.png" alt="PKTS Team in Korea">
      </div>
      <div class="achievement-body">
        <span class="achievement-badge"><i class="fas fa-trophy"></i> International</span>
        <h3>16th Korea Open International Championships</h3>
        <p>We proudly represented PKTS Karate Dojo at the Korea Open International Karate-Do Championships and the 3rd WCKF World Championships in Busan, South Korea — showcasing exceptional skill and sportsmanship on the global stage.</p>
        <a href="about.php" class="btn-primary" style="margin-top: 8px">Read the Full Story <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>

  <!-- CTA BANNER -->
  <div class="cta-banner">
    <div class="cta-text">
      <h2>Ready to Start Training?</h2>
      <p>First trial class is free — no experience needed. All ages and skill levels welcome.</p>
    </div>
    <div class="cta-actions">
      <a href="register.php" class="btn-white"><i class="fas fa-calendar-check"></i> Book Free Trial</a>
      <a href="contact.php" class="btn-outline-white">Contact Us</a>
    </div>
  </div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

  <!-- Scroll to Top -->
  <button id="scrollToTop" aria-label="Scroll to top"><i class="fas fa-arrow-up"></i></button>

  <script src="../js/hp.js"></script>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      var scrollToTop = document.getElementById('scrollToTop');
      
      window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
          scrollToTop.classList.add('show');
        } else {
          scrollToTop.classList.remove('show');
        }
      });
      
      scrollToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  </script>
</body>
</html>