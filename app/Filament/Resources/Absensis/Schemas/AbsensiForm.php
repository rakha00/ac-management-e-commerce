<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->relationship('karyawan', 'id')
                    ->required(),
                DatePicker::make('tanggal')
                    ->required(),
                DateTimePicker::make('waktu_absen')
                    ->required(),
                Toggle::make('telat')
                    ->required(),
                TextInput::make('keterangan'),
                Toggle::make('terkonfirmasi')
                    ->required(),
                TextInput::make('dikonfirmasi_oleh')
                    ->numeric(),
                DateTimePicker::make('waktu_konfirmasi'),
            ]);
    }
}
