<?php

namespace App\Filament\Resources\BarangMasuk\Schemas;

use App\Models\BarangMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class BarangMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi barang masuk')
                    ->schema([
                        Select::make('principal_id')
                            ->label('Principal')
                            ->relationship('principal', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('nomor_barang_masuk', BarangMasuk::generateNomorBarangMasuk($state));
                                }
                            })
                            ->disabled(fn (?Model $record): bool => $record !== null),
                        TextInput::make('nomor_barang_masuk')
                            ->label('Nomor Barang Masuk')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record && $record->nomor_barang_masuk) {
                                    $set('nomor_barang_masuk', $record->nomor_barang_masuk);
                                }
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
