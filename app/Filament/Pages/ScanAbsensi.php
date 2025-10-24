<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanAbsensi extends Page
{
    protected string $view = 'filament.pages.scan-absensi';

    // Tampilkan di navigasi hanya untuk non-admin
    public static function shouldRegisterNavigation(): bool
    {
        $jabatan = auth()->user()?->karyawan?->jabatan;
        return in_array($jabatan, ['gudang', 'helper', 'teknisi', 'staff', 'sales']);
    }

    public function getTitle(): string
    {
        return 'Scan Absensi';
    }
}
