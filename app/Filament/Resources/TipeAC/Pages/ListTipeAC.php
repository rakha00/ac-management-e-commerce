<?php

namespace App\Filament\Resources\TipeAC\Pages;

use App\Filament\Resources\TipeAC\TipeACResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipeAC extends ListRecords
{
    protected static string $resource = TipeACResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
