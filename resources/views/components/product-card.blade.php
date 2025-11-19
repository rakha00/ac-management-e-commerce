@props(['id' => null, 'type' => 'unit', 'product' => null, 'category' => '', 'price' => '', 'image' => '', 'title' => '', 'href' => '#'])

<div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden group flex flex-col h-full">
    <a href="{{ $href }}" class="block overflow-hidden relative">
        <img src="{{ $image }}" alt="Produk AC"
            class="w-full h-32 md:h-44 object-cover transition-transform duration-300 group-hover:scale-105">
    </a>
    <div class="p-2 md:p-3 flex flex-col flex-grow">
        <span class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wider">{{ $category }}</span>
        <h4 class="text-xs md:text-sm font-semibold text-gray-800 hover:text-gsi-red transition-colors my-1 line-clamp-2 min-h-[2.5em] leading-tight">
            <a href="{{ $href }}">{{ $title }}</a>
        </h4>

        <div class="mt-1 flex-grow">
            @if ($price === 'Hubungi untuk Harga')
                <span class="text-xs md:text-base font-semibold text-gray-800">{{ $price }}</span>
            @else
                <span class="text-sm md:text-base font-bold text-gsi-red">{{ $price }}</span>
            @endif
        </div>

        <div class="mt-2 md:mt-3" x-data="{ loading: false }">
            <button
                @if($id) 
                    @click="loading = true; Livewire.dispatch('add-to-cart', { productId: {{ $id }}, type: '{{ $type }}' }); setTimeout(() => loading = false, 1000)" 
                    :disabled="loading"
                @endif
                :class="{ 'opacity-75 cursor-not-allowed': loading }"
                class="w-full px-2 py-1.5 md:px-3 md:py-2 bg-gsi-red/10 text-gsi-red text-xs md:text-sm font-bold rounded-lg hover:bg-gsi-red hover:text-white focus:outline-none transition-all duration-200 flex justify-center items-center">
                <span x-show="!loading">+ Keranjang</span>
                <span x-show="loading" class="flex items-center" style="display: none;">
                    <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3 md:h-4 md:w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="hidden md:inline">Menambahkan...</span>
                    <span class="md:hidden">...</span>
                </span>
            </button>
        </div>
    </div>
</div>
