<?php

namespace App\Filament\Resources\Spareparts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class SparepartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sparepart')
                    ->columns(1)
                    ->schema([
                        TextInput::make('kode_sparepart')
                            ->label('Kode Sparepart')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Harga Saat Ini')
                    ->columns(1)
                    ->schema([
                        TextInput::make('harga_modal')
                            ->label('Harga Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),

                        TextInput::make('harga_ecommerce')
                            ->label('Harga E-commerce')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),
                    ]),
                Section::make('Stok')
                    ->columns(2)
                    ->schema([
                        TextInput::make('stok_awal')
                            ->label('Stok Awal')
                            ->numeric()
                            ->required()
                            ->suffix('unit'),
                        TextInput::make('stok_akhir')
                            ->label('Stok Akhir')
                            ->numeric()
                            ->suffix('unit')
                            ->disabled(),
                        TextInput::make('stok_masuk')
                            ->label('Stok Masuk')
                            ->numeric()
                            ->suffix('unit')
                            ->disabled(),
                        TextInput::make('stok_keluar')
                            ->label('Stok Keluar')
                            ->numeric()
                            ->suffix('unit')
                            ->disabled(),
                    ]),
                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Foto Sparepart')
                    ->schema([
                        FileUpload::make('path_foto_sparepart')
                            ->label('Foto Sparepart')
                            ->image()
                            ->multiple()
                            ->directory('foto-sparepart')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->nullable(),
                    ]),
            ])
            ->columns(1);
    }
}
