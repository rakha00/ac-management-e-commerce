<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ScanAbsensi extends Page
{
    protected string $view = 'filament.pages.scan-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $jabatan = auth()->user()?->karyawan?->jabatan;

        return in_array($jabatan, ['gudang', 'helper', 'teknisi', 'staff', 'sales']);
    }

    public function mount(): void
    {
        // Pastikan user punya profil karyawan
        if (! auth()->user()?->karyawan) {
            abort(403, 'Anda tidak memiliki profil karyawan.');
        }
    }

    public function getTitle(): string
    {
        return 'Scan Absensi';
    }
}
