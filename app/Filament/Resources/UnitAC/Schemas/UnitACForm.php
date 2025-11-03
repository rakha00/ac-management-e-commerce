<?php

namespace App\Filament\Resources\UnitAC\Schemas;

use App\Models\Merk;
use App\Models\TipeAC;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('nama_unit')
                        ->label('Nama Unit')
                        ->required(),
                    Select::make('merk_id')
                        ->label('Merk')
                        ->options(Merk::pluck('merk', 'id')),
                    TextInput::make('pk')
                        ->label('PK')
                        ->numeric(),
                    Select::make('tipe_ac_id')
                        ->label('Tipe AC')
                        ->options(TipeAC::pluck('tipe_ac', 'id')),
                ]),
            Section::make('Foto Produk')
                ->schema([
                    FileUpload::make('path_foto_produk')
                        ->label('Foto Produk')
                        ->disk('public')
                        ->image()
                        ->multiple()
                        ->directory('foto-produk')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                ]),
            Section::make('Harga Saat Ini')
                ->columns(1)
                ->schema([
                    TextInput::make('harga_dealer')
                        ->label('Harga Dealer')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(','),
                    TextInput::make('harga_ecommerce')
                        ->label('Harga E-commerce')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(','),
                    TextInput::make('harga_retail')
                        ->label('Harga Retail')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(','),
                ]),
            Section::make('Stok')
                ->columns(2)
                ->schema([
                    TextInput::make('stok_awal')
                        ->label('Stok Awal')
                        ->numeric()
                        ->suffix('unit'),
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
            Section::make('Deskripsi Unit')
                ->schema([
                    RichEditor::make('keterangan')
                        ->label('Keterangan'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
