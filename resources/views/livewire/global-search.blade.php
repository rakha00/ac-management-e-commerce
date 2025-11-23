<div class="relative">
    <form action="/produk" method="GET" class="relative group">
        <input type="text" name="q" wire:model.live.debounce.500ms="query"
            placeholder="Cari AC Daikin, Servis, Spare Part..."
            class="w-full pl-5 pr-28 py-3 text-sm text-gray-700 bg-gray-100 border border-gray-200 rounded-lg
                   focus:outline-none focus:ring-2 focus:ring-gsi-red/50 focus:border-gsi-red transition-all">
        <button type="submit"
            class="absolute right-0 top-0 h-full px-5 py-3 bg-gsi-red text-white rounded-r-lg
                   hover:bg-red-700 font-semibold text-sm">
            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
        </button>
    </form>

    <!-- Search Results Dropdown -->
    @if ($showResults && count($results) > 0)
        <div
            class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
            @foreach ($results as $product)
                <a href="#" wire:click.prevent="selectProduct({{ $product->id }})"
                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @php
                                $image = asset('img/produk/placeholder.png');
                                if (!empty($product->path_foto_produk) && is_array($product->path_foto_produk) && count($product->path_foto_produk) > 0) {
                                    $image = asset('storage/' . $product->path_foto_produk[0]);
                                }
                            @endphp
                            <img src="{{ $image }}"
                                alt="{{ $product->nama_unit }}" class="w-10 h-10 object-cover rounded">
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">
                                {{ $product->nama_unit }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @php
                                    $merk = $product->merk->merk ?? null;
                                    $tipe = $product->tipeAC->tipe_ac ?? null;
                                    $info = array_filter([$merk, $tipe]); // Remove null values
                                @endphp
                                @if(count($info) > 0)
                                    {{ implode(' • ', $info) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm font-semibold text-gsi-red">
                            Rp {{ number_format($product->harga_ecommerce, 0, ',', '.') }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @elseif($showResults && count($results) === 0 && strlen($query) > 0)
        <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
            <div class="px-4 py-3 text-sm text-gray-500 text-center">
                Tidak ada produk ditemukan untuk "{{ $query }}"
            </div>
        </div>
    @endif
</div>
