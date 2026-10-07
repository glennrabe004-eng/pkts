<?php
// Shared header / navigation

// Load customer avatar from database for global display on all pages
if (!isset($conn)) {
    require_once __DIR__ . '/../db.php';
}
if (isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true && isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $avatar_stmt = $conn->prepare("SELECT avatar FROM customers WHERE id = ? LIMIT 1");
    if ($avatar_stmt) {
        $avatar_stmt->bind_param("i", $customer_id);
        $avatar_stmt->execute();
        $avatar_result = $avatar_stmt->get_result();
        if ($avatar_row = $avatar_result->fetch_assoc()) {
            if (!empty($avatar_row['avatar'])) {
                $_SESSION['customer_avatar'] = $avatar_row['avatar'];
            }
        }
        $avatar_stmt->close();
    }
}
?>
<nav class="nav" id="mainNav">
  <div class="nav-left">
    <a href="Homepage.php" class="nav-brand">
      <img src="../logos/PKTS LOGO.png" alt="PKTS Logo" id="navLogo">
    </a>

    <ul class="nav-left-nav" id="navLinks">
      <li><a href="Homepage.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li class="has-drop">
        <a href="#" class="drop-toggle" id="branchesToggle">
          <i class="fas fa-location-dot" style="color: var(--red);"></i> Branches <i class="fas fa-chevron-down drop-toggle-icon"></i>
        </a>
        <div class="drop-menu branch-menu">
          <a href="contact.php?branch=robinson_metro_east" onclick="selectBranch('Robinson Metro East', event)"><i class="fas fa-map-marker-alt"></i> Robinson Metro East</a>
          <a href="contact.php?branch=vista_mall_antipolo" onclick="selectBranch('Vista Mall Antipolo', event)"><i class="fas fa-map-marker-alt"></i> Vista Mall Antipolo</a>
          <a href="contact.php?branch=marikina_falcon" onclick="selectBranch('Marikina Falcon', event)"><i class="fas fa-map-marker-alt"></i> Marikina Falcon</a>
        </div>
      </li>
      <li><a href="progress.php"><i class="fas fa-medal" style="color: var(--red);"></i> Belt Progress</a></li>
      <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="register.php" class="nav-cta">Book Now</a></li>
    </ul>
  </div>

  <!-- Mobile Top Bar: Profile, Logout, Hamburger all on right -->
  <div class="nav-mobile-top">
    <div class="nav-mobile-actions">
      <a href="profile.php" class="customer-nav-name mobile-nav-item">
        <?php if (!empty($_SESSION['customer_avatar'])): ?>
          <img src="<?php echo htmlspecialchars($_SESSION['customer_avatar']); ?>" alt="Profile" class="customer-nav-avatar">
        <?php else: ?>
          <i class="fas fa-circle-user customer-nav-icon"></i>
        <?php endif; ?>
      </a>
      <a href="logout.php" class="logout-nav-btn mobile-nav-item" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </a>
      <button id="mobileMenuBtn" class="mobile-menu-btn hamburger-right" aria-label="Toggle Navigation">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>

  <div class="nav-right">
    <a href="profile.php" class="customer-nav-name">
      <?php if (!empty($_SESSION['customer_avatar'])): ?>
        <img src="<?php echo htmlspecialchars($_SESSION['customer_avatar']); ?>" alt="Profile" class="customer-nav-avatar">
      <?php else: ?>
        <i class="fas fa-circle-user customer-nav-icon"></i>
      <?php endif; ?>
      <span class="cust-name-text"><?php echo htmlspecialchars($_SESSION['customer_first_name'] ?? 'Guest'); ?></span>
    </a>

    <a href="logout.php" class="logout-nav-btn" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</nav>

<script>
  function selectBranch(name, e) {
    localStorage.setItem('pkts_selected_branch', name);
  }

  (function() {
    var mobileMenuBtn = document.getElementById('mobileMenuBtn');
    var navLinks = document.getElementById('navLinks');
    var branchesToggle = document.getElementById('branchesToggle');
    var dropToggle = document.querySelector('.drop-toggle');
    var ticking = false;
    
    function updateHeader() {
      var header = document.querySelector('header, .main-header, .nav');
      
      if (!header) return;
      
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }
    
    function onScroll() {
      if (!ticking) {
        window.requestAnimationFrame(function() {
          updateHeader();
          ticking = false;
        });
        ticking = true;
      }
    }
    
    if (mobileMenuBtn && navLinks) {
      mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        navLinks.classList.toggle('active');
        mobileMenuBtn.classList.toggle('active');
      });
    }

    if (dropToggle && branchesToggle) {
      dropToggle.addEventListener('click', function(e) {
        e.preventDefault();
        var parentLi = dropToggle.closest('.has-drop');
        if (parentLi) {
          parentLi.classList.toggle('show');
        }
        var icon = dropToggle.querySelector('.drop-toggle-icon');
        if (icon) {
          icon.classList.toggle('fa-chevron-down');
          icon.classList.toggle('fa-chevron-up');
        }
      });
    }

    document.addEventListener('click', function(e) {
      if (navLinks && navLinks.classList.contains('active')) {
        var isClickInside = navLinks.contains(e.target) || (mobileMenuBtn && mobileMenuBtn.contains(e.target));
        if (!isClickInside) {
          navLinks.classList.remove('active');
          mobileMenuBtn.classList.remove('active');
        }
      }
    });

    window.addEventListener('scroll', onScroll);
    updateHeader();
  })();
</script>