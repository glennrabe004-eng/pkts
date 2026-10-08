<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Trial Class – PKTS Karate</title>
  <link rel="icon" href="../logos/PKTS LOGO.png">
  <meta name="description" content="Book your free trial karate class at PKTS Karate Dojo in Pasig, Metro Manila. All ages and skill levels welcome.">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/design.css" />
  <style>
    body {
      font-family: 'Inter', 'Roboto', sans-serif !important;
    }

    .bg-register {
      background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #252525 100%);
      min-height: 100vh;
      padding-bottom: 60px;
    }

    .register-hero {
      position: relative;
      padding-top: calc(var(--nav-h) + 100px);
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      background: linear-gradient(rgba(10, 10, 10, 0.85), rgba(20, 20, 20, 0.9)), url("../background/BG1.png") center/cover no-repeat;
      color: var(--white);
    }

    .register-hero .hero-inner {
      max-width: 700px;
      padding: 40px 24px;
    }

    .register-hero h1 {
      font-size: clamp(2.5rem, 6vw, 3.5rem);
      font-family: var(--font-display);
      letter-spacing: -0.04em;
      margin-bottom: 24px;
    }

    .register-hero .hero-sub {
      font-size: 1.1rem;
      opacity: 0.95;
      margin-bottom: 32px;
    }

    .register-hero .hero-actions {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .trial-container {
      max-width: 700px;
      margin: 80px auto 60px;
      background: var(--white);
      border-radius: var(--r-lg);
      box-shadow: var(--shadow-xl);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .trial-header {
      background: linear-gradient(135deg, var(--red), var(--red2));
      color: var(--white);
      padding: 40px 30px;
      text-align: center;
    }

    .trial-header .trial-header-eyebrow {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.9);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .trial-header h1 {
      font-family: var(--font-display);
      font-size: 2.2rem;
      letter-spacing: -0.03em;
      margin: 0 0 16px;
    }

    .trial-header p {
      margin: 0;
      opacity: 0.95;
      font-size: 1rem;
    }

    .trial-form {
      padding: 40px 30px;
    }

    .section-title {
      font-family: var(--font-display);
      font-size: 1.4rem;
      color: var(--ink);
      margin: 32px 0 20px;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--red-light);
      display: inline-block;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-field label {
      font-weight: 600;
      color: var(--ink);
      font-size: 0.95rem;
    }

    .form-field label span {
      color: var(--red);
      margin-left: 4px;
    }

    .form-field input,
    .form-field select,
    .form-field textarea {
      padding: 12px 14px;
      border: 2px solid var(--border);
      border-radius: var(--r-sm);
      font-size: 1rem;
      transition: border-color 0.25s ease, box-shadow 0.25s ease;
      background: var(--white);
    }

    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus {
      outline: none;
      border-color: var(--red);
      box-shadow: 0 0 0 3px rgba(224, 32, 32, 0.15);
    }

    .form-field input[type="date"] {
      padding: 12px;
    }

    .note {
      font-size: 0.9rem;
      color: var(--ink-dark);
      margin: 12px 0 20px;
    }

    .note strong {
      color: var(--red);
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, var(--red), var(--red2));
      color: var(--white);
      border: none;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 28px rgba(224, 32, 32, 0.45);
      background: linear-gradient(135deg, #d62828, #e63946);
    }

    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .toast {
      position: fixed;
      top: 100px;
      left: 50%;
      transform: translateX(-50%);
      background: #3e3e49;
      color: #fff;
      padding: 16px 32px;
      border-radius: 8px;
      font-weight: bold;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      opacity: 0;
      animation: toast-fadein 0.4s ease forwards, toast-fadeout 0.4s ease 2.5s forwards;
    }

    @keyframes toast-fadein {
      from { opacity: 0; transform: translate(-50%, -20px); }
      to { opacity: 1; transform: translate(-50%, 0); }
    }

    @keyframes toast-fadeout {
      from { opacity: 1; transform: translate(-50%, 0); }
      to { opacity: 0; transform: translate(-50%, -20px); }
    }
</style>
</head>
<body>

  <?php require_once __DIR__ . '/layout/header.php'; ?>

  <div class="bg-register">
    <!-- ── HERO ── -->
    <section class="register-hero">
      <div class="hero-inner">
        <div class="hero-eyebrow">Free Trial · No Experience Needed</div>
        <h1>Book Your <span style="color: var(--red);">Karate</span><br>Trial Class</h1>
        <p class="hero-sub">Join the PKTS family — your first class is on us. Fill out the form below and we'll confirm your slot.</p>
      </div>
    </section>

    <!-- ── FORM ── -->
    <div class="trial-container">
      <div class="trial-header">
        <div class="trial-header-eyebrow">Guardian & Student Details</div>
        <h2>Guardian & Student Details</h2>
        <p>Fill in your information to secure your trial slot</p>
      </div>

      <div class="trial-form">
        <div id="slot-counts" style="margin-bottom: 20px; padding: 16px; background: #f8f9fa; border-radius: 12px; display: none;">
          <div class="slot-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 class="slot-title" style="margin: 0; font-size: 1rem; color: #333;">Current Slot Availability</h3>
            <span class="refresh-btn" style="cursor: pointer; color: #e02020; font-size: 0.9rem;" onclick="refreshSlots()">↻ Refresh</span>
          </div>
          <div id="slots-list" style="display: grid; gap: 8px;"></div>
        </div>
        <form action="submit_booking.php" method="POST" enctype="multipart/form-data" data-ajax="true">
          <!-- Dojo Branch Selection -->
          <h2 class="section-title">Select Dojo Branch</h2>
          <div class="form-grid">
            <div class="form-field" style="grid-column: 1 / -1;">
              <label>PKTS Branch <span>*</span></label>
              <select name="branch" required>
                <option value="Robinson Metro East">Robinson Metro East (Marikina-Pasig)</option>
                <option value="Vista Mall Antipolo">Vista Mall Antipolo</option>
                <option value="Marikina Falcon">Marikina Falcon</option>
              </select>
            </div>
          </div>

          <!-- Guardian & Student -->
          <h2 class="section-title">Student & Guardian Profile</h2>
          <div class="form-grid">
            <div class="form-field">
              <label>Guardian First Name <span>*</span></label>
              <input type="text" name="guardian_first" required placeholder="Juan" oninput="this.value=this.value.replace(/[0-9]/g,'')">
            </div>
            <div class="form-field">
              <label>Guardian Last Name <span>*</span></label>
              <input type="text" name="guardian_last" required placeholder="Dela Cruz" oninput="this.value=this.value.replace(/[0-9]/g,'')">
            </div>
          </div>
          <div class="form-grid">
            <div class="form-field">
              <label>Student First Name <span>*</span></label>
              <input type="text" name="student_first" required placeholder="Maria" oninput="this.value=this.value.replace(/[0-9]/g,'')">
            </div>
            <div class="form-field">
              <label>Student Last Name <span>*</span></label>
              <input type="text" name="student_last" required placeholder="Dela Cruz" oninput="this.value=this.value.replace(/[0-9]/g,'')">
            </div>
            <div class="form-field">
              <label>Student Age (4 - 18 yrs) <span>*</span></label>
              <input type="number" name="student_age" min="4" max="18" required placeholder="e.g. 10">
            </div>
          </div>

          <!-- Contact -->
          <h2 class="section-title">Contact Information</h2>
          <div class="form-grid">
            <div class="form-field">
              <label>Email Address <span>*</span></label>
              <input type="email" name="email" required placeholder="juan@email.com">
            </div>
            <div class="form-field">
              <label>Phone Number <span>*</span></label>
              <input type="text" name="phone" required maxlength="11" placeholder="09XXXXXXXXX" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>
          </div>

          <!-- Health Pre-Assessment & Clearance -->
          <h2 class="section-title"><i class="fas fa-heartbeat" style="color: var(--red);"></i> Health Pre-Assessment & Clearance</h2>
          <div class="form-grid">
            <div class="form-field" style="grid-column: 1 / -1;">
              <label>Medical Certificate Upload (PDF, JPG, PNG)</label>
              <input type="file" name="medical_certificate" accept=".pdf,.jpg,.jpeg,.png">
              <small class="note" style="margin: 4px 0 0;">Upload doctor's medical clearance if student has prior health conditions.</small>
            </div>
            <div class="form-field" style="grid-column: 1 / -1;">
              <label>Pre-Assessment Health Notes / Allergies / Physical Conditions</label>
              <textarea name="pre_assessment_notes" rows="3" placeholder="Please list any medical conditions, asthma, allergies, or physical considerations for training..."></textarea>
            </div>
          </div>

          <!-- Address -->
          <h2 class="section-title">Current Address</h2>
          <div class="form-grid">
            <div class="form-field" style="grid-column: 1 / -1;">
              <label>Street Address <span>*</span></label>
              <input type="text" name="street" required placeholder="123 Karate St., Barangay X">
            </div>
          </div>
          <div class="form-grid">
            <div class="form-field">
              <label>City <span>*</span></label>
              <input type="text" name="city" required placeholder="Pasig / Marikina / Antipolo">
            </div>
            <div class="form-field">
              <label>Province <span>*</span></label>
              <select name="province" required>
                <option value="" disabled selected>Select Province</option>
                <option selected>National Capital Region (NCR)</option>
                <option>Rizal</option>
                <option>Bulacan</option>
                <option>Cavite</option>
                <option>Laguna</option>
                <option>Batangas</option>
                <option>Pampanga</option>
                <option>Other</option>
              </select>
            </div>
            <div class="form-field">
              <label>Postal / Zip Code <span>*</span></label>
              <input type="text" name="zip" required maxlength="4" placeholder="1600" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>
          </div>

          <!-- Schedule -->
          <h2 class="section-title">Preferred Trial Schedule</h2>
          <p class="note">Trial classes are available on <strong>Saturdays and Sundays</strong> only.</p>
          <div class="form-grid">
            <div class="form-field">
              <label>Date <span>*</span></label>
              <input type="date" name="trial_date" id="trialDate" required>
            </div>
            <div class="form-field">
              <label>Preferred Time Slot <span>*</span></label>
              <select name="time_slot" required>
                <option value="" disabled selected>Choose a time</option>
                <option>Saturday 10:00 AM</option>
                <option>Saturday 11:30 AM</option>
                <option>Saturday 02:00 PM</option>
                <option>Sunday 10:00 AM</option>
                <option>Sunday 11:30 AM</option>
                <option>Sunday 02:00 PM</option>
              </select>
            </div>
          </div>

          <!-- Terms & Liability Waiver -->
          <div class="form-field" style="margin: 24px 0; padding: 16px; background: #f8f9fa; border-radius: 8px; border: 1px solid var(--border);">
            <label style="display: flex; align-items: flex-start; gap: 10px; font-weight: 500; cursor: pointer;">
              <input type="checkbox" name="liability_waiver" value="1" required style="margin-top: 3px; width: 18px; height: 18px;">
              <span>I hereby certify that the student is physically fit for martial arts training, understand the age entry limits (4-18 yrs), and accept the dojo terms and liability waiver. <span>*</span></span>
            </label>
          </div>

          <button type="submit" class="submit-btn">
            <i class="fas fa-calendar-check"></i> Submit Online Enrollment & Book Trial
          </button>

      </form>
    </div>
  </div> <!-- /.trial-container -->

  <!-- ── CTA BANNER ── -->
  <div class="cta-banner" style="margin-top: 0;">
    <div class="cta-text">
      <h2>Ready to Start Training?</h2>
      <p>First trial class is free — no experience needed. All ages and skill levels welcome.</p>
    </div>
    <div class="cta-actions">
      <a href="contact.php" class="btn-white"><i class="fas fa-envelope"></i> Email Us</a>
    </div>
  </div>
  </div> <!-- /.bg-register -->

  <!-- ── FOOTER ── -->
  <?php require_once __DIR__ . '/layout/footer.php'; ?>

<!-- Toast & Scroll to Top -->
    <div id="cart-toast" class="toast">Thank you for booking! Redirecting…</div>
    <button id="scrollToTop" aria-label="Scroll to top"><i class="fas fa-arrow-up"></i></button>

    <script src="../js/hp.js"></script>
    <script src="../js/appoint.js"></script>
    <script>
    const MAX_SLOTS_PER_TIME = 5;
    
    function refreshSlots() {
        const container = document.getElementById('slots-list');
        const countsDiv = document.getElementById('slot-counts');
        
        if (!container || !countsDiv) return;
        
        fetch('api/booking_slots.php')
            .then(response => response.json())
            .then(data => {
                const slots = data.slots || {};
                container.innerHTML = '';
                
                const sortedSlots = Object.keys(slots).sort();
                
                sortedSlots.forEach(slot => {
                    const count = slots[slot];
                    const remaining = Math.max(0, MAX_SLOTS_PER_TIME - count);
                    const available = remaining > 0;
                    
                    const slotEl = document.createElement('div');
                    slotEl.className = 'slot-item';
                    slotEl.style.cssText = `
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 10px 12px;
                        background: ${available ? '#e8f5e9' : '#ffebee'};
                        border-radius: 8px;
                        border-left: 4px solid ${available ? '#22c55e' : '#ef4444'};
                    `;
                    slotEl.innerHTML = `
                        <span style="font-weight: 500; color: #333;">${slot}</span>
                        <span style="font-size: 0.9rem; color: ${available ? '#16a34a' : '#dc2626'};">
                            ${available ? remaining + ' left' : 'Full'}
                        </span>
                    `;
                    container.appendChild(slotEl);
                });
                
                countsDiv.style.display = 'block';
            })
            .catch(err => {
                console.log('Could not load slot counts');
            });
    }
    
    setInterval(refreshSlots, 30000);
    
    document.addEventListener('DOMContentLoaded', function() {
        refreshSlots();
    });
    </script>
</body>
</html>