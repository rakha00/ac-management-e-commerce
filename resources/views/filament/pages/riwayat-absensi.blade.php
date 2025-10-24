<x-filament-panels::page>
    <div class="space-y-4">
        <h2 class="text-xl font-semibold">Riwayat Absensi</h2>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Waktu</th>
                        <th class="px-4 py-2 text-left">Telat</th>
                        <th class="px-4 py-2 text-left">Keterangan</th>
                        <th class="px-4 py-2 text-left">Terkonfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $row)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ \Illuminate\Support\Carbon::parse($row->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-2">
                                {{ \Illuminate\Support\Carbon::parse($row->waktu_absen)->format('H:i:s') }}</td>
                            <td class="px-4 py-2">
                                @if($row->telat)
                                    <span class="text-red-600 font-medium">Ya</span>
                                @else
                                    <span class="text-green-600">Tidak</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $row->keterangan }}</td>
                            <td class="px-4 py-2">
                                @if($row->terkonfirmasi)
                                    <span class="text-green-600">Sudah</span>
                                @else
                                    <span class="text-yellow-600">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>