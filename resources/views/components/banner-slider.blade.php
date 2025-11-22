@props([
    'banners' => collect([]),
])

@php
    // If no banners from database, use one default banner as fallback
    $defaultBanner = [
        'image' => '/img/banner-slider/1.png',
        'alt' => 'Promo AC Diskon 35%'
    ];
    
    $displayBanners = $banners->isNotEmpty() ? $banners : collect([$defaultBanner]);
@endphp

<section class="mb-6 md:mb-8">
    <div class="swiper rounded-lg overflow-hidden shadow-lg relative banner-slider w-full max-w-7xl mx-auto">
        <div class="swiper-wrapper">
            @foreach($displayBanners as $banner)
                <div class="swiper-slide">
                    @if(is_array($banner))
                        {{-- Default fallback banner --}}
                        <img src="{{ asset($banner['image']) }}" alt="{{ $banner['alt'] }}"
                            class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover">
                    @else
                        {{-- Banner from database --}}
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner {{ $loop->iteration }}"
                            class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover">
                    @endif
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>
