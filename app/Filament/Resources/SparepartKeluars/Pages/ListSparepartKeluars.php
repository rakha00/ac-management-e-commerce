<?php

namespace App\Filament\Resources\SparepartKeluars\Pages;

use App\Filament\Resources\SparepartKeluars\SparepartKeluarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSparepartKeluars extends ListRecords
{
    protected static string $resource = SparepartKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
