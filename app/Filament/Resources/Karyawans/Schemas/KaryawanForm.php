<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('jabatan')
                    ->required(),
                TextInput::make('no_hp')
                    ->required(),
                TextInput::make('gaji_pokok')
                    ->required()
                    ->numeric(),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('foto_ktp')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('dokumen_tambahan')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('kontak_darurat_serumah')
                    ->required(),
                TextInput::make('kontak_darurat_tidak_serumah')
                    ->required(),
                Toggle::make('status_aktif')
                    ->required(),
                DatePicker::make('tanggal_terakhir_aktif'),
            ]);
    }
}
