@section('title', 'Keranjang Belanja')

<div class="container mx-auto px-4 md:px-6 py-6 md:py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Keranjang Belanja</h1>

    @if(count($cartItems) > 0)
        <div class="flex flex-col lg:flex-row gap-6 md:gap-8">
            {{-- Cart Items List --}}
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <ul class="divide-y divide-gray-100">
                        @foreach($cartItems as $item)
                            <li class="p-4 md:p-6">
                                <div class="flex gap-4 md:gap-6 items-center">
                                    {{-- Image --}}
                                    <div class="flex-shrink-0">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" 
                                            class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg border border-gray-200">
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        
                                        {{-- Product Info --}}
                                        <div class="flex-1 min-w-0 mr-4">
                                            <h3 class="text-sm md:text-base font-semibold text-gray-900 line-clamp-2 mb-1" title="{{ $item['name'] }}">
                                                {{ $item['name'] }}
                                            </h3>
                                            <p class="text-xs md:text-sm text-gray-500">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </p>
                                        </div>

                                        {{-- Actions (Quantity & Remove) --}}
                                        <div class="flex items-center justify-between md:justify-end gap-4 md:gap-8 w-full md:w-auto">
                                            
                                            {{-- Quantity Control --}}
                                            <div class="flex items-center border border-gray-200 rounded-lg">
                                                <button wire:click="decrement('{{ $item['key'] }}')" 
                                                    class="p-1.5 md:p-2 text-gray-500 hover:text-gsi-red hover:bg-red-50 transition-colors rounded-l-lg">
                                                    <x-heroicon-o-minus class="w-4 h-4" />
                                                </button>
                                                <span class="w-8 md:w-10 text-center text-sm font-medium text-gray-900 select-none">
                                                    {{ $item['quantity'] }}
                                                </span>
                                                <button wire:click="increment('{{ $item['key'] }}')" 
                                                    class="p-1.5 md:p-2 text-gray-500 hover:text-gsi-red hover:bg-red-50 transition-colors rounded-r-lg">
                                                    <x-heroicon-o-plus class="w-4 h-4" />
                                                </button>
                                            </div>

                                            {{-- Subtotal & Remove --}}
                                            <div class="text-right min-w-[80px]">
                                                <p class="text-sm md:text-base font-bold text-gsi-red mb-1">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </p>
                                                <button wire:click="remove('{{ $item['key'] }}')" 
                                                    class="text-xs text-gray-400 hover:text-red-500 transition-colors flex items-center justify-end ml-auto gap-1">
                                                    <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 lg:sticky lg:top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Ringkasan Belanja</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <span>Total Item</span>
                            <span class="font-medium text-gray-900">{{ array_sum(array_column($cartItems, 'quantity')) }} barang</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-900">Total Harga</span>
                            <span class="text-xl font-bold text-gsi-red">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button wire:click="checkout" wire:loading.attr="disabled"
                        class="w-full bg-gsi-red text-white py-3.5 px-4 rounded-xl font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-200 flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <x-heroicon-s-chat-bubble-left-right class="w-5 h-5" />
                        <span>Checkout via WhatsApp</span>
                        <div wire:loading wire:target="checkout" class="ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                    
                    <p class="mt-4 text-xs text-center text-gray-500">
                        Proses checkout akan diarahkan ke WhatsApp admin kami untuk konfirmasi ketersediaan stok dan pembayaran.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div class="bg-red-50 p-6 rounded-full mb-4">
                <x-heroicon-o-shopping-cart class="h-12 w-12 text-gsi-red" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Keranjang Anda kosong</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">Sepertinya Anda belum menambahkan produk apapun. Yuk, cari produk kebutuhan AC Anda sekarang!</p>
            <a href="/produk" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-gsi-red hover:bg-red-700 transition-all shadow-lg shadow-red-200">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
