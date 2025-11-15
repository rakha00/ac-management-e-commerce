<?php

namespace App\Filament\Resources\DistributorSpareparts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistributorSparepartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Distributor Sparepart')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nama_distributor')
                            ->label('Nama Distributor')
                            ->required(),
                        TextInput::make('kontak')
                            ->label('Kontak'),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
