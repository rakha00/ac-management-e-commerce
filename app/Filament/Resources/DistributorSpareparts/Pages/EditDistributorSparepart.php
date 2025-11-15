<?php

namespace App\Filament\Resources\DistributorSpareparts\Pages;

use App\Filament\Resources\DistributorSpareparts\DistributorSparepartResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDistributorSparepart extends EditRecord
{
    protected static string $resource = DistributorSparepartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
