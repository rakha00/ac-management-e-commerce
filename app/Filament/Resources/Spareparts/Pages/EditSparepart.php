<?php

namespace App\Filament\Resources\Spareparts\Pages;

use App\Filament\Resources\Spareparts\SparepartResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSparepart extends EditRecord
{
    protected static string $resource = SparepartResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
