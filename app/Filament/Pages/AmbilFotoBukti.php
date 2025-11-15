<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AmbilFotoBukti extends Page
{
    protected string $view = 'filament.pages.ambil-foto-bukti';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?string $token = null;

    public function mount(): void
    {
        // Check karyawan profile
        if (! auth()->user()?->karyawan) {
            abort(403, 'Anda tidak memiliki profil karyawan.');
        }

        // Get token from URL
        $this->token = request()->query('token');

        if (! $this->token) {
            abort(400, 'Token tidak ditemukan. Silakan scan QR kembali.');
        }
    }
}
