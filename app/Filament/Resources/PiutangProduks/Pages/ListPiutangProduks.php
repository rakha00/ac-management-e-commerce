<?php

namespace App\Filament\Resources\PiutangProduks\Pages;

use App\Filament\Resources\PiutangProduks\PiutangProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangProduks extends ListRecords
{
    protected static string $resource = PiutangProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
