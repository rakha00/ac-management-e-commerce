<?php

namespace App\Filament\Resources\Laporan\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LaporanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Laporan')
                    ->schema([
                        DateTimePicker::make('tanggal')
                            ->label('Tanggal & Waktu')
                            ->required(),

                        TextInput::make('nama_konsumen')
                            ->label('Nama Konsumen')
                            ->required()
                            ->maxLength(255),

                        Select::make('jenis_pengerjaan')
                            ->label('Jenis Pengerjaan')
                            ->options([
                                'Perawatan Rutin' => 'Perawatan Rutin',
                                'Perbaikan' => 'Perbaikan',
                                'Instalasi' => 'Instalasi',
                                'Pengisian Freon' => 'Pengisian Freon',
                                'Lain-lain' => 'Lain-lain',
                            ])
                            ->required()
                            ->native(false),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Lampiran')
                    ->schema([
                        FileUpload::make('foto_pengerjaan')
                            ->label('Foto Pengerjaan')
                            ->image()
                            ->directory('laporan-foto')
                            ->visibility('public')
                            ->maxSize(3072)
                            ->downloadable()
                            ->previewable()
                            ->required(),
                    ]),
            ]);
    }
}
