{{-- Filter Component --}}
<aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0 hidden md:block" x-data="{
    localCategory: @entangle('category').defer,
    localTipe: @entangle('tipe').defer,
    localMerk: @entangle('merk').defer
}">
    <div class="space-y-6">

        {{-- === Filter Kategori === --}}
        <div x-data="{ open: true }" class="bg-white p-4 rounded-lg shadow-sm">
            <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Kategori</h3>
                <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse class="space-y-3 pt-2">
                <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                    <input type="radio" name="category-filter" x-model="localCategory" value="all" @change="$wire.set('category', localCategory)"
                        class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                    <span class="ml-2">Semua</span>
                </label>
                <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                    <input type="radio" name="category-filter" x-model="localCategory" value="unit" @change="$wire.set('category', localCategory)"
                        class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                    <span class="ml-2">Unit AC</span>
                </label>
                <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                    <input type="radio" name="category-filter" x-model="localCategory" value="sparepart" @change="$wire.set('category', localCategory)"
                        class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                    <span class="ml-2">Sparepart</span>
                </label>
            </div>
        </div>

        {{-- === Filter Tipe AC === --}}
        @if($category !== 'sparepart')
        <div x-data="{ open: true }" class="bg-white p-4 rounded-lg shadow-sm">
            <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Tipe AC</h3>
                <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse class="space-y-3 pt-2">
                @foreach ($types as $type)
                    <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                        <input type="radio" name="tipe-filter" x-model="localTipe" value="{{ $type->id }}" @change="$wire.set('tipe', localTipe)"
                            class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                        <span class="ml-2">{{ $type->tipe_ac }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- === Filter Merek === --}}
        <div x-data="{ open: true }" class="bg-white p-4 rounded-lg shadow-sm">
            <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Merek</h3>
                <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-collapse class="space-y-3 pt-2">
                @foreach ($brands as $brand)
                    <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                        <input type="radio" name="merk-filter" x-model="localMerk" value="{{ $brand->id }}" @change="$wire.set('merk', localMerk)"
                            class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                        <span class="ml-2">{{ $brand->merk }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- === Filter Harga === --}}
        <div x-data="{
            tempMinPrice: @entangle('tempMinPrice'),
            tempMaxPrice: @entangle('tempMaxPrice'),
            priceLimitMax: @js($priceLimitMax),
            priceLimitMin: @js($priceLimitMin),
            formatPrice(price) {
                if (this.priceLimitMax <= 10000000) {
                    // For sparepart (max 10 juta), show in ribu (Rb)
                    if (price >= 1000000) {
                        return 'Rp ' + (price / 1000000).toFixed(1) + ' Jt';
                    }
                    return 'Rp ' + Math.round(price / 1000) + ' Rb';
                } else {
                    // For unit AC (max 50 juta), show in juta (Jt)
                    return 'Rp ' + (price / 1000000).toFixed(1) + ' Jt';
                }
            },
            getStep() {
                return this.priceLimitMax <= 10000000 ? 10000 : 1000000;
            }
        }" x-init="$watch('tempMinPrice', value => {
            if (value > tempMaxPrice) tempMinPrice = tempMaxPrice;
        });
        $watch('tempMaxPrice', value => {
            if (value < tempMinPrice) tempMaxPrice = tempMinPrice;
        });" class="bg-white p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-90 mb-4">Filter Harga</h3>

            {{-- Display Harga --}}
            <div class="flex justify-between items-center text-xs sm:text-sm mb-4 gap-2">
                <span class="px-2 py-1 bg-gray-100 rounded flex-1 text-center text-xs" style="min-height: 28px; display: flex; align-items: center; justify-content: center;"
                    x-text="formatPrice(tempMinPrice)"></span>
                <span class="flex-shrink-0">-</span>
                <span class="px-2 py-1 bg-gray-100 rounded flex-1 text-center text-xs" style="min-height: 28px; display: flex; align-items: center; justify-content: center;"
                    x-text="formatPrice(tempMaxPrice)"></span>
            </div>

            {{-- Range Slider --}}
            <div class="relative h-2 w-full mb-4">
                <div class="absolute bg-gray-200 rounded-full h-1.5 w-full top-0"></div>
                <div class="absolute bg-gsi-red rounded-full h-1.5 top-0"
                    :style="`left: ${((tempMinPrice - priceLimitMin) / (priceLimitMax - priceLimitMin)) * 100}%; right: ${100 - ((tempMaxPrice - priceLimitMin) / (priceLimitMax - priceLimitMin)) * 100}%`">
                </div>
                <input type="range" :min="priceLimitMin" :max="priceLimitMax" :value="tempMinPrice"
                    :step="getStep()"
                    x-on:input="
                        tempMinPrice = $event.target.valueAsNumber;
                        if (tempMinPrice > tempMaxPrice) tempMinPrice = tempMaxPrice;
                    "
                    class="absolute w-full h-1.5 appearance-none bg-transparent m-0 p-0 top-0">
                <input type="range" :min="priceLimitMin" :max="priceLimitMax" :value="tempMaxPrice"
                    :step="getStep()"
                    x-on:input="
                        tempMaxPrice = $event.target.valueAsNumber;
                        if (tempMaxPrice < tempMinPrice) tempMaxPrice = tempMinPrice;
                    "
                    class="absolute w-full h-1.5 appearance-none bg-transparent m-0 p-0 top-0">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-3">
                <button wire:click="resetFilters"
                    class="w-1/2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                    Reset
                </button>
                <button wire:click="applyFilters"
                    class="w-1/2 px-4 py-2 bg-gsi-red text-white rounded-lg font-semibold hover:bg-red-700 transition">
                    Terapkan
                </button>
            </div>
        </div>

    </div>
