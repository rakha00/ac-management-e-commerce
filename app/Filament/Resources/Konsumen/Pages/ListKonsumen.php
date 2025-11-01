<?php

namespace App\Filament\Resources\Konsumen\Pages;

use App\Filament\Resources\Konsumen\KonsumenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKonsumen extends ListRecords
{
    protected static string $resource = KonsumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
