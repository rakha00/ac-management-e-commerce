@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden md:flex">
            <div class="md:shrink-0">
                <img class="h-full w-full object-cover md:w-64" src="https://via.placeholder.com/400"
                    alt="{{ $product->nama_produk }}">
            </div>
            <div class="p-8">
                <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">{{ $product->kategori }}</div>
                <h1 class="block mt-1 text-lg leading-tight font-medium text-black">{{ $product->nama_produk }}</h1>
                <p class="mt-2 text-gray-600">{{ $product->deskripsi }}</p>
                <div class="mt-4">
                    <h2 class="text-lg font-semibold text-gray-800">Spesifikasi Teknis:</h2>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach ($product->spesifikasi as $key => $value)
                            <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4">
                    <span
                        class="text-2xl font-bold text-blue-600">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                </div>
                <div class="mt-6">
                    <button id="whatsapp-order-button"
                        class="bg-green-500 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-green-600 transition-colors duration-300">
                        Pesan via WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('whatsapp-order-button').addEventListener('click', function () {
            const productName = "{{ $product->nama_produk }}";
            const productPrice = "Rp{{ number_format($product->harga, 0, ',', '.') }}";
            const message = `Halo, saya tertarik dengan produk ${productName} dengan harga ${productPrice}. Bisakah saya mendapatkan informasi lebih lanjut atau melakukan pemesanan?`;
            const whatsappUrl = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`; // Ganti dengan nomor WhatsApp Anda
            window.open(whatsappUrl, '_blank');
        });
    </script>
@endsection