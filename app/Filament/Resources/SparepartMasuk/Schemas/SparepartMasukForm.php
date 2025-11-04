<?php

namespace App\Filament\Resources\SparepartMasuk\Schemas;

use App\Models\SparepartMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

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
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $set('nomor_sparepart_masuk', SparepartMasuk::generateSequentialNumber((string) $state));
                                }
                            })
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Select::make('distributor_sparepart_id')
                            ->label('Distributor')
                            ->relationship('distributor', 'nama_distributor')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('nomor_sparepart_masuk')
                            ->label('Nomor Sparepart Masuk')
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
