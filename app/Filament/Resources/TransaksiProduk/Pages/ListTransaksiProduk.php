<?php

namespace App\Filament\Resources\TransaksiProduk\Pages;

use App\Filament\Resources\TransaksiProduk\TransaksiProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiProduk extends ListRecords
{
    protected static string $resource = TransaksiProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
