<?php

namespace App\Filament\Resources\PiutangProduks\Pages;

use App\Filament\Resources\PiutangProduks\PiutangProdukResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPiutangProduk extends EditRecord
{
    protected static string $resource = PiutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
