<?php

namespace App\Filament\Resources\SparepartMasuks\Pages;

use App\Filament\Resources\SparepartMasuks\SparepartMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSparepartMasuks extends ListRecords
{
    protected static string $resource = SparepartMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
