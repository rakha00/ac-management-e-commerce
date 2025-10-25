<?php

namespace App\Filament\Resources\TransaksiProduks\Pages;

use App\Filament\Resources\TransaksiProduks\TransaksiProdukResource;
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
