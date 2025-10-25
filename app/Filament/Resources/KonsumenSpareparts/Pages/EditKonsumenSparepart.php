<?php

namespace App\Filament\Resources\KonsumenSpareparts\Pages;

use App\Filament\Resources\KonsumenSpareparts\KonsumenSparepartResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKonsumenSparepart extends EditRecord
{
    protected static string $resource = KonsumenSparepartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
