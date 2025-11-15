<?php

namespace App\Filament\Resources\Karyawan\Pages;

use App\Filament\Resources\Karyawan\KaryawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKaryawan extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
