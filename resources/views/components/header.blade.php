<header x-data="{ open: false }" @click.outside="open = false" class="bg-white shadow-md sticky top-0 z-50">

    <!-- Top Bar -->
    <div class="bg-gray-800 text-white text-xs py-1.5 hidden md:block">
        <div class="container mx-auto px-4 md:px-6 flex justify-between items-center">
            <span>Solusi AC Profesional: Dealer, Servis, Instalasi</span>

            <a href="https://wa.me/6285695643257" target="_blank" class="flex items-center hover:text-gsi-green">
                <x-fab-whatsapp class="w-4 h-4 mr-1.5" />
                <span>0856-9564-3257 / cs.globalservis.int@gmail.com</span>
            </a>
        </div>
    </div>

    <!-- Main Header -->
    <div class="container mx-auto px-4 md:px-6 py-3">
        <div class="flex justify-between items-center gap-4">

            <!-- Logo -->
            <a href="{{ \App\Helpers\PriceHelper::url('/') }}" class="flex-shrink-0">
                <img class="h-10 md:h-12 w-auto" src="/img/GSI-landscape.png" alt="Logo Global Servis Int.">
            </a>

            <!-- Desktop Search -->
            <div class="flex-grow max-w-2xl hidden md:block">
                @livewire('global-search')
            </div>

            <!-- Right Section -->
            <div class="flex items-center space-x-4">
                <a href="https://wa.me/6285695643257" target="_blank"
                    class="hidden md:flex items-center text-gray-600 hover:text-gsi-red">
                    <x-heroicon-o-chat-bubble-left-right class="w-7 h-7" />
                    <span class="ml-2 text-sm font-medium hidden lg:block">Konsultasi</span>
                </a>

@livewire('cart-counter')

                <div class="md:hidden">
                    <button @click="open = !open" class="text-gray-700 hover:text-gsi-red focus:outline-none">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Desktop Navigation -->
    <nav class="bg-white border-t border-gray-100 hidden md:block">
        <div class="container mx-auto px-4 md:px-6 py-2.5 flex items-center space-x-6">
            <a href="{{ \App\Helpers\PriceHelper::url('/') }}" class="text-gray-800 hover:text-gsi-red font-semibold">Home</a>

            @foreach ($tipeAC as $tipe)
                <a href="{{ \App\Helpers\PriceHelper::url('/produk?tipe=' . $tipe->id) }}" class="text-gray-700 hover:text-gsi-red font-medium">
                    {{ $tipe->tipe_ac }}
                </a>
            @endforeach

            <a href="{{ \App\Helpers\PriceHelper::url('/servis') }}" class="text-gray-700 hover:text-gsi-red font-medium">Pesan Servis</a>
            <a href="{{ \App\Helpers\PriceHelper::url('/produk?promo=true') }}" class="text-gsi-red hover:text-red-700 font-semibold">Promo Spesial</a>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="open" x-collapse class="md:hidden border-t bg-white">
        <div class="px-4 py-4 space-y-4">

            <!-- Mobile Search -->
            @livewire('global-search')

            <!-- Mobile Navigation Links -->
            <div class="space-y-2">

                <a href="{{ \App\Helpers\PriceHelper::url('/') }}"
                    class="block text-gray-800 hover:text-gsi-red font-semibold py-2 border-b border-gray-100">
                    Home
                </a>

                @foreach ($tipeAC as $tipe)
                    <a href="{{ \App\Helpers\PriceHelper::url('/produk?tipe=' . $tipe->id) }}"
                        class="block text-gray-700 hover:text-gsi-red font-medium py-2 border-b border-gray-100">
                        {{ $tipe->tipe_ac }}
                    </a>
                @endforeach

                <a href="{{ \App\Helpers\PriceHelper::url('/servis') }}"
                    class="block text-gray-700 hover:text-gsi-red font-medium py-2 border-b border-gray-100">
                    Pesan Servis
                </a>

                <a href="{{ \App\Helpers\PriceHelper::url('/produk?promo=true') }}"
                    class="block text-gsi-red hover:text-red-700 font-semibold py-2 border-b border-gray-100">
                    Promo Spesial
                </a>
            </div>

            <!-- Mobile Contact -->
            <div class="pt-4 space-y-3">
                <a href="https://wa.me/6285695643257" target="_blank"
                    class="flex items-center text-gray-600 hover:text-gsi-red">
                    <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-3" />
                    <span class="text-sm font-medium">Konsultasi</span>
                </a>
            </div>

        </div>
    </div>

</header>
