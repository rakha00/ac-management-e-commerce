@extends('layouts.app')

@section('title', 'Global Servis Int. - Pusat Dealer & Servis AC')

@section('content')
    <div class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8">

        <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-6xl mx-auto">

            <div class="mb-6 md:mb-8 pb-4 border-b border-gray-200">
                <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900">
                    AC DAIKIN STANDART THAILAND 1 PK FTC25NV14 + RC25NV14
                </h1>
                <span class="text-sm text-gray-500 mt-1">Kategori: <a href="#" class="text-gsi-red hover:underline">AC
                        Split</a></span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">

                <div x-data="{
                    images: [
                        { main: 'https://placehold.co/600x450/e0e0e0/969696?text=AC+Indoor', thumb: 'https://placehold.co/100x100/e0e0e0/969696?text=Indoor' },
                        { main: 'https://placehold.co/600x450/cccccc/969696?text=AC+Outdoor', thumb: 'https://placehold.co/100x100/cccccc/969696?text=Outdoor' },
                        { main: 'https://placehold.co/600x450/e0e0e0/969696?text=Remote', thumb: 'https://placehold.co/100x100/e0e0e0/969696?text=Remote' },
                        { main: 'https://placehold.co/600x450/cccccc/969696?text=Ruang+Tamu', thumb: 'https://placehold.co/100x100/cccccc/969696?text=Suasana' },
                        { main: 'https://placehold.co/600x450/e0e0e0/969696?text=Fitur', thumb: 'https://placehold.co/100x100/e0e0e0/969696?text=Fitur' }
                    ],
                    activeImageSrc: 'https://placehold.co/600x450/e0e0e0/969696?text=AC+Indoor'
                }">
                    <div class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
                        <img x-bind:src="activeImageSrc" alt="Produk Utama"
                            class="w-full h-auto object-cover aspect-square transition-all duration-300">
                    </div>

                    <div class="grid grid-cols-5 gap-2 md:gap-3">
                        <template x-for="image in images" :key="image.thumb">
                            <div @click="activeImageSrc = image.main"
                                class="rounded-md cursor-pointer border-2 transition-all duration-200 aspect-square overflow-hidden"
                                :class="{
                                    'border-gsi-red shadow-md': activeImageSrc === image.main,
                                    'border-transparent hover:border-gray-300': activeImageSrc !== image.main
                                }">
                                <img x-bind:src="image.thumb" alt="Thumbnail Produk" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <div class="mb-5">
                        <span class="text-4xl font-bold text-gsi-red">
                            Rp 4.489.000
                        </span>
                    </div>

                    <div class="space-y-3 mb-8">
                        <button
                            class="w-full px-6 py-4 bg-gsi-red text-white text-base font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors flex items-center justify-center">
                            <x-heroicon-s-shopping-cart class="w-5 h-5 mr-2.5" />
                            Tambah ke Keranjang
                        </button>
                        <a href="https://wa.me/628123456789" target="_blank"
                            class="w-full px-6 py-4 bg-green-500 text-white text-base font-semibold rounded-lg shadow-md hover:bg-green-600 transition-colors flex items-center justify-center">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-2.5" />
                            Order Cepat via WhatsApp
                        </a>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            Spesifikasi
                        </h3>

                        <div class="space-y-2">
                            <div class="flex items-center p-3 bg-gray-50 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">SKU Produk</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">1</span>
                            </div>
                            <div class="flex items-center p-3 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Made In</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">MADE IN THAILAND</span>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Merek</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">Daikin</span>
                            </div>
                            <div class="flex items-center p-3 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Tipe AC</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">Split</span>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Daya Watt</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">220</span>
                            </div>
                            <div class="flex items-center p-3 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">BTU</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">9000</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="mt-12 md:mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                    Deskripsi Produk
                </h2>

                <div class_alias="text-gray-700 leading-relaxed space-y-4">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip ex ea commodo consequat.
                    </p>
                    <p>
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
                        pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit
                        anim id est laborum.
                    </p>
                    <h4 class="text-lg font-semibold text-gray-800 pt-2">Fitur Utama:</h4>
                    <ul class="list-disc list-inside space-y-2 pl-4">
                        <li><strong>Mode Econo:</strong> Mengurangi konsumsi daya listrik agar hemat energi.</li>
                        <li><strong>Filter Gin-Ion:</strong> Menghilangkan partikel debu dan bau tak sedap.</li>
                        <li><strong>Blue Fin Kondensor:</strong> Sirip kondensor anti karat untuk durabilitas lebih lama.
                        </li>
                        <li><strong>Refrigeran R32:</strong> Ramah lingkungan dan lebih cepat mendinginkan.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
