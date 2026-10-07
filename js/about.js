/**
 * about.js — PKTS About Page
 * hp.js handles: body fade-in, nav, scroll-to-top, cart badge.
 * This file only handles the scroll-triggered fade-in animations.
 */

document.addEventListener('DOMContentLoaded', () => {

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.about-block, .offer-card').forEach(el => {
    observer.observe(el);
  });

});