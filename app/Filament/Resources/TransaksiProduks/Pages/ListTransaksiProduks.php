<?php

namespace App\Filament\Resources\TransaksiProduks\Pages;

use App\Filament\Resources\TransaksiProduks\TransaksiProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiProduks extends ListRecords
{
    protected static string $resource = TransaksiProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
