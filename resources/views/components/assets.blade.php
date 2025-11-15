<!-- Styles -->
@livewireStyles
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

<!-- Swiper.js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- TailwindCSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'gsi-red': '#D11A2A',
                    'gsi-green': '#008000',
                },
            },
        },
    };
</script>

<!-- Swiper Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSliders = document.querySelectorAll('.banner-slider');

        bannerSliders.forEach(function(sliderEl) {
            if (typeof Swiper !== 'undefined' && !sliderEl.swiperInstance) {
                sliderEl.swiperInstance = new Swiper(sliderEl, {
                    loop: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: sliderEl.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    navigation: {
                        nextEl: sliderEl.querySelector('.swiper-button-next'),
                        prevEl: sliderEl.querySelector('.swiper-button-prev'),
                    },
                    effect: 'slide',
                    speed: 500,
                });
            }
        });
    });
</script>

<!-- Custom Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@600;700&display=swap');

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #F4F7F6;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
    }

    /* Swiper buttons */
    .swiper-button-next,
    .swiper-button-prev {
        color: #fff;
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 1.25rem;
        font-weight: 900;
    }

    /* Range slider thumb */
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        pointer-events: all;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid #D11A2A;
        cursor: pointer;
        position: relative;
        z-index: 10;
    }

    input[type=range]::-moz-range-thumb {
        pointer-events: all;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid #D11A2A;
        cursor: pointer;
        position: relative;
        z-index: 10;
    }
</style>
