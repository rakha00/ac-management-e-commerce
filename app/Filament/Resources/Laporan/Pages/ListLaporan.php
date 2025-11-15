<?php

namespace App\Filament\Resources\Laporan\Pages;

use App\Filament\Resources\Laporan\LaporanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaporan extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
