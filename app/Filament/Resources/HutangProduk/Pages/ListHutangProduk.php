<?php

namespace App\Filament\Resources\HutangProduk\Pages;

use App\Filament\Resources\HutangProduk\HutangProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHutangProduk extends ListRecords
{
    protected static string $resource = HutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
