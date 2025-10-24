<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use Filament\Pages\Page;

class RiwayatAbsensi extends Page
{
    protected string $view = 'filament.pages.riwayat-absensi';

    // Navigasi hanya untuk non-admin (karyawan)
    public static function shouldRegisterNavigation(): bool
    {
        $jabatan = auth()->user()?->karyawan?->jabatan;
        return in_array($jabatan, ['gudang', 'helper', 'teknisi', 'staff', 'sales']);
    }

    public function getTitle(): string
    {
        return 'Riwayat Absensi';
    }

    // Sediakan data riwayat untuk Blade
    public function getViewData(): array
    {
        $karyawan = auth()->user()?->karyawan;

        $items = Absensi::query()
            ->where('karyawan_id', $karyawan?->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_absen')
            ->limit(60)
            ->get();

        return compact('items');
    }
}
