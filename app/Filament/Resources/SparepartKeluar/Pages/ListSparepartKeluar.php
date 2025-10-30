<?php

namespace App\Filament\Resources\SparepartKeluar\Pages;

use App\Filament\Resources\SparepartKeluar\SparepartKeluarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSparepartKeluar extends ListRecords
{
    protected static string $resource = SparepartKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
