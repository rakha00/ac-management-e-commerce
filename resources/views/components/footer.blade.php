<footer class="bg-gray-800 text-gray-300 pt-16 pb-8">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <img src="/img/GSI-landscape.png" alt="Logo GSI" class="h-16 w-auto mb-4">
                <p class="text-gray-400">
                    Dealer, Servis, dan Instalasi AC profesional. Solusi pendingin untuk kenyamanan Anda.
                </p>
            </div>
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Navigasi</h5>
                <ul class="space-y-2">
                    <li><a href="/" class="hover:text-gsi-red transition-colors">Home</a></li>
                    <li><a href="/produk" class="hover:text-gsi-red transition-colors">Produk AC</a></li>
                    <li><a href="/servis" class="hover:text-gsi-red transition-colors">Pesan Servis</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Layanan Kami</h5>
                <ul class="space-y-2 text-gray-400">
                    <li>Dealer Resmi AC</li>
                    <li>Servis & Perawatan</li>
                    <li>Instalasi & Pemasangan</li>
                    <li>Spare Part Original</li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Hubungi Kami</h5>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-start">
                        <x-heroicon-o-map-pin class="flex-shrink-0 w-5 h-5 mt-1 mr-2" />
                        <span>Jl. Placeholder No. 123, Jakarta, Indonesia</span>
                    </li>
                    <li class="flex items-center">
                        <x-heroicon-o-phone class="flex-shrink-0 w-5 h-5 mr-2" />
                        <span>0856-9564-3257</span>
                    </li>
                    <li class="flex items-center">
                        <x-heroicon-o-envelope class="flex-shrink-0 w-5 h-5 mr-2" />
                        <span>cs.globalservis.int@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-gray-700 text-center text-gray-500 text-sm">
            &copy; <span id="currentYear"></span> Global Servis Int. Semua Hak Dilindungi.
        </div>
    </div>
    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</footer>