</aside>

{{-- === Mobile Filter === --}}
<div x-data="{
    localCategory: @entangle('category').defer,
    localTipe: @entangle('tipe').defer,
    localMerk: @entangle('merk').defer
}" x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 z-50 md:hidden" aria-modal="true">
    <div @click.outside="filtersOpen = false" x-show="filtersOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed bottom-0 left-0 right-0 w-full h-[90vh] bg-white rounded-t-2xl shadow-xl flex flex-col">
        {{-- Header --}}
        <div class="flex justify-between items-center p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h3 class="text-xl font-semibold text-gray-900">Filter Produk</h3>
            <button @click="filtersOpen = false" class="text-gray-500 hover:text-gsi-red">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        {{-- Isi Filter --}}
        <div class="p-4 space-y-6 overflow-y-auto">

            {{-- Kategori --}}
            <div x-data="{ open: true }" class="bg-gray-50 p-4 rounded-lg">
                <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">Kategori</h3>
                    <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-collapse class="space-y-3 pt-2">
                    <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                        <input type="radio" name="category-filter-mobile" x-model="localCategory" value="all" @change="$wire.set('category', localCategory)"
                            class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                        <span class="ml-2">Semua</span>
                    </label>
                    <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                        <input type="radio" name="category-filter-mobile" x-model="localCategory" value="unit" @change="$wire.set('category', localCategory)"
                            class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                        <span class="ml-2">Unit AC</span>
                    </label>
                    <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                        <input type="radio" name="category-filter-mobile" x-model="localCategory" value="sparepart" @change="$wire.set('category', localCategory)"
                            class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                        <span class="ml-2">Sparepart</span>
                    </label>
                </div>
            </div>

            {{-- Tipe AC --}}
            @if($category !== 'sparepart')
            <div x-data="{ open: true }" class="bg-gray-50 p-4 rounded-lg">
                <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">Tipe AC</h3>
                    <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-collapse class="space-y-3 pt-2">
                    @foreach ($types as $type)
                        <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                            <input type="radio" name="tipe-filter-mobile" x-model="localTipe" value="{{ $type->id }}" @change="$wire.set('tipe', localTipe)"
                                class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                            <span class="ml-2">{{ $type->tipe_ac }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Merek --}}
            <div x-data="{ open: true }" class="bg-gray-50 p-4 rounded-lg">
                <button @click="open = !open" class="flex justify-between items-center w-full mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">Merek</h3>
                    <x-heroicon-s-chevron-down class="w-5 h-5 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-collapse class="space-y-3 pt-2">
                    @foreach ($brands as $brand)
                        <label class="flex items-center text-sm text-gray-600 hover:text-gsi-red cursor-pointer">
                            <input type="radio" name="merk-filter-mobile" x-model="localMerk" value="{{ $brand->id }}" @change="$wire.set('merk', localMerk)"
                                class="h-4 w-4 text-gsi-red focus:ring-gsi-red/50">
                            <span class="ml-2">{{ $brand->merk }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Harga --}}
            <div x-data="{
                tempMinPrice: @entangle('tempMinPrice'),
                tempMaxPrice: @entangle('tempMaxPrice'),
                priceLimitMax: @js($priceLimitMax),
                priceLimitMin: @js($priceLimitMin),
                formatPrice(price) {
                    if (this.priceLimitMax <= 10000000) {
                        // For sparepart (max 10 juta), show in ribu (Rb)
                        if (price >= 1000000) {
                            return 'Rp ' + (price / 1000000).toFixed(1) + ' Jt';
                        }
                        return 'Rp ' + Math.round(price / 1000) + ' Rb';
                    } else {
                        // For unit AC (max 50 juta), show in juta (Jt)
                        return 'Rp ' + (price / 1000000).toFixed(1) + ' Jt';
                    }
                },
                getStep() {
                    return this.priceLimitMax <= 10000000 ? 10000 : 1000000;
                }
            }" x-init="$watch('tempMinPrice', value => {
                if (value > tempMaxPrice) tempMinPrice = tempMaxPrice;
            });
            $watch('tempMaxPrice', value => {
                if (value < tempMinPrice) tempMaxPrice = tempMinPrice;
            });" class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Harga</h3>

                <div class="flex justify-between items-center text-xs sm:text-sm mb-4 gap-2">
                    <span class="px-2 py-1 bg-white rounded border flex-1 text-center text-xs" style="min-height: 28px; display: flex; align-items: center; justify-content: center;"
                        x-text="formatPrice(tempMinPrice)"></span>
                    <span class="flex-shrink-0">-</span>
                    <span class="px-2 py-1 bg-white rounded border flex-1 text-center text-xs" style="min-height: 28px; display: flex; align-items: center; justify-content: center;"
                        x-text="formatPrice(tempMaxPrice)"></span>
                </div>

                <div class="relative h-2 w-full mb-4">
                    <div class="absolute bg-gray-200 rounded-full h-1.5 w-full top-0"></div>
                    <div class="absolute bg-gsi-red rounded-full h-1.5 top-0"
                        :style="`left: ${((tempMinPrice - priceLimitMin) / (priceLimitMax - priceLimitMin)) * 100}%; right: ${100 - ((tempMaxPrice - priceLimitMin) / (priceLimitMax - priceLimitMin)) * 100}%`">
                    </div>
                    <input type="range" :min="priceLimitMin" :max="priceLimitMax" :value="tempMinPrice"
                        :step="getStep()"
                        x-on:input="
                            tempMinPrice = $event.target.valueAsNumber;
                            if (tempMinPrice > tempMaxPrice) tempMinPrice = tempMaxPrice;
                        "
                        class="absolute w-full h-1.5 appearance-none bg-transparent m-0 p-0 top-0">
                    <input type="range" :min="priceLimitMin" :max="priceLimitMax" :value="tempMaxPrice"
                        :step="getStep()"
                        x-on:input="
                            tempMaxPrice = $event.target.valueAsNumber;
                            if (tempMaxPrice < tempMinPrice) tempMaxPrice = tempMinPrice;
                        "
                        class="absolute w-full h-1.5 appearance-none bg-transparent m-0 p-0 top-0">
                </div>
            </div>
        </div>

        {{-- Footer Button --}}
        <div class="p-4 border-t border-gray-200 sticky bottom-0 bg-white z-10 flex gap-3">
            <button wire:click="resetFilters" @click="filtersOpen = false"
                class="w-1/2 px-5 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold">
                Reset
            </button>
            <button wire:click="applyFilters" @click="filtersOpen = false"
                class="w-1/2 px-5 py-3 bg-gsi-red text-white rounded-lg font-semibold hover:bg-red-700">
                Terapkan
            </button>
        </div>
    </div>
</div>
