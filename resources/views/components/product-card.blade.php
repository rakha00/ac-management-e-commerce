@props(['product' => null, 'category' => '', 'price' => '', 'image' => '', 'title' => '', 'href' => '#'])

<div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden group flex flex-col">
    <a href="{{ $href }}" class="block overflow-hidden">
        <img src="{{ $image }}" alt="Produk AC"
            class="w-full h-36 md:h-40 object-cover transition-transform duration-300 group-hover:scale-105">
    </a>
    <div class="p-3 flex flex-col flex-grow">
        <span class="text-xs text-gray-500">{{ $category }}</span>
        <h4 class="text-sm font-semibold text-gray-800 hover:text-gsi-red transition-colors my-1 h-10 overflow-hidden">
            <a href="{{ $href }}">{{ $title }}</a>
        </h4>

        <div class="mt-1 flex-grow">
            @if ($price === 'Hubungi untuk Harga')
                <span class="text-base font-semibold text-gray-800">{{ $price }}</span>
            @else
                <span class="text-base font-bold text-gsi-red">{{ $price }}</span>
            @endif
        </div>

        <div class="mt-3">
            <button
                class="w-full px-3 py-2 bg-gsi-red/10 text-gsi-red text-sm font-bold rounded-lg hover:bg-gsi-red hover:text-white focus:outline-none transition-all duration-200">
                + Keranjang
            </button>
        </div>
    </div>
</div>
