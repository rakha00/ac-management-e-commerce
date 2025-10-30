<?php

namespace App\Filament\Resources\PettyCashes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PettyCashForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Pemasukan')
                ->schema([
                    TextInput::make('pemasukan')
                        ->label('Jumlah Pemasukan')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->placeholder('0'),
                    TextInput::make('keterangan_pemasukan')
                        ->label('Keterangan Pemasukan')
                        ->placeholder('Opsional')
                        ->maxLength(255),
                ])
                ->columns(2),
            Section::make('Pengeluaran')
                ->schema([
                    TextInput::make('pengeluaran')
                        ->label('Jumlah Pengeluaran')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->placeholder('0'),
                    TextInput::make('keterangan_pengeluaran')
                        ->label('Keterangan Pengeluaran')
                        ->placeholder('Opsional')
                        ->maxLength(255),
                ])
                ->columns(2),
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),
            FileUpload::make('path_bukti_pembayaran')
                ->label('Bukti Pembayaran (Foto)')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->directory('bukti-petty-cash')
                ->maxSize(2048)
                ->downloadable()
                ->previewable()
                ->helperText('Format: JPG/PNG/WebP, maks 2MB'),
        ]);
    }
}
