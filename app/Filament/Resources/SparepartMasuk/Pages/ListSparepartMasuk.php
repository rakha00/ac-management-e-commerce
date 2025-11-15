<?php

namespace App\Filament\Resources\SparepartMasuk\Pages;

use App\Filament\Resources\SparepartMasuk\SparepartMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSparepartMasuk extends ListRecords
{
    protected static string $resource = SparepartMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
