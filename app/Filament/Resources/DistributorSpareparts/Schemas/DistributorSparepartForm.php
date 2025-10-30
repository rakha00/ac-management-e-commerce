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
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kontak')
                            ->label('Kontak')
                            ->tel()
                            ->maxLength(255)
                            ->nullable(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->maxLength(255)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
