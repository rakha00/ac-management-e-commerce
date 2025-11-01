<?php

namespace App\Filament\Resources\Konsumen\Pages;

use App\Filament\Resources\Konsumen\KonsumenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKonsumen extends EditRecord
{
    protected static string $resource = KonsumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
