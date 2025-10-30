<?php

namespace App\Filament\Resources\TransaksiProduk\Pages;

use App\Filament\Resources\TransaksiProduk\TransaksiProdukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiProduk extends EditRecord
{
    protected static string $resource = TransaksiProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
