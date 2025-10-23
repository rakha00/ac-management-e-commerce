<?php

namespace App\Filament\Resources\UnitACS\Pages;

use App\Filament\Resources\UnitACS\UnitACResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitACS extends ListRecords
{
    protected static string $resource = UnitACResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
