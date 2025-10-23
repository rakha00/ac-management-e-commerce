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
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),

            Section::make('Pemasukan')
                ->schema([
                    TextInput::make('pemasukan')
                        ->label('Jumlah Pemasukan')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(','),
                    TextInput::make('keterangan_pemasukan')
                        ->label('Keterangan Pemasukan')
                        ->maxLength(255),
                ])
                ->collapsed(),

            Section::make('Pengeluaran')
                ->schema([
                    TextInput::make('pengeluaran')
                        ->label('Jumlah Pengeluaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state)),
                    TextInput::make('keterangan_pengeluaran')
                        ->label('Keterangan Pengeluaran')
                        ->maxLength(255),
                ])
                ->collapsed(),

            FileUpload::make('bukti_pembayaran')
                ->label('Bukti Pembayaran (Foto)')
                ->image()
                ->directory('bukti-petty-cash')
                ->maxSize(2048)
                ->downloadable()
                ->previewable(),
        ]);
    }
}
