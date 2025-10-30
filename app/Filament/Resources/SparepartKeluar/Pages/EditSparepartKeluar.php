<?php

namespace App\Filament\Resources\SparepartKeluar\Pages;

use App\Filament\Resources\SparepartKeluar\SparepartKeluarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSparepartKeluar extends EditRecord
{
    protected static string $resource = SparepartKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
