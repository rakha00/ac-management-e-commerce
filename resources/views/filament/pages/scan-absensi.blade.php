<x-filament-panels::page>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <div class="header-top">
                    <div class="header-text">
                        <h2 class="title">Scan Absensi</h2>
                        <p class="subtitle">
                            Arahkan kamera ke QR pada halaman "QR Absensi Harian".
                        </p>
                    </div>

                    <span id="scan-status" class="status-badge status-warn">
                        Menunggu izin kamera
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="scanner-section">
                    <div class="scanner-wrapper">
                        <div id="reader" class="scanner"></div>
                    </div>

                    <div class="camera-controls">
                        <div class="camera-select">
                            <label for="camera-select" class="camera-label">
                                Pilih Kamera
                            </label>
                            <select id="camera-select" class="camera-dropdown" hidden></select>
                        </div>

                        <div class="restart-container">
                            <button id="restart-btn" type="button" class="btn-restart">
                                Restart
                            </button>
                        </div>
                    </div>

                    <div class="scanner-notes">
                        <p class="scanner-note">
                            Jika kamera tidak muncul, pastikan Anda memberi izin akses kamera pada browser.
                        </p>
                        <p class="scanner-note-secondary">
                            Disarankan menggunakan kamera belakang (environment) di perangkat mobile.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="footer-content">
                    <span>Privasi: video tidak disimpan, hanya diproses di perangkat untuk membaca QR.</span>
                    <button type="button" id="reload-page" class="btn-reload">
                        Muat Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="absen-form" method="POST" action="{{ route('absensi.submit') }}" class="hidden-form">
        @csrf
        <input type="hidden" name="token" id="token">
    </form>

    <!-- Hidden triggers to open Filament modals via Alpine $dispatch -->
    <button id="open-success-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-success' })" class="hidden-btn">Open</button>
    <button id="open-duplicate-modal" type="button" x-data="{}"
        x-on:click="$dispatch('open-modal', { id: 'scan-duplicate' })" class="hidden-btn">Open</button>

    <!-- Filament success modal shown after successful scan & submit -->
    <x-filament::modal id="scan-success" icon="heroicon-o-check-circle" icon-color="success"
        :close-by-clicking-away="false">
        <x-slot name="heading">
            Absen Berhasil
        </x-slot>

        <p class="modal-text">
            Absensi berhasil disimpan. Tekan "OK" untuk menuju halaman Riwayat Absensi.
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

        <p class="modal-text">
            Anda sudah melakukan absensi hari ini. Tekan "OK" untuk menuju halaman Riwayat Absensi.
        </p>

        <x-slot name="footer">
            <x-filament::button x-on:click="window.location.href='{{ \App\Filament\Pages\RiwayatAbsensi::getUrl() }}'">
                OK
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <style>
        :root {
            --bg-light: #ffffff;
            --bg-dark: #1f2937;
            --bg-darker: #111827;
            --text-dark: #1f2937;
            --text-medium: #4b5563;
            --text-light: #6b7280;
            --text-lighter: #9ca3af;
            --text-dark-mode: #d1d5db;
            --text-dark-mode-secondary: #9ca3af;
            --border-color: #e5e7eb;
            --border-dark: #374151;
            --border-darker: #1f2937;
            --radius: 12px;
            --shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --ring-gray: #d1d5db;
        }

        /* Page Container */
        .page-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 0.75rem;
        }

        @media (min-width: 640px) {
            .page-container {
                padding: 0;
            }
        }

        /* Card */
        .card {
            border-radius: var(--radius);
            border: 1px solid #e5e7eb;
            background-color: var(--bg-light);
            box-shadow: var(--shadow);
        }

        body.dark .card {
            background-color: #111827;
            border-color: #1f2937;
        }

        /* Card Header */
        .card-header {
            padding: 1rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        body.dark .card-header {
            border-bottom-color: #1f2937;
        }

        @media (min-width: 640px) {
            .card-header {
                padding: 1.25rem 1.5rem;
            }
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-direction: column;
        }

        @media (min-width: 640px) {
            .header-top {
                flex-direction: row;
                gap: 3.5rem;
            }
        }

        .header-text {
            width: 100%;
        }

        @media (min-width: 640px) {
            .header-text {
                width: auto;
            }
        }

        .title {
            font-size: 1.125rem;
            font-weight: 600;
            letter-spacing: -0.025em;
            margin: 0;
            color: var(--text-dark);
        }

        body.dark .title {
            color: var(--text-dark-mode);
        }

        .subtitle {
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #4b5563;
        }

        body.dark .subtitle {
            color: #9ca3af;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .status-info {
            background-color: #eff6ff;
            color: #1e40af;
            border-color: rgba(37, 99, 235, 0.2);
        }

        body.dark .status-info {
            background-color: rgba(30, 64, 175, 0.3);
            color: #93c5fd;
        }

        .status-warn {
            background-color: #fef3c7;
            color: #92400e;
            border-color: rgba(217, 119, 6, 0.2);
        }

        body.dark .status-warn {
            background-color: rgba(120, 53, 15, 0.3);
            color: #fcd34d;
        }

        .status-ok {
            background-color: #d1fae5;
            color: #065f46;
            border-color: rgba(5, 150, 105, 0.2);
        }

        body.dark .status-ok {
            background-color: rgba(6, 95, 70, 0.3);
            color: #6ee7b7;
        }

        .status-err {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: rgba(220, 38, 38, 0.2);
        }

        body.dark .status-err {
            background-color: rgba(153, 27, 27, 0.3);
            color: #fca5a5;
        }

        /* Card Body */
        .card-body {
            padding: 1.5rem 1rem;
        }

        @media (min-width: 640px) {
            .card-body {
                padding: 2rem 1.5rem;
            }
        }

        .scanner-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .scanner-wrapper {
            width: 100%;
            max-width: 560px;
        }

        .scanner {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: var(--shadow);
            overflow: hidden;
            background-color: #f9fafb;
        }

        body.dark .scanner {
            border-color: #374151;
            background-color: #1f2937;
        }

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
            display: block;
        }

        /* Camera Controls */
        .camera-controls {
            width: 100%;
            max-width: 560px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .camera-controls {
                grid-template-columns: 2fr 1fr;
            }
        }

        .camera-select {
            width: 100%;
        }

        .camera-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        body.dark .camera-label {
            color: #d1d5db;
        }

        .camera-dropdown {
            width: 100%;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            font-size: 0.875rem;
            color: #374151;
            box-shadow: var(--shadow);
            padding: 0.5rem;
        }

        .camera-dropdown:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        body.dark .camera-dropdown {
            background-color: #1f2937;
            border-color: #374151;
            color: #d1d5db;
        }

        .restart-container {
            display: flex;
            align-items: flex-end;
        }

        .btn-restart {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            border-radius: 6px;
            background-color: #ffffff;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-restart:hover {
            background-color: #f9fafb;
        }

        body.dark .btn-restart {
            background-color: #1f2937;
            color: #d1d5db;
            border-color: #374151;
        }

        body.dark .btn-restart:hover {
            background-color: #374151;
        }

        /* Scanner Notes */
        .scanner-notes {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .scanner-note {
            font-size: 0.75rem;
            color: #4b5563;
            margin: 0;
        }

        body.dark .scanner-note {
            color: #9ca3af;
        }

        .scanner-note-secondary {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0;
        }

        body.dark .scanner-note-secondary {
            color: #9ca3af;
        }

        /* Card Footer */
        .card-footer {
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            border-radius: 0 0 var(--radius) var(--radius);
        }

        @media (min-width: 640px) {
            .card-footer {
                padding: 1rem 1.5rem;
            }
        }

        body.dark .card-footer {
            background-color: rgba(17, 24, 39, 0.5);
        }

        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #4b5563;
        }

        body.dark .footer-content {
            color: #9ca3af;
        }

        .btn-reload {
            display: none;
            align-items: center;
            gap: 0.25rem;
            border-radius: 6px;
            background-color: #ffffff;
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        @media (min-width: 640px) {
            .btn-reload {
                display: inline-flex;
            }
        }

        .btn-reload:hover {
            background-color: #f9fafb;
        }

        body.dark .btn-reload {
            background-color: #1f2937;
            color: #d1d5db;
            border-color: #374151;
        }

        body.dark .btn-reload:hover {
            background-color: #374151;
        }

        /* Hidden Elements */
        .hidden-form,
        .hidden-btn {
            display: none;
        }

        /* Modal Text */
        .modal-text {
            font-size: 0.875rem;
            color: #4b5563;
        }

        body.dark .modal-text {
            color: #9ca3af;
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
                statusEl.className = 'status-badge status-' + tone;
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