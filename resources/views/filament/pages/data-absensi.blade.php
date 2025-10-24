<x-filament-panels::page>
    <div class="space-y-4">
        <h2 class="text-xl font-semibold">QR Absensi Harian</h2>

        <div class="bg-white p-4 inline-block rounded shadow">
            {!! QrCode::size(240)->generate($this->tokenHarian) !!}
        </div>

        <p class="text-sm text-gray-600">
            Token absensi berubah setiap hari (zona Asia/Jakarta). Minta karyawan membuka halaman Scan Absensi di panel
            lalu arahkan kamera ke QR ini.
        </p>

        <div class="text-xs text-gray-400">
            Token (debug): {{ $this->tokenHarian }}
        </div>
    </div>
</x-filament-panels::page>