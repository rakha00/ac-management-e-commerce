<div class="container mx-auto" x-data="{ filtersOpen: false }">

    {{-- Mobile Filter Button --}}
    <div class="container mx-auto px-4 md:px-6 md:hidden sticky top-[64px] z-30 bg-transparent backdrop-blur-sm py-3">
        <button @click="filtersOpen = true"
            class="w-full flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-700 font-semibold">
            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 mr-2" />
            Tampilkan Filter
        </button>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-6 md:py-8 flex flex-col md:flex-row gap-6 md:gap-8">

        {{-- Filter --}}
        <x-filter-products :types="$types" :brands="$brands" :category="$category" :minPrice="$minPrice" :maxPrice="$maxPrice" :priceLimitMin="$priceLimitMin"
            :priceLimitMax="$priceLimitMax" :tempMinPrice="$tempMinPrice" :tempMaxPrice="$tempMaxPrice" />

        {{-- Content --}}
        <main class="w-full md:w-3/4 lg:w-4/5">

            {{-- Header Content --}}
            <div
                class="bg-white p-4 rounded-lg shadow-sm mb-6 flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Title --}}
                <div class="w-full md:w-auto text-center md:text-left">
                    <h1 class="text-2xl font-semibold text-gray-900">Semua Produk</h1>
                    <p class="text-sm text-gray-500">
                        Menampilkan {{ $products->count() }} produk
                        @if ($products->total() > 0)
                            dari {{ $products->total() }} total
                        @endif
                    </p>
                </div>

                {{-- Search Bar --}}
                <div class="w-full md:flex-1 md:mx-8">
                    <div class="relative group flex items-center">
                        <input type="text" wire:model.defer="query" wire:keydown.enter.prevent="doSearch"
                            placeholder="Cari nama produk, tipe, atau SKU..."
                            class="block w-full pl-10 pr-12 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-gsi-red/20 focus:border-gsi-red sm:text-sm transition duration-150 ease-in-out">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass
                                class="h-5 w-5 text-gray-400 group-focus-within:text-gsi-red transition-colors" />
                        </div>
                        <button type="button" wire:click="doSearch" wire:loading.attr="disabled"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-gsi-red text-white rounded-md hover:bg-red-700 transition-colors flex items-center justify-center disabled:opacity-70">
                            <span wire:loading.remove target="doSearch">
                                <x-heroicon-s-arrow-right class="w-4 h-4" />
                            </span>
                            <span wire:loading target="doSearch">
                                <x-heroicon-o-arrow-path class="animate-spin h-4 w-4 text-white" />
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Sort Dropdown --}}
                <div class="w-full md:w-auto">
                    <label for="sort_by" class="sr-only">Urutkan</label>
                    <select id="sort_by" wire:model.live="sortBy"
                        class="w-full md:w-48 text-sm border-gray-200 px-3 py-2.5 rounded-lg shadow-sm focus:ring-gsi-red focus:border-gsi-red bg-white cursor-pointer">
                        <option value="default">Urutkan: Populer</option>
                        <option value="price_asc">Harga: Terendah - Tertinggi</option>
                        <option value="price_desc">Harga: Tertinggi - Terendah</option>
                        <option value="newest">Terbaru</option>
                        <option value="name_asc">Nama: A-Z</option>
                        <option value="name_desc">Nama: Z-A</option>
                    </select>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-6">
                @forelse($products as $product)
                    @php
                        $image = $product->image_path;
                        if (is_string($image)) {
                            $decoded = json_decode($image, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $image = $decoded[0] ?? null;
                            }
                        } elseif (is_array($image)) {
                            $image = $image[0] ?? null;
                        }
                        
                        $image = $image ? asset('storage/' . $image) : asset('img/GSI.png');
                        
                        $href = $product->type === 'sparepart' 
                            ? route('detail-sparepart', $product->id) 
                            : route('detail-products', $product->id);
                    @endphp
                    <x-product-card 
                        :id="$product->id" 
                        :type="$product->type"
                        category="{{ $product->category }}" 
                        title="{{ $product->name }}"
                        price="{{ $this->formatPrice($product->price) }}"
                        image="{{ $image }}"
                        href="{{ $href }}" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada produk</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Tidak ada produk yang sesuai dengan filter yang dipilih.
                        </p>
                        <div class="mt-6">
                            <button wire:click="resetFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gsi-red hover:bg-red-700">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination - Floating at Bottom --}}
            @if($products->hasPages())
            <div class="sticky bottom-0 left-0 right-0 z-20 mt-6 py-3">
                <x-pagination :paginator="$products" />
            </div>
            @endif

        </main>
    </div>
</div>
