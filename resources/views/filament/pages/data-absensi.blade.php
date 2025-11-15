<x-filament-panels::page>
    <style>
        .qr-container {
            max-width: 672px;
            margin: 0 auto;
        }

        .qr-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background-color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .dark .qr-card {
            background-color: #111827;
            border-color: #1f2937;
        }

        .qr-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
        }

        .dark .qr-header {
            border-bottom-color: #1f2937;
        }

        .qr-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-direction: column;
        }

        @media (min-width: 640px) {
            .qr-header-content {
                flex-direction: row;
                gap: 48px;
            }
        }

        .qr-title {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.025em;
            margin: 0;
        }

        .qr-subtitle {
            margin-top: 4px;
            font-size: 14px;
            color: #6b7280;
        }

        .dark .qr-subtitle {
            color: #9ca3af;
        }

        .qr-header-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 6px;
            background-color: #eff6ff;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 500;
            color: #1d4ed8;
            border: 1px solid rgba(29, 78, 216, 0.2);
        }

        .dark .date-badge {
            background-color: rgba(29, 78, 216, 0.3);
            color: #93c5fd;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 6px;
            background-color: white;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #d1d5db;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #f9fafb;
        }

        .dark .btn {
            background-color: #1f2937;
            color: #e5e7eb;
            border-color: #374151;
        }

        .dark .btn:hover {
            background-color: #374151;
        }

        .qr-body {
            padding: 24px 24px 0;
        }

        .info-grid {
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .info-card {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: white;
            padding: 12px;
            font-size: 14px;
        }

        .dark .info-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        .info-label {
            color: #6b7280;
        }

        .dark .info-label {
            color: #9ca3af;
        }

        .info-value {
            margin-top: 4px;
            color: #111827;
            font-weight: 600;
        }

        .dark .info-value {
            color: #f3f4f6;
        }

        .qr-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding-bottom: 16px;
        }

        .qr-wrapper {
            border-radius: 8px;
            padding: 12px;
            background-color: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .dark .qr-wrapper {
            background-color: #1f2937;
            border-color: #374151;
        }

        .qr-instructions {
            text-align: center;
        }

        .qr-instruction-text {
            font-size: 14px;
            color: #374151;
            margin: 0 0 4px 0;
        }

        .dark .qr-instruction-text {
            color: #d1d5db;
        }

        .qr-instruction-subtext {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .dark .qr-instruction-subtext {
            color: #9ca3af;
        }

        .qr-footer {
            padding: 16px 24px;
            background-color: #f9fafb;
            border-radius: 0 0 12px 12px;
        }

        .dark .qr-footer {
            background-color: rgba(17, 24, 39, 0.5);
        }

        .qr-footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7280;
        }

        .dark .qr-footer-content {
            color: #9ca3af;
        }

        .fullscreen-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .fullscreen-overlay.active {
            display: flex;
        }

        .fullscreen-content {
            position: relative;
            max-width: 896px;
            margin: 0 auto;
        }

        .fullscreen-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .fullscreen-title {
            color: white;
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }

        .btn-close {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 500;
            color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }

        .btn-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .fullscreen-qr {
            border-radius: 12px;
            padding: 16px;
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .fullscreen-hint {
            margin-top: 12px;
            text-align: center;
            font-size: 12px;
            color: #e5e7eb;
        }

        .icon-sm {
            width: 14px;
            height: 14px;
        }
    </style>

    <div class="qr-container">
        <div class="qr-card">
            <div class="qr-header">
                <div class="qr-header-content">
                    <div>
                        <h2 class="qr-title">QR Absensi Harian</h2>
                        <p class="qr-subtitle">
                            Token berubah setiap hari (Asia/Jakarta)
                        </p>
                    </div>

                    <div class="qr-header-buttons">
                        <span class="date-badge">
                            {{ now('Asia/Jakarta')->translatedFormat('d M Y') }}
                        </span>

                        <button type="button" onclick="openQRFullscreen()" class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-sm" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" />
                            </svg>
                            Fullscreen
                        </button>
                    </div>
                </div>
            </div>

            <div class="qr-body">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Waktu Absen</div>
                        <div class="info-value">
                            {{ $this->jamMasuk }} WIB
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Waktu Telat</div>
                        <div class="info-value">
                            {{ $this->waktuTelat }} WIB
                        </div>
                    </div>
                </div>
                <div class="qr-center">
                    <div class="qr-wrapper">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(260)->margin(1)->generate($this->tokenHarian) !!}
                    </div>

                    <div class="qr-instructions">
                        <p class="qr-instruction-text">
                            Arahkan kamera di halaman Scan Absensi karyawan ke QR ini.
                        </p>
                        <p class="qr-instruction-subtext">
                            Pastikan perangkat karyawan memiliki koneksi internet yang stabil.
                        </p>
                    </div>
                </div>
            </div>

            <div class="qr-footer">
                <div class="qr-footer-content">
                    <span>Zona waktu: Asia/Jakarta</span>

                    <button type="button" onclick="window.location.reload()" class="btn">
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Overlay -->
    <div id="qr-fullscreen-overlay" class="fullscreen-overlay" onclick="closeQRFullscreen()" aria-hidden="true">
        <div class="fullscreen-content" onclick="event.stopPropagation()">
            <div class="fullscreen-header">
                <h3 class="fullscreen-title">QR Absensi Harian</h3>
                <button type="button" onclick="closeQRFullscreen()" class="btn-close">
                    Close
                </button>
            </div>

            <div class="fullscreen-qr">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(600)->margin(1)->generate($this->tokenHarian) !!}
            </div>

            <p class="fullscreen-hint">
                Tekan ESC atau klik area gelap untuk menutup.
            </p>
        </div>
    </div>

    <script>
        function openQRFullscreen() {
            const overlay = document.getElementById('qr-fullscreen-overlay');
            overlay.classList.add('active');
            if (overlay.requestFullscreen) {
                overlay.requestFullscreen().catch(() => { /* ignore */ });
            }
        }

        function closeQRFullscreen() {
            const overlay = document.getElementById('qr-fullscreen-overlay');
            overlay.classList.remove('active');
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