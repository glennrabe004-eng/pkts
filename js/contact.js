document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('active');
});
const header = document.querySelector('.main-header');
const navContainer = document.querySelector('.main-nav-container');
const scrollThreshold = 100;

window.addEventListener('scroll', () => {
    if (window.scrollY > scrollThreshold) {
        header.classList.add('scrolled');
        navContainer.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
        navContainer.classList.remove('scrolled');
    }
});
const sectionToPin = document.querySelector('.pin-section');
const pinThreshold = 300;

window.addEventListener('scroll', () => {
    if (window.scrollY > pinThreshold) {
        sectionToPin.classList.add('pinned');
    } else {
        sectionToPin.classList.remove('pinned');
    }
});
function openGoogleMaps() {
    const destination = "14.619616499999998,121.0999832";
    
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${destination}`, "_blank");
}
document.addEventListener('DOMContentLoaded', function() {
    const locationBadges = document.querySelectorAll('.location-badge');
    
    locationBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            const text = this.textContent.trim();
            
            if (text === "Robinson Metro East") {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=14.619616499999998,121.0999832`, "_blank");
            } else if (text === "Pasig, Metro Manila") {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=Pasig,Metro+Manila`, "_blank");
            }
        });
    });
});