<?php

namespace App\Filament\Resources\PengeluaranKantors\Pages;

use App\Filament\Resources\PengeluaranKantors\PengeluaranKantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranKantors extends ListRecords
{
    protected static string $resource = PengeluaranKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
