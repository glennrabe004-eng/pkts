document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.add('active'); // fade the page in

    const header = document.querySelector('.main-header');
    const scrollBtn = document.getElementById('scrollToTop');
    const navLinks = document.querySelectorAll('nav.main-nav a');
    const contentWrapper = document.querySelector('.content-wrapper');

    /* ---------- Header shrink + scroll-to-top visibility ---------- */
    function onScroll() {
        const y = window.scrollY;
        if (header) header.classList.toggle('scrolled', y > 50);
        if (scrollBtn) scrollBtn.classList.toggle('show', y > 300);
    }
    window.addEventListener('scroll', onScroll);
    onScroll(); // handle pages that load already scrolled

    if (scrollBtn) {
        scrollBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------- Highlight the current page in the nav ---------- */
    const currentPage = (location.pathname.split('/').pop() || 'homepage.html').toLowerCase();
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href === '#' || href.includes('?')) return;
        if (href.split('#')[0].toLowerCase() === currentPage) {
            link.classList.add('active');
        }
    });

    /* ---------- Fade-out transition (only if the page has .content-wrapper) ---------- */
    if (contentWrapper) {
        navLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                const href = this.getAttribute('href');
                if (!href || href === '#' || this.target === '_blank') return;
                if (event.ctrlKey || event.metaKey || event.shiftKey) return;

                event.preventDefault();
                contentWrapper.classList.add('fade-out');
                setTimeout(() => { window.location.href = href; }, 300);
            });
        });

        // Undo the fade-out if the user comes back with the browser's back button
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) contentWrapper.classList.remove('fade-out');
        });
    }
});