<?php

namespace App\Filament\Resources\SparepartMasuks\Schemas;

use App\Models\SparepartMasuk;
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
 * Sparepart Masuk form schema.
 * Fields:
 * - Tanggal Masuk (required)
 * - Distributor (Select from distributor_spareparts, required)
 * - Nomor Sparepart Masuk (auto-generated preview per tanggal, unique & sequential)
 * - Keterangan (optional)
 * - Detail Sparepart Masuk (multiple with relationship)
 */
class SparepartMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sparepart Masuk')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->required()
                            ->reactive(),

                        Select::make('distributor_sparepart_id')
                            ->label('Distributor')
                            ->options(fn() => \App\Models\DistributorSparepart::query()
                                ->orderBy('nama_distributor')
                                ->pluck('nama_distributor', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Read-only preview; value is auto-generated in model creating hook
                        TextInput::make('nomor_sparepart_masuk')
                            ->label('Nomor Sparepart Masuk')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn($state, callable $get) => SparepartMasuk::generateSequentialNumber(
                                (string) ($get('tanggal_masuk') ?? now()->toDateString()),
                                'SM'
                            )),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Detail Sparepart Masuk')
                    ->schema([
                        Repeater::make('detailSparepartMasuk')
                            ->relationship('detailSparepartMasuk')
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
                                        } else {
                                            $set('nama_sparepart', null);
                                        }
                                    }),

                                TextInput::make('nama_sparepart')
                                    ->label('Nama Sparepart')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('jumlah_masuk')
                                    ->label('Jumlah Masuk')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
