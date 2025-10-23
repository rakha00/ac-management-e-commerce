<?php

namespace App\Filament\Resources\PengeluaranKantors\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class PengeluaranKantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),

            Section::make('Detail Pengeluaran')
                ->schema([
                    TextInput::make('pengeluaran')
                        ->label('Jumlah Pengeluaran')
                        ->numeric()
                        ->prefix('Rp')
                        ->mask(fn ($mask) => $mask
                            ->numeric()
                            ->thousandsSeparator(',')
                        )
                        ->required(),

                    TextInput::make('keterangan_pengeluaran')
                        ->label('Keterangan')
                        ->maxLength(255)
                        ->required(),
                ]),

            FileUpload::make('bukti_pembayaran')
                ->label('Bukti Pembayaran (Foto)')
                ->image()
                ->directory('bukti-pengeluaran-kantor')
                ->maxSize(2048)
                ->downloadable()
                ->previewable(),
        ]);
    }
}
