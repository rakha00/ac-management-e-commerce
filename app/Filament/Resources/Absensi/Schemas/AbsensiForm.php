<?php

namespace App\Filament\Resources\Absensi\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Absensi')
                    ->columns(2)
                    ->schema([
                        Select::make('karyawan_id')
                            ->relationship('karyawan', 'nama')
                            ->required()
                            ->disabled(fn ($record) => filled($record)),
                        DateTimePicker::make('waktu_absen')
                            ->required(),
                    ]),
                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_telat')
                            ->label('Telat')
                            ->required(),
                        Select::make('keterangan')
                            ->label('Keterangan')
                            ->options([
                                'Telat' => 'Telat',
                                'Tepat waktu' => 'Tepat waktu',
                            ])
                            ->native(false),
                    ]),
                Section::make('Konfirmasi')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_terkonfirmasi')
                            ->label('Terkonfirmasi')
                            ->required(),
                        TextInput::make('dikonfirmasi_oleh_id')
                            ->label('Dikonfirmasi Oleh')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, $record) => optional($record?->dikonfirmasiOleh)->name ?? '—'),
                        DateTimePicker::make('dikonfirmasi_pada')
                            ->label('Waktu Konfirmasi'),
                    ]),
            ]);
    }
}
