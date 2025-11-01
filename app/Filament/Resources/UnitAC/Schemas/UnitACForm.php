<?php

namespace App\Filament\Resources\UnitAC\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class UnitACForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Unit')
                ->columns(1)
                ->schema([
                    TextInput::make('sku')
                        ->label('SKU')
                        ->placeholder('Contoh: SKU123')
                        ->maxLength(50)
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('nama_unit')
                        ->label('Nama Unit')
                        ->placeholder('Contoh: Panasonic')
                        ->maxLength(100)
                        ->required(),
                ]),
            Section::make('Foto Produk')
                ->schema([
                    FileUpload::make('foto_produk')
                        ->label('Foto Produk')
                        ->image()
                        ->multiple()
                        ->directory('foto-produk')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->nullable(),
                ]),
            Section::make('Harga Saat Ini')
                ->columns(1)
                ->schema([
                    TextInput::make('harga_dealer')
                        ->label('Harga Dealer')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('0')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->required(),
                    TextInput::make('harga_ecommerce')
                        ->label('Harga E-commerce')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('0')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->required(),
                    TextInput::make('harga_retail')
                        ->label('Harga Retail')
                        ->numeric()
                        ->minValue(0)
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
                        ->minValue(0)
                        ->default(0)
                        ->placeholder('0')
                        ->suffix('unit')
                        ->required(),
                    TextInput::make('stok_akhir')
                        ->label('Stok Akhir')
                        ->numeric()
                        ->suffix('unit')
                        ->disabled(),
                    TextInput::make('stok_masuk')
                        ->label('Stok Masuk')
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
                        ->rows(4)
                        ->maxLength(500)
                        ->placeholder('Tambahkan detail/notes terkait unit AC...')
                        ->nullable(),
                ]),
        ]);
    }
}
