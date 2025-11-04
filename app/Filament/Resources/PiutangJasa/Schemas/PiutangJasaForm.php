<?php

namespace App\Filament\Resources\PiutangJasa\Schemas;

use App\Models\PiutangJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PiutangJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Piutang Jasa')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('transaksi_jasa_id')
                            ->label('Kode Jasa')
                            ->relationship('transaksiJasa', 'kode_jasa')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->dehydrated()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $total = PiutangJasa::calculateTotalPiutang($state);

                                $set('total_piutang', number_format($total));
                                $set('sisa_piutang', number_format($total));
                                $set('status_pembayaran', 'belum lunas');
                            }),

                        TextInput::make('total_piutang')
                            ->label('Total Piutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('sisa_piutang')
                            ->label('Sisa Piutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled()
                            ->dehydrated(),

                        Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum lunas' => 'Belum Lunas',
                                'tercicil' => 'Tercicil',
                                'sudah lunas' => 'Sudah Lunas',
                            ])
                            ->native(false)
                            ->default('belum lunas')
                            ->required(),

                        DatePicker::make('jatuh_tempo')
                            ->label('Jatuh Tempo')
                            ->required(),
                    ]),
                Section::make('Informasi Tambahan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
