<?php

namespace App\Filament\Resources\Laporan\Pages;

use App\Filament\Resources\Laporan\LaporanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporan extends CreateRecord
{
    protected static string $resource = LaporanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            abort(403, 'Unauthorized');
        }

        // Auto-set karyawan_id to the authenticated user's Karyawan record
        $data['karyawan_id'] = $user->karyawan->id;

        return $data;
    }
}
