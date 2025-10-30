<?php

namespace App\Filament\Resources\PengeluaranKantor\Pages;

use App\Filament\Resources\PengeluaranKantor\PengeluaranKantorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaranKantor extends EditRecord
{
    protected static string $resource = PengeluaranKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
