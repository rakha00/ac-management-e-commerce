<?php

namespace App\Filament\Resources\HutangProduks\Pages;

use App\Filament\Resources\HutangProduks\HutangProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHutangProduks extends ListRecords
{
    protected static string $resource = HutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
