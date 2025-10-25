<?php

namespace App\Filament\Resources\TransaksiJasas\Pages;

use App\Filament\Resources\TransaksiJasas\TransaksiJasaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiJasa extends EditRecord
{
    protected static string $resource = TransaksiJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
