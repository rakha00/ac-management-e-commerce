<?php

namespace App\Filament\Resources\PengeluaranKantors\Pages;

use App\Filament\Resources\PengeluaranKantors\PengeluaranKantorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaranKantor extends EditRecord
{
    protected static string $resource = PengeluaranKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
