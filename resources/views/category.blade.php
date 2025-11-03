@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">Kategori: {{ ucfirst($type) }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <img src="https://via.placeholder.com/300" alt="{{ $product->nama_produk }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-semibold text-lg text-gray-800">{{ $product->nama_produk }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ Str::limit($product->deskripsi, 100) }}</p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-xl font-bold text-blue-600">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                        <a href="{{ route('product_detail', ['id' => $product->id]) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600 text-center col-span-full">Tidak ada produk dalam kategori ini.</p>
        @endforelse
    </div>
</div>
@endsection