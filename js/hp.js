document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.add('active');

    const header = document.querySelector('.main-header');
    const hero = document.querySelector('.hero');
    const body = document.body;
    const navLinks = document.querySelectorAll('nav.main-nav a');
    const contentWrapper = document.querySelector('.content-wrapper');

    const scrollThreshold = 50;

    window.addEventListener('scroll', () => {
        if (window.scrollY > scrollThreshold) {
            header.classList.add('scrolled');
            body.style.paddingTop = `${header.offsetHeight}px`;
            hero.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
            body.style.paddingTop = 0;
            hero.classList.remove('scrolled');
        }
    });

    navLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            navLinks.forEach(navLink => navLink.classList.remove('active'));
            this.classList.add('active');

            if (contentWrapper) {
                contentWrapper.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = this.getAttribute('href');
                }, 300);
            } else {
                window.location.href = this.getAttribute('href');
            }
        });
    });

    const homeLink = document.querySelector('nav.main-nav a[href="Homepage.html"]');
    if (homeLink) {
        homeLink.classList.add('active');
    }
});