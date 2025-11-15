<?php

namespace App\Filament\Resources\MerkSpareparts\Pages;

use App\Filament\Resources\MerkSpareparts\MerkSparepartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMerkSpareparts extends ListRecords
{
    protected static string $resource = MerkSparepartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
