<a href="{{ \App\Helpers\PriceHelper::url('/cart') }}" class="flex items-center text-gray-600 hover:text-gsi-red relative">
    <x-heroicon-o-shopping-cart class="w-7 h-7" />
    <span class="ml-2 text-sm font-medium hidden lg:block">Keranjang</span>
    @if($count > 0)
        <span
            class="absolute -top-2 left-4 bg-gsi-red text-white text-xs w-5 h-5 rounded-full flex items-center justify-center animate-bounce">
            {{ $count }}
        </span>
    @endif
</a>
