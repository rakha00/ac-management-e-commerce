<?php

namespace App\Filament\Resources\Spareparts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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
                            ->unique(ignoreRecord: true),

                        TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->required(),
                    ]),
                Section::make('Foto Sparepart')
                    ->schema([
                        FileUpload::make('path_foto_sparepart')
                            ->label('Foto Sparepart')
                            ->image()
                            ->disk('public')
                            ->multiple()
                            ->directory('foto-sparepart')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    ]),
                Section::make('Harga Saat Ini')
                    ->columns(1)
                    ->schema([
                        TextInput::make('harga_modal')
                            ->label('Harga Modal')
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
                Section::make('Deskripsi Sparepart')
                    ->schema([
                        RichEditor::make('keterangan')
                            ->label('Keterangan'),
                    ]),
            ])
            ->columns(1);
    }
}
