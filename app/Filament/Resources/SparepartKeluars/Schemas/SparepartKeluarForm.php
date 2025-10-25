<?php

namespace App\Filament\Resources\SparepartKeluars\Schemas;

use App\Models\SparepartKeluar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Sparepart Keluar form schema.
 * Fields:
 * - Tanggal Keluar (required)
 * - Konsumen (Select from konsumen_spareparts, required)
 * - Nomor Invoice (auto-generated preview per tanggal, unique & sequential, prefix INVSP)
 * - Keterangan (optional)
 * - Detail Sparepart Keluar (multiple with relationship)
 */
class SparepartKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sparepart Keluar')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->required()
                            ->reactive(),

                        Select::make('konsumen_sparepart_id')
                            ->label('Konsumen')
                            ->options(fn() => \App\Models\KonsumenSparepart::query()
                                ->orderBy('nama_konsumen')
                                ->pluck('nama_konsumen', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Read-only preview; value is auto-generated in model creating hook
                        TextInput::make('nomor_invoice')
                            ->label('Nomor Invoice')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn($state, callable $get) => SparepartKeluar::generateSequentialNumber(
                                (string) ($get('tanggal_keluar') ?? now()->toDateString()),
                                'INVSP'
                            )),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Detail Sparepart Keluar')
                    ->schema([
                        Repeater::make('detailSparepartKeluar')
                            ->relationship('detailSparepartKeluar')
                            ->label('Detail')
                            ->columns(3)
                            ->addActionLabel('Tambah Baris')
                            ->defaultItems(0)
                            ->schema([
                                Select::make('sparepart_id')
                                    ->label('Kode Sparepart')
                                    ->options(fn() => \App\Models\Sparepart::query()
                                        ->orderBy('kode_sparepart')
                                        ->pluck('kode_sparepart', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $sp = \App\Models\Sparepart::find($state);
                                        if ($sp) {
                                            $set('nama_sparepart', $sp->nama_sparepart);
                                            // Auto fill modal price from master as default for convenience
                                            $set('harga_modal', $sp->harga_modal);
                                        } else {
                                            $set('nama_sparepart', null);
                                            $set('harga_modal', null);
                                        }
                                    }),

                                TextInput::make('nama_sparepart')
                                    ->label('Nama Sparepart')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('jumlah_keluar')
                                    ->label('Jumlah Keluar')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),

                                TextInput::make('harga_modal')
                                    ->label('Harga Modal (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->required(),

                                TextInput::make('harga_jual')
                                    ->label('Harga Jual (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->required(),

                                TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->nullable()
                                    ->columnSpan(3),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
