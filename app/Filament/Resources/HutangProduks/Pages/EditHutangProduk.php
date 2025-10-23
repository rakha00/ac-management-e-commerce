<?php

namespace App\Filament\Resources\HutangProduks\Pages;

use App\Filament\Resources\HutangProduks\HutangProdukResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHutangProduk extends EditRecord
{
    protected static string $resource = HutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
