<?php

namespace App\Filament\Resources\PengeluaranKantor\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PengeluaranKantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detail Pengeluaran')
                ->columns(2)
                ->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->required(),
                    TextInput::make('pengeluaran')
                        ->label('Jumlah Pengeluaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->placeholder('0')
                        ->required(),
                    TextInput::make('keterangan_pengeluaran')
                        ->label('Keterangan')
                        ->maxLength(255)
                        ->required()
                        ->columnSpanFull(),
                ]),
            Section::make('Lampiran')
                ->columns(1)
                ->schema([
                    FileUpload::make('path_bukti_pembayaran')
                        ->label('Bukti Pembayaran (Foto)')
                        ->image()
                        ->directory('bukti-pengeluaran-kantor')
                        ->maxSize(2048)
                        ->downloadable()
                        ->previewable()
                        ->helperText('Format: JPG/PNG/WebP, maks 2MB')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
