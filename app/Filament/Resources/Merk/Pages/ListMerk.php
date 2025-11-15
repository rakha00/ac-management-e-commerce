<?php

namespace App\Filament\Resources\Merk\Pages;

use App\Filament\Resources\Merk\MerkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMerk extends ListRecords
{
    protected static string $resource = MerkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
