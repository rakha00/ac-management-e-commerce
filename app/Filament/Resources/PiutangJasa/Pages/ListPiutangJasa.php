<?php

namespace App\Filament\Resources\PiutangJasa\Pages;

use App\Filament\Resources\PiutangJasa\PiutangJasaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangJasa extends ListRecords
{
    protected static string $resource = PiutangJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
