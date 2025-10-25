<?php

namespace App\Filament\Resources\SparepartKeluars\Pages;

use App\Filament\Resources\SparepartKeluars\SparepartKeluarResource;
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
