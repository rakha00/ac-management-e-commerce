<x-filament-panels::page>
    @if ($this->sudahAbsenHariIni)
        <div class="p-4 bg-green-100 text-green-700 rounded-lg">
            Anda sudah absen hari ini.
        </div>
    @else
        <div class="p-4 bg-red-100 text-red-700 rounded-lg">
            Anda belum absen hari ini.
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>