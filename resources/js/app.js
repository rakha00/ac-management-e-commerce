import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    function initializeSlider(sliderContainer) {
        const images = sliderContainer.querySelectorAll('.slider-image');
        const prevButton = sliderContainer.querySelector('.prev-button');
        const nextButton = sliderContainer.querySelector('.next-button');
        let currentIndex = 0;

        function showImage(index) {
            images.forEach((img, i) => {
                if (i === index) {
                    img.style.opacity = 1;
                    img.classList.add('active');
                } else {
                    img.style.opacity = 0;
                    img.classList.remove('active');
                }
            });
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        }

        function prevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        }

        prevButton.addEventListener('click', prevImage);
        nextButton.addEventListener('click', nextImage);

        showImage(currentIndex);

        // Auto-advance slider every 3 seconds
        // Auto-slide functionality (disabled)
        // setInterval(nextImage, 3000);
    }

    document.querySelectorAll('.slider-container').forEach(initializeSlider);
});