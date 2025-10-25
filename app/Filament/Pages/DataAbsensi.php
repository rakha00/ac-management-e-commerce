<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DataAbsensi extends Page
{
    protected string $view = 'filament.pages.data-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'QR Absensi Harian';

    public ?string $tokenHarian = null;

    public function mount(): void
    {
        // Token harian berbasis APP_KEY + tanggal (zona Asia/Jakarta)
        $today = now('Asia/Jakarta')->format('Y-m-d');
        $this->tokenHarian = hash_hmac('sha256', $today, config('app.key'));
    }

    // Tampilkan di navigasi hanya untuk admin
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->karyawan?->jabatan === 'admin';
    }
}
