<?php

namespace App\Filament\Resources\PiutangProduk\Schemas;

use App\Models\PiutangProduk;
use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class PiutangProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Piutang Produk')
                    ->schema([
                        Select::make('transaksi_produk_id')
                            ->label('No Invoice')
                            ->relationship('transaksiProduk', 'nomor_invoice')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $trx = TransaksiProduk::where('id', $state)->first();

                                if (! $trx) {
                                    return;
                                }

                                $total = PiutangProduk::calculateTotalPiutang($trx->id);

                                $set('total_piutang', number_format($total));
                                $set('sisa_piutang', number_format($total));
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
                    ])
                    ->columnSpanFull(),
                Section::make('Pembayaran')
                    ->schema([
                        Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum lunas' => 'Belum Lunas',
                                'tercicil' => 'Tercicil',
                                'sudah lunas' => 'Sudah Lunas',
                            ])
                            ->disabled()
                            ->dehydrated()
                            ->default('belum lunas'),
                        DatePicker::make('jatuh_tempo')
                            ->label('Jatuh Tempo')
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
