<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DataAbsensi extends Page
{
    protected string $view = 'filament.pages.data-absensi';

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
