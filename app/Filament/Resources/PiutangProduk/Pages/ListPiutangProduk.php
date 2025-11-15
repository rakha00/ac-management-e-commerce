<?php

namespace App\Filament\Resources\PiutangProduk\Pages;

use App\Filament\Resources\PiutangProduk\PiutangProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangProduk extends ListRecords
{
    protected static string $resource = PiutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
