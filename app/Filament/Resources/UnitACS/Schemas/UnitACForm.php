<?php

namespace App\Filament\Resources\UnitACS\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\FormSchema;
use Filament\Schemas\Components\Section;

class UnitACForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Unit')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('nama_merk')
                        ->label('Nama Merk')
                        ->required(),
                ]),

            Section::make('Foto Produk')
                ->schema([
                    Forms\Components\FileUpload::make('foto_produk')
                        ->label('Foto Produk')
                        ->image()
                        ->directory('foto-produk')
                        ->imagePreviewHeight('150px')
                        ->nullable(),
                ]),

            Section::make('Harga')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('harga_dealer')
                        ->label('Harga Dealer')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\TextInput::make('harga_ecommerce')
                        ->label('Harga E-commerce')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\TextInput::make('harga_retail')
                        ->label('Harga Retail')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                ]),

            Section::make('Stok')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('stock_awal')
                        ->label('Stock Awal')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Forms\Components\TextInput::make('stock_akhir')
                        ->label('Stock Akhir')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Forms\Components\TextInput::make('stock_masuk')
                        ->label('Stock Masuk')
                        ->numeric()
                        ->default(0),

                    Forms\Components\TextInput::make('stock_keluar')
                        ->label('Stock Keluar')
                        ->numeric()
                        ->default(0),
                ]),

            Section::make('Keterangan')
                ->schema([
                    Forms\Components\Textarea::make('remarks')
                        ->label('Remarks')
                        ->rows(3)
                        ->nullable(),
                ]),
        ]);
    }
}
