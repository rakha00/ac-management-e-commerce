<nav x-data="{ open: false }" class="bg-white shadow-md fixed top-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="bg-blue-800 text-white text-xl font-bold px-4 py-2 rounded-lg mr-2">S</span>
                        <span class="text-2xl font-bold text-blue-800">SejukMart</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 lg:flex">
                    <a href="/"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Beranda</a>
                    <a href="#"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Produk</a>
                    <a href="#"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Kategori</a>
                    <a href="#"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Promo</a>
                    <a href="#"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Blog</a>
                    <a href="/tentang-kami"
                        class="inline-flex items-center px-1 pt-1 font-medium leading-5 text-gray-600 hover:text-blue-800 focus:outline-none transition duration-150 ease-in-out">Tentang
                        Kami</a>
                </div>
            </div>
            <div class="hidden lg:flex items-center ml-auto">
                <a href="https://wa.me/6281234567890" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <x-fab-whatsapp class="w-5 h-5 mr-1" />
                    WhatsApp
                </a>
            </div>
            <!-- Pindahkan hamburger ke sini untuk penempatan yang benar -->
            <div class="flex items-center lg:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Beranda</a>
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Produk</a>
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Kategori</a>
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Promo</a>
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Blog</a>
            <a href="/tentang-kami"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Tentang
                Kami</a>
        </div>
    </div>
</nav>