<?php

namespace App\Filament\Resources\TransaksiJasa\Pages;

use App\Filament\Resources\TransaksiJasa\TransaksiJasaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiJasa extends ListRecords
{
    protected static string $resource = TransaksiJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
