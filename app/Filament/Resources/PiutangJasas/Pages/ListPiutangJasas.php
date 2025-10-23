<?php

namespace App\Filament\Resources\PiutangJasas\Pages;

use App\Filament\Resources\PiutangJasas\PiutangJasaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangJasas extends ListRecords
{
    protected static string $resource = PiutangJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
