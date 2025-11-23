@extends('layouts.app')

@section('title', 'Global Servis Int. - Pusat Dealer & Servis AC')

@section('content')
    <div class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8">

        <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg max-w-6xl mx-auto">

            <div class="mb-6 md:mb-8 pb-4 border-b border-gray-200">
                <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900">
                    {{ $product->nama_sparepart }}
                </h1>
                <span class="text-sm text-gray-500 mt-1">Kategori: <a href="/produk?category=sparepart" class="text-gsi-red hover:underline">Sparepart</a></span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">

                @php
                    $productImages = [];
                    if (!empty($product->path_foto_sparepart) && is_array($product->path_foto_sparepart)) {
                        foreach ($product->path_foto_sparepart as $foto) {
                            $productImages[] = [
                                'main' => asset('storage/' . $foto),
                                'thumb' => asset('storage/' . $foto),
                            ];
                        }
                    }
                    
                    if (empty($productImages)) {
                        $productImages[] = [
                            'main' => asset('img/produk/placeholder.png'),
                            'thumb' => asset('img/produk/placeholder.png'),
                        ];
                    }
                @endphp
                <div x-data="{
                    images: {{ json_encode($productImages) }},
                    activeImageSrc: '{{ $productImages[0]['main'] }}'
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
                            Rp {{ number_format($product->harga_ecommerce, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-8">
                        <button
                            x-data="{ loading: false }"
                            @click="loading = true; Livewire.dispatch('add-to-cart', { productId: {{ $product->id }}, type: 'sparepart' }); setTimeout(() => loading = false, 1000)"
                            :disabled="loading"
                            :class="{ 'opacity-75 cursor-not-allowed': loading }"
                            class="w-full px-6 py-4 bg-gsi-red text-white text-base font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors flex items-center justify-center">
                            <span x-show="!loading" class="flex items-center">
                                <x-heroicon-s-shopping-cart class="w-5 h-5 mr-2.5" />
                                Tambah ke Keranjang
                            </span>
                            <span x-show="loading" class="flex items-center" style="display: none;">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menambahkan...
                            </span>
                        </button>
                        @php
                            $message = "Halo Global Servis Int., saya tertarik dengan sparepart {$product->nama_sparepart}. Mohon info lebih lanjut.";
                            $encodedMessage = urlencode($message);
                            $whatsappUrl = "https://wa.me/6285695643257?text={$encodedMessage}";
                        @endphp
                        <a href="{{ $whatsappUrl }}" target="_blank"
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
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Kode Sparepart</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">{{ $product->kode_sparepart }}</span>
                            </div>
                            @if($product->merkSparepart)
                            <div class="flex items-center p-3 bg-gray-50 rounded">
                                <span class="w-1/3 text-sm font-semibold text-gray-600">Merek</span>
                                <span class="w-2/3 text-sm text-gray-800 font-medium">{{ $product->merkSparepart->merk_spareparts }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="mt-12 md:mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                    Deskripsi Produk
                </h2>

                <div class="text-gray-700 leading-relaxed space-y-4">
                    @if ($product->keterangan && trim(strip_tags($product->keterangan)) !== '')
                        {!! $product->keterangan !!}
                    @else
                        <p>Deskripsi produk belum tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
