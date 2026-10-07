function showToast(message) {
  const toast = document.getElementById("cart-toast");
  if (!toast) return;

  toast.textContent = message;
  toast.style.display = "block";

  setTimeout(() => {
    toast.style.display = "none"
  }, 3000);
}

function submitBooking(form, callback) {
  const formData = new FormData(form);
  const submitBtn = form.querySelector('.submit-btn');
  
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  }
  
  fetch(form.action, {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Book My Free Trial Class';
    }
    
    if (data.includes('success') || data.includes('confirmation') || data.includes('Thank you')) {
      showToast('Booking submitted successfully!');
      if (callback) callback(true);
    } else {
      showToast('Booking submitted! Please check your email for confirmation.');
      if (callback) callback(true);
    }
  })
  .catch(error => {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Book My Free Trial Class';
    }
    showToast('Submission failed. Please try again.');
    if (callback) callback(false);
  });
}

document.querySelector("form")?.addEventListener("submit", function (e) {
  const form = e.target;
  const isAjaxTarget = form.dataset.ajax === 'true';
  
  if (isAjaxTarget) {
    e.preventDefault();
    const redirect = setTimeout(() => {
      window.location.href = "ty2.php";
    }, 1500);
    
    submitBooking(form, (success) => {
      if (success) {
        clearTimeout(redirect);
        window.location.href = "ty2.php";
      }
    });
  }
})
