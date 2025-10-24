<x-filament-panels::page>
    <div class="space-y-4">
        <h2 class="text-xl font-semibold">Scan Absensi</h2>

        <div id="reader" style="width: 320px;"></div>

        <form id="absen-form" method="POST" action="{{ route('absensi.submit') }}" class="hidden">
            @csrf
            <input type="hidden" name="token" id="token">
        </form>

        <p class="text-sm text-gray-600">Arahkan kamera ke QR pada halaman Data Absensi.</p>

        <div class="text-xs text-gray-500">
            Jika kamera tidak tersedia, minta admin menampilkan QR dan coba perangkat lain.
        </div>
    </div>

    {{-- Use HTML5 QR Code scanner --}}
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startScanner = () => {
                const html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                Html5Qrcode.getCameras().then(cameras => {
                    const cameraId = (cameras[0] || {}).id;
                    if (!cameraId) {
                        alert('Camera not found');
                        return;
                    }
                    html5QrCode.start(cameraId, config, (decodedText) => {
                        // decodedText should be the daily token
                        document.getElementById('token').value = decodedText;
                        document.getElementById('absen-form').submit();
                        html5QrCode.stop();
                    }, (errorMessage) => {
                        // Optional: handle scan failure per frame
                        // console.warn(errorMessage);
                    }).catch(err => alert('Camera start error: ' + err));
                }).catch(err => alert('Camera access error: ' + err));
            };
            startScanner();
        });
    </script>
</x-filament-panels::page>