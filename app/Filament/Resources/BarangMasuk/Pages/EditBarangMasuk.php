<?php

namespace App\Filament\Resources\BarangMasuk\Pages;

use App\Filament\Resources\BarangMasuk\BarangMasukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBarangMasuk extends EditRecord
{
    protected static string $resource = BarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
