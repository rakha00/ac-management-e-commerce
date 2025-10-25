<?php

namespace App\Filament\Resources\TransaksiJasas\Pages;

use App\Filament\Resources\TransaksiJasas\TransaksiJasaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiJasas extends ListRecords
{
    protected static string $resource = TransaksiJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
