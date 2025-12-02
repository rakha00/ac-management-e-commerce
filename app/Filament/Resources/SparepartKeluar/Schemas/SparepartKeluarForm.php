<?php

namespace App\Filament\Resources\SparepartKeluar\Schemas;

use App\Models\SparepartKeluar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SparepartKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sparepart Keluar')
                    ->columns(3)
                    ->schema([

                        DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->required()
                            ->live()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->afterStateUpdated(function (callable $set, $state) {
                                if (! $state) {
                                    $set('nomor_invoice', null);

                                    return;
                                }

                                $set('nomor_invoice', SparepartKeluar::generateSequentialNumber((string) $state));
                            }),

                        TextInput::make('nomor_invoice')
                            ->label('Nomor Invoice')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(),

                        Select::make('konsumen_id')
                            ->label('Konsumen')
                            ->relationship('konsumen', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull()
                            ->disabled(fn (string $operation) => $operation === 'edit'),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
