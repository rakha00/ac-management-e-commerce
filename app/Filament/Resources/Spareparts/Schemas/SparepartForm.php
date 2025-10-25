<?php

namespace App\Filament\Resources\Spareparts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Sparepart master form schema.
 * Fields: Kode Sparepart, Nama Sparepart, Harga Modal, Stock Awal, Keterangan.
 * Note: stock_masuk / stock_keluar / stock_akhir are maintained by the system and not editable here.
 */
class SparepartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sparepart')
                    ->columns(2)
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

                        TextInput::make('harga_modal')
                            ->label('Harga Modal')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),

                        TextInput::make('stock_awal')
                            ->label('Stock Awal')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ]),
                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }
}
