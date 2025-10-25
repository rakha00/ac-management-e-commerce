<x-filament-panels::page>
    <div class="max-w-3xl mx-auto px-3 sm:px-0">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between sm:gap-14 gap-4 flex-col sm:flex-row">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight">Scan Absensi</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Arahkan kamera ke QR pada halaman “QR Absensi Harian”.
                        </p>
                    </div>

                    <span id="scan-status"
                        class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                        Menunggu izin kamera
                    </span>
                </div>
            </div>

            <div class="px-4 py-6 sm:px-6 sm:py-8">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-full max-w-[560px]">
                        <div id="reader"
                            class="w-full aspect-square rounded-lg ring-1 ring-gray-200 shadow-sm dark:ring-gray-700 dark:bg-gray-800 overflow-hidden">
                        </div>
                    </div>

                    <div class="w-full max-w-[560px] grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label for="camera-select"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Pilih Kamera
                            </label>
                            <select id="camera-select"
                                class="w-full rounded-md border-gray-300 bg-white text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                                hidden>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button id="restart-btn" type="button"
                                class="w-full inline-flex items-center justify-center gap-1 rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                                Restart
                            </button>
                        </div>
                    </div>

                    <div class="text-center space-y-1">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Jika kamera tidak muncul, pastikan Anda memberi izin akses kamera pada browser.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Disarankan menggunakan kamera belakang (environment) di perangkat mobile.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gray-50 rounded-b-xl dark:bg-gray-900/50">
                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>Privasi: video tidak disimpan, hanya diproses di perangkat untuk membaca QR.</span>
                    <button type="button" id="reload-page"
                        class="hidden sm:inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                        Muat Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="absen-form" method="POST" action="{{ route('absensi.submit') }}" class="hidden">
        @csrf
        <input type="hidden" name="token" id="token">
    </form>

    <!-- Hidden triggers to open Filament modals via Alpine $dispatch -->
    <button id="open-success-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-success' })" class="hidden">Open</button>
    <button id="open-duplicate-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-duplicate' })" class="hidden">Open</button>

    <!-- Filament success modal shown after successful scan & submit -->
    <x-filament::modal id="scan-success" icon="heroicon-o-check-circle" icon-color="success"
        :close-by-clicking-away="false">
        <x-slot name="heading">
            Absen Berhasil
        </x-slot>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Absensi berhasil disimpan. Tekan “OK” untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='{{ \App\Filament\Pages\RiwayatAbsensi::getUrl() }}'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Duplicate modal -->
    <x-filament::modal id="scan-duplicate" icon="heroicon-o-exclamation-triangle" icon-color="warning"
        :close-by-clicking-away="false">
        <x-slot name="heading">
            Sudah Absen
        </x-slot>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Anda sudah melakukan absensi hari ini. Tekan “OK” untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='{{ \App\Filament\Pages\RiwayatAbsensi::getUrl() }}'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <style>
        /* Force scanner preview to strict 1:1 square */
        #reader {
            aspect-ratio: 1 / 1;
            display: block;
        }

        #reader video,
        #reader canvas {
            width: 100% !important;
            height: 100% !important;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            /* fill square without distortion (crop if needed) */
            display: block;
        }
    </style>
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusEl = document.getElementById('scan-status');
            const cameraSelect = document.getElementById('camera-select');
            const restartBtn = document.getElementById('restart-btn');
            const reloadBtn = document.getElementById('reload-page');
            const tokenInput = document.getElementById('token');
            const form = document.getElementById('absen-form');

            let html5QrCode = null;
            let currentCameraId = null;
            let isRunning = false;
            let availableCameras = [];

            const setStatus = (text, tone = 'info') => {
                statusEl.textContent = text;
                const base = 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset';
                const light = {
                    info: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-300',
                    warn: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                    ok: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                    err: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-900/30 dark:text-rose-300',
                };
                statusEl.className = base + ' ' + (light[tone] ?? light.info);
            };

            const stopScanner = async () => {
                if (html5QrCode) {
                    try { await html5QrCode.stop(); } catch (_) { }
                    try { await html5QrCode.clear(); } catch (_) { }
                }
                isRunning = false;
            };

            const startScanner = async (cameraId) => {
                try {
                    await stopScanner();
                    html5QrCode = new Html5Qrcode('reader');

                    const config = {
                        fps: 10,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const edge = Math.min(viewfinderWidth, viewfinderHeight);
                            const size = Math.min(340, Math.floor(edge * 0.8));
                            return { width: size, height: size };
                        },
                    };

                    currentCameraId = cameraId;
                    setStatus('Memulai kamera…', 'info');

                    await html5QrCode.start(
                        { deviceId: { exact: cameraId } },
                        config,
                        (decodedText) => {
                            if (!isRunning) return;
                            setStatus('Mendeteksi QR…', 'info');

                            // Submit segera setelah terdeteksi, lalu tampilkan modal sukses
                            tokenInput.value = decodedText;
                            setStatus('Mengirim absensi…', 'ok');

                            (async () => {
                                await stopScanner();
                                try {
                                    const fd = new FormData(form);
                                    const resp = await fetch(form.action, {
                                        method: 'POST',
                                        body: fd,
                                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                    });
                                    let created = true;
                                    try {
                                        const data = await resp.json();
                                        created = !!(data && data.created);
                                    } catch (_) {
                                        created = true;
                                    }
                                    if (created) {
                                        document.getElementById('open-success-modal')?.click();
                                    } else {
                                        document.getElementById('open-duplicate-modal')?.click();
                                    }
                                } catch (err) {
                                    setStatus('Gagal menyimpan: ' + err, 'err');
                                }
                            })();
                        },
                        (_errorMessage) => {
                            // Frame scan fail: dibiarkan sunyi untuk UX
                        }
                    );

                    isRunning = true;
                    setStatus('Kamera aktif — arahkan ke QR', 'ok');
                } catch (err) {
                    setStatus('Gagal memulai kamera: ' + err, 'err');
                }
            };

            const populateCameras = (cameras) => {
                cameraSelect.innerHTML = '';
                cameras.forEach((cam, idx) => {
                    const opt = document.createElement('option');
                    opt.value = cam.id;
                    opt.textContent = cam.label || `Kamera ${idx + 1}`;
                    cameraSelect.appendChild(opt);
                });
                cameraSelect.hidden = cameras.length <= 1;
            };

            const findDefaultCameraId = (cameras) => {
                const env = cameras.find(c => /back|rear|environment/i.test(c.label || ''));
                return (env || cameras[0])?.id ?? null;
            };

            const init = async () => {
                setStatus('Memuat daftar kamera…', 'warn');
                try {
                    availableCameras = await Html5Qrcode.getCameras();
                    if (!availableCameras || availableCameras.length === 0) {
                        setStatus('Tidak ada kamera terdeteksi', 'err');
                        return;
                    }

                    populateCameras(availableCameras);

                    const defId = findDefaultCameraId(availableCameras);
                    if (defId) {
                        cameraSelect.value = defId;
                        await startScanner(defId);
                    } else {
                        setStatus('Tidak menemukan kamera yang cocok', 'err');
                    }
                } catch (err) {
                    setStatus('Izin kamera ditolak atau tidak tersedia', 'err');
                }
            };

            cameraSelect.addEventListener('change', async (e) => {
                const newId = e.target.value;
                if (newId && newId !== currentCameraId) {
                    setStatus('Beralih kamera…', 'warn');
                    await startScanner(newId);
                }
            });

            restartBtn.addEventListener('click', async () => {
                if (currentCameraId) {
                    setStatus('Restart scanner…', 'warn');
                    await startScanner(currentCameraId);
                } else {
                    init();
                }
            });

            reloadBtn?.addEventListener('click', () => window.location.reload());

            init();
        });
    </script>
</x-filament-panels::page>