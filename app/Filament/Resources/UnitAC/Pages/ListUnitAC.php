<?php

namespace App\Filament\Resources\UnitAC\Pages;

use App\Filament\Resources\UnitAC\UnitACResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitAC extends ListRecords
{
    protected static string $resource = UnitACResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
