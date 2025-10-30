<?php

namespace App\Filament\Resources\UnitAC\Pages;

use App\Filament\Resources\UnitAC\UnitACResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitAC extends CreateRecord
{
    protected static string $resource = UnitACResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
