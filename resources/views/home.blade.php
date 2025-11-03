@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="pt-10 bg-linear-to-r from-blue-50 to-indigo-50"></section>
    <div class="container mx-auto px-4 max-w-6xl py-10">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-16">
            <div class="lg:w-1/2 text-center lg:text-left">
                <h1 class="text-3xl md:text-6xl font-extrabold text-gray-800 leading-tight mb-6">
                    Temukan AC & Sparepart <span class="text-blue-800">Terbaik</span> untuk Rumah Anda
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-xl mx-auto lg:mx-0">
                    SejukMart menyediakan AC dan sparepart berkualitas dengan harga bersaing, produk original, dan
                    layanan cepat untuk kenyamanan rumah tangga Anda.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 mb-8">
                    <a href="#"
                        class="bg-blue-800 text-white px-6 py-3 rounded-full text-base font-semibold hover:bg-blue-700 transition-all duration-300 shadow-md hover:shadow-lg">
                        <x-fab-whatsapp class="w-5 h-5 inline-block mr-2" /> Pesan via WhatsApp
                    </a>
                    <a href="#"
                        class="border-2 border-blue-800 text-blue-800 px-6 py-3 rounded-full text-base font-semibold hover:bg-blue-50 transition-all duration-300">
                        Lihat Katalog
                    </a>
                </div>
                <div class="flex flex-wrap justify-center lg:justify-start gap-8 max-w-4xl mx-auto">
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-blue-500 mr-2"></x-heroicon-s-check-circle>
                        <span>Produk 100% Original</span>
                    </div>
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-blue-500 mr-2"></x-heroicon-s-check-circle>
                        <span>Garansi Resmi Pabrik</span>
                    </div>
                    <div class="flex items-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-blue-500 mr-2"></x-heroicon-s-check-circle>
                        <span>Gratis Konsultasi Teknis</span>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2 flex justify-center">
                <img src="{{ asset('img/hero.jpg') }}" alt="AC Unit"
                    class="rounded-xl shadow-xl max-w-full h-auto transform hover:scale-[1.02] transition-transform duration-500">
            </div>
        </div>
    </div>
    </section>

    <!-- Categories Section -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Kategori Produk</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Pilih kategori untuk menemukan produk atau layanan yang Anda
                    butuhkan</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <a href="{{ route('category', ['type' => 'ac-portable']) }}"
                    class="group block p-6 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:shadow-lg">
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4 group-hover:bg-blue-200 transition-colors duration-300">
                            <x-heroicon-s-bolt class="h-10 w-10 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">AC Portable</h3>
                        <p class="text-gray-600 text-sm">Mudah dipindahkan</p>
                        <div class="mt-4 text-blue-600 font-medium group-hover:underline">Lihat Produk</div>
                    </div>
                </a>
                <a href="{{ route('category', ['type' => 'service']) }}"
                    class="group block p-6 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:shadow-lg">
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4 group-hover:bg-blue-200 transition-colors duration-300">
                            <x-heroicon-s-wrench class="h-10 w-10 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">Layanan Servis</h3>
                        <p class="text-gray-600 text-sm">Isi freon & pemasangan</p>
                        <div class="mt-4 text-blue-600 font-medium group-hover:underline">Lihat Produk</div>
                    </div>
                </a>
                <a href="{{ route('category', ['type' => 'ac']) }}"
                    class="group block p-6 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:shadow-lg">
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4 group-hover:bg-blue-200 transition-colors duration-300">
                            <x-heroicon-s-bars-3 class="h-10 w-10 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">AC Split</h3>
                        <p class="text-gray-600 text-sm">Hemat energi dan efisien</p>
                        <div class="mt-4 text-blue-600 font-medium group-hover:underline">Lihat Produk</div>
                    </div>
                </a>
                <a href="{{ route('category', ['type' => 'sparepart']) }}"
                    class="group block p-6 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:shadow-lg">
                    <div class="text-center">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4 group-hover:bg-blue-200 transition-colors duration-300">
                            <x-heroicon-s-cog class="h-10 w-10 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">Sparepart AC</h3>
                        <p class="text-gray-600 text-sm">Kompresor, freon & lainnya</p>
                        <div class="mt-4 text-blue-600 font-medium group-hover:underline">Lihat Produk</div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Popular Products Section -->
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Produk Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Pilihan terbaik dari ribuan pelanggan Kami</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse ($unitACs as $ac)
                    <div
                        class="group bg-white/30 backdrop-blur-md border border-gray-200 rounded-lg shadow-lg p-4 flex flex-col h-[450px]">
                        <div class="relative mb-5 overflow-hidden rounded-md h-64">
                            <div class="slider-container h-full">
                                @foreach ($ac->path_foto_produk as $path)
                                    <img src="{{ asset('storage/' . $path) }}" alt="{{ $ac->nama_unit }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 slider-image">
                                @endforeach
                                <button
                                    class="slider-button prev-button absolute top-1/2 left-2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full focus:outline-none">
                                    &#10094;
                                </button>
                                <button
                                    class="slider-button next-button absolute top-1/2 right-2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full focus:outline-none">
                                    &#10095;
                                </button>
                            </div>
                            <div
                                class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                                Stok Tersisa: {{ $ac->stok_akhir }}
                            </div>
                        </div>
                        <div class="flex flex-col justify-between grow">
                            <h3 class="font-semibold text-xl text-gray-800 mb-2">{{ Str::limit($ac->nama_unit, 50) }}</h3>
                            <div class="text-gray-600 mb-4 h-12 overflow-hidden">
                                <p>{{ Str::limit($ac->keterangan, 100) }}</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-2xl font-bold text-blue-700">Rp{{ number_format($ac->harga_ecommerce, 0, ',', '.') }}</span>
                                <a href="#"
                                    class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center">
                                    <x-fab-whatsapp class="w-8 h-8" />
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 text-center col-span-full">Tidak ada unit AC yang tersedia saat ini.</p>
                @endforelse
                @forelse ($spareparts as $sparepart)
                    <div
                        class="group bg-white/30 backdrop-blur-md border border-gray-200 rounded-lg shadow-lg p-4 flex flex-col h-[450px]">
                        @foreach ($sparepart->path_foto_sparepart as $path)
                            <img src="{{ asset('storage/' . $path) }}" alt="{{ $sparepart->nama_sparepart }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 slider-image">
                        @endforeach
                        <div class="flex flex-col justify-between grow">
                            <h3 class="font-semibold text-xl text-gray-800 mb-2">
                                {{ Str::limit($sparepart->nama_sparepart, 50) }}
                            </h3>
                            <div class="text-gray-600 mb-4 h-12 overflow-hidden">
                                <p>{{ Str::limit($sparepart->keterangan, 100) }}</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-2xl font-bold text-blue-700">Rp{{ number_format($sparepart->harga_ecommerce, 0, ',', '.') }}</span>
                                <a href="#"
                                    class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center">
                                    <x-fab-whatsapp class="w-8 h-8" />
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 text-center col-span-full">Tidak ada sparepart yang tersedia saat ini.</p>
                @endforelse
            </div>
            <div class="text-center mt-12">
                <a href="#"
                    class="inline-block bg-blue-700 text-white font-bold py-4 px-10 rounded-full hover:bg-blue-800 transition-all duration-300 shadow-md hover:shadow-lg">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>

    <!-- Benefit Section -->
    <section class="bg-linear-to-b from-white to-blue-50"></section>
    <div class="container mx-auto px-4 max-w-6xl py-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">Mengapa Memilih Kami?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Keunggulan layanan yang kami tawarkan</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="text-center group">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-blue-600 bg-opacity-10 rounded-full group-hover:bg-blue-600 group-hover:bg-opacity-20 transition-all duration-300">
                    <x-heroicon-o-tag class="w-10 h-10 text-white" />
                </div>
                <h3 class="font-semibold text-xl text-gray-800 mb-3">Harga Bersaing</h3>
                <p class="text-gray-600">Harga terbaik di pasaran dengan kualitas terjamin</p>
            </div>
            <div class="text-center group">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-blue-600 bg-opacity-10 rounded-full group-hover:bg-blue-600 group-hover:bg-opacity-20 transition-all duration-300">
                    <x-heroicon-o-shield-check class="w-10 h-10 text-white" />
                </div>
                <h3 class="font-semibold text-xl text-gray-800 mb-3">Produk Original</h3>
                <p class="text-gray-600">Semua produk 100% original dengan garansi resmi</p>
            </div>
            <div class="text-center group">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-blue-600 bg-opacity-10 rounded-full group-hover:bg-blue-600 group-hover:bg-opacity-20 transition-all duration-300">
                    <x-heroicon-o-truck class="w-10 h-10 text-white" />
                </div>
                <h3 class="font-semibold text-xl text-gray-800 mb-3">Layanan Cepat</h3>
                <p class="text-gray-600">Pengiriman kilat dan layanan servis responsif</p>
            </div>
            <div class="text-center group">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-blue-600 bg-opacity-10 rounded-full group-hover:bg-blue-600 group-hover:bg-opacity-20 transition-all duration-300">
                    <x-heroicon-o-phone class="w-10 h-10 text-white" />
                </div>
                <h3 class="font-semibold text-xl text-gray-800 mb-3">Support 24/7</h3>
                <p class="text-gray-600">Tim support siap membantu via WhatsApp kapan saja</p>
            </div>
        </div>
    </div>
    </section>

    <!-- Promo Section -->
    <section class="py-15 bg-blue-700 text-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="lg:w-1/2 mb-10 lg:mb-0">
                    <h2 class="text-3xl font-bold mb-6">Penawaran Spesial Bulan Ini</h2>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <x-heroicon-o-check class="w-6 h-6 text-white" />
                            </div>
                            <p class="text-lg">Diskon hingga 25% untuk semua tipe AC</p>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <x-heroicon-o-check class="w-6 h-6 text-white" />
                            </div>
                            <p class="text-lg">Gratis biaya pengiriman</p>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <x-heroicon-o-check class="w-6 h-6 text-white" />
                            </div>
                            <p class="text-lg">Gratis instalasi untuk pembelian AC baru</p>
                        </div>
                    </div>
                    <a href="#"
                        class="inline-block bg-white text-blue-700 font-bold py-4 px-10 rounded-full hover:bg-blue-50 transition-all duration-300 shadow-md hover:shadow-lg">
                        Lihat Penawaran
                    </a>
                </div>
                <div class="lg:w-1/2 flex justify-center">
                    <div class="relative">
                        <div
                            class="absolute -top-6 -right-6 bg-yellow-400 text-blue-900 font-bold text-xl px-6 py-3 rounded-full transform rotate-12">
                            25% OFF
                        </div>
                        <img src="{{ asset('img/promo.jpg') }}" alt="Promo AC" class="rounded-lg shadow-xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    </div>
@endsection