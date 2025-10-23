<?php

namespace App\Filament\Resources\UnitACS\Pages;

use App\Filament\Resources\UnitACS\UnitACResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitAC extends EditRecord
{
    protected static string $resource = UnitACResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
