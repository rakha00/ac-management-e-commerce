<?php

namespace App\Filament\Resources\PengeluaranKantor\Pages;

use App\Filament\Resources\PengeluaranKantor\PengeluaranKantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranKantor extends ListRecords
{
    protected static string $resource = PengeluaranKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
