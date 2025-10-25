<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between gap-4 sm:gap-12 flex-col sm:flex-row">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight">QR Absensi Harian</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Token berubah setiap hari (Asia/Jakarta)
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ now('Asia/Jakarta')->translatedFormat('d M Y') }}
                        </span>

                        <button type="button" onclick="openQRFullscreen()"
                            class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" />
                            </svg>
                            Fullscreen
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-6">
                <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-3 text-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="text-gray-500 dark:text-gray-400">Waktu Absen</div>
                        <div class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">
                            08:00 WIB
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-3 text-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="text-gray-500 dark:text-gray-400">Waktu Telat</div>
                        <div class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">
                            08:15 WIB
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center space-y-4">
                    <div
                        class="rounded-lg p-3 bg-white ring-1 ring-gray-200 shadow-sm dark:bg-gray-800 dark:ring-gray-700">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(260)->margin(1)->generate($this->tokenHarian) !!}
                    </div>

                    <div class="text-center space-y-1">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Arahkan kamera di halaman Scan Absensi karyawan ke QR ini.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Pastikan perangkat karyawan memiliki koneksi internet yang stabil.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-xl dark:bg-gray-900/50">
                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>Zona waktu: Asia/Jakarta</span>

                    <button type="button" onclick="window.location.reload()"
                        class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Overlay -->
    <div id="qr-fullscreen-overlay"
        class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        onclick="closeQRFullscreen()" aria-hidden="true">
        <div class="relative max-w-4xl mx-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-white text-sm font-medium">QR Absensi Harian</h3>
                <button type="button" onclick="closeQRFullscreen()"
                    class="inline-flex items-center gap-1 rounded-md bg-white/10 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm ring-1 ring-inset ring-white/20 hover:bg-white/20">
                    Close
                </button>
            </div>

            <div class="rounded-xl p-4 bg-white shadow-lg">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(600)->margin(1)->generate($this->tokenHarian) !!}
            </div>

            <p class="mt-3 text-center text-xs text-gray-200">
                Tekan ESC atau klik area gelap untuk menutup.
            </p>
        </div>
    </div>

    <script>
        function openQRFullscreen() {
            const overlay = document.getElementById('qr-fullscreen-overlay');
            overlay.classList.remove('hidden');
            if (overlay.requestFullscreen) {
                overlay.requestFullscreen().catch(() => { /* ignore */ });
            }
        }

        function closeQRFullscreen() {
            const overlay = document.getElementById('qr-fullscreen-overlay');
            overlay.classList.add('hidden');
            if (document.fullscreenElement) {
                document.exitFullscreen().catch(() => { /* ignore */ });
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeQRFullscreen();
            }
        }, { passive: true });
    </script>
</x-filament-panels::page>