<?php

namespace App\Filament\Resources\KonsumenSpareparts\Pages;

use App\Filament\Resources\KonsumenSpareparts\KonsumenSparepartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKonsumenSpareparts extends ListRecords
{
    protected static string $resource = KonsumenSparepartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
