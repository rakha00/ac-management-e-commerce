<?php

namespace App\Filament\Resources\PettyCashes\Pages;

use App\Filament\Resources\PettyCashes\PettyCashResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPettyCash extends EditRecord
{
    protected static string $resource = PettyCashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
