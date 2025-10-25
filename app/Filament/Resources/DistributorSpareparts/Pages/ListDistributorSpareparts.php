<?php

namespace App\Filament\Resources\DistributorSpareparts\Pages;

use App\Filament\Resources\DistributorSpareparts\DistributorSparepartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDistributorSpareparts extends ListRecords
{
    protected static string $resource = DistributorSparepartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
