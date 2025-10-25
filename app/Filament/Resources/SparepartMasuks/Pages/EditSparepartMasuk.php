<?php

namespace App\Filament\Resources\SparepartMasuks\Pages;

use App\Filament\Resources\SparepartMasuks\SparepartMasukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSparepartMasuk extends EditRecord
{
    protected static string $resource = SparepartMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
