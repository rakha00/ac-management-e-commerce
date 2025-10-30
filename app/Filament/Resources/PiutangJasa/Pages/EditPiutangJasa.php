<?php

namespace App\Filament\Resources\PiutangJasa\Pages;

use App\Filament\Resources\PiutangJasa\PiutangJasaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPiutangJasa extends EditRecord
{
    protected static string $resource = PiutangJasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
