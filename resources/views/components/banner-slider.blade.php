@props([
    'banners' => [
        ['image' => '/img/banner-slider/1.png', 'title' => 'Promo AC Diskon 35%'],
        ['image' => '/img/banner-slider/2.png', 'title' => 'Gratis Instalasi Setiap Pembelian Unit Baru'],
        ['image' => '/img/banner-slider/3.png', 'title' => 'Servis Rutin Hanya Rp 65.000'],
    ],
])

<section class="mb-6 md:mb-8">
    <div class="swiper rounded-lg overflow-hidden shadow-lg relative banner-slider w-full max-w-7xl mx-auto">
        <div class="swiper-wrapper">
            @forelse($banners as $banner)
                <div class="swiper-slide">
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}"
                        class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover">
                </div>
            @empty
                <div class="swiper-slide">
                    <img src="https://placehold.co/1000x400" alt="No banners available"
                        class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover">
                </div>
            @endforelse
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>
