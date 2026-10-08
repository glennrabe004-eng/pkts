const productCarousels = document.querySelectorAll('.product .carousel-container');

        productCarousels.forEach(carouselContainer => {
            const slides = carouselContainer.querySelectorAll('.carousel-slide');
            const indicators = carouselContainer.querySelectorAll('.carousel-indicator');
            let currentSlide = 0;
            let intervalId;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    indicators.forEach((indicator, j) => {
                        indicator.classList.remove('active');
                    });
                    if (i === index) {
                        slide.classList.add('active');
                        indicators[i].classList.add('active');
                    }
                });
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            function startCarousel() {
                intervalId = setInterval(nextSlide, 3000);
            }

            function stopCarousel() {
                clearInterval(intervalId);
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    stopCarousel();
                    currentSlide = index;
                    showSlide(currentSlide);
                    startCarousel();
                });
            });

            startCarousel();

           const carouselContainerElement = carouselContainer;
            carouselContainerElement.addEventListener('mouseenter', stopCarousel);
            carouselContainerElement.addEventListener('mouseleave', startCarousel);
        });