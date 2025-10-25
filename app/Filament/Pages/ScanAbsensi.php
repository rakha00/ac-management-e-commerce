<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ScanAbsensi extends Page
{
    protected string $view = 'filament.pages.scan-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
