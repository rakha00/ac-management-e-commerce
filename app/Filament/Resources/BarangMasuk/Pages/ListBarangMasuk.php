<?php

namespace App\Filament\Resources\BarangMasuk\Pages;

use App\Filament\Resources\BarangMasuk\BarangMasukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBarangMasuk extends ListRecords
{
    protected static string $resource = BarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
