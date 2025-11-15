<?php

namespace App\Filament\Resources\TransaksiJasa\Pages;

use App\Filament\Resources\TransaksiJasa\TransaksiJasaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiJasa extends EditRecord
{
    protected static string $resource = TransaksiJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
