@extends('layouts.app')

@section('title', 'Global Servis Int. - Pusat Dealer & Servis AC')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle smooth scrolling for anchor links
            const links = document.querySelectorAll('a[href^="#"]');

            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const headerHeight = document.querySelector('header').offsetHeight;
                        const offsetTop = targetElement.offsetTop - headerHeight -
                            20; // 20px extra padding

                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="container mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="flex flex-col md:flex-row gap-6 md:gap-8">

            <aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0">
                <div class="bg-white p-4 rounded-lg shadow-sm" x-data="{ open: true }">
                    <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">Kategori Produk</h3>
                        <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                            x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <ul x-show="open" x-collapse class="space-y-2">
                        @foreach ($sections as $section)
                            <li><a href="#{{ $section['slug'] }}"
                                    class="block text-sm text-gray-600 hover:text-gsi-red hover:bg-gray-50 p-2 rounded">{{ $section['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="hidden md:block">
                    <x-pesan-servis />
                </div>
            </aside>
            <main class="w-full md:w-3/4 lg:w-4/5">

                <!-- Banner Slider -->
                <x-banner-slider :banners="$banners" />

                <!-- Features -->
                <x-features />

                <!-- Produk Terlaris -->
                <section class="mb-6 md:mb-8 bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold text-gray-900">Produk Terlaris</h2>
                        <a href="{{ \App\Helpers\PriceHelper::url('/produk') }}" class="text-sm font-semibold text-gsi-red hover:underline">Lihat
                            Semua</a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4">
                        @foreach ($popularProducts as $product)
                            <x-product-card :id="$product->id" category="{{ $product->tipeAC->tipe_ac ?? 'AC' }}"
                                title="{{ $product->nama_unit }}"
                                price="Rp {{ number_format($product->display_price, 0, ',', '.') }}"
                                image="{{ !empty($product->path_foto_produk) && is_array($product->path_foto_produk) && count($product->path_foto_produk) > 0 ? asset('storage/' . $product->path_foto_produk[0]) : asset('img/produk/placeholder.png') }}"
                                href="{{ \App\Helpers\PriceHelper::url('/produk/' . $product->id) }}" />
                        @endforeach
                    </div>
                </section>

                <!-- Banner Servis -->
                <a href="{{ \App\Helpers\PriceHelper::url('/servis') }}" class="block mb-6 md:mb-8 rounded-lg overflow-hidden shadow-lg group">
                    <img src="/img/banner-slider/servis.png" alt="Banner Servis"
                        class="w-full h-40 md:h-70 lg:h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                </a>

                <!-- Product Sections -->
                @foreach ($sections as $section)
                    <x-product-section categoryId="{{ $section['categoryId'] }}" title="{{ $section['title'] }}"
                        category="{{ $section['title'] }}" categorySlug="{{ $section['slug'] }}" :products="$section['products']" />

                    @if (!$loop->last)
                        <div class="mb-6 md:mb-8"></div>
                    @endif
                @endforeach

                <!-- Pesan Servis untuk Mobile -->
                <div class="block md:hidden mb-8">
                    <x-pesan-servis />
                </div>

            </main>
        </div>
    </div>
@endsection
