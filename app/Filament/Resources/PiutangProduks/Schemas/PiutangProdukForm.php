<?php

namespace App\Filament\Resources\PiutangProduks\Schemas;

use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PiutangProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Select invoice from TransaksiProduk, auto-fill totals when selected
                Select::make('no_invoice')
                    ->label('No Invoice')
                    ->options(fn() => TransaksiProduk::query()
                        ->orderBy('tanggal_transaksi', 'desc')
                        ->pluck('nomor_invoice', 'nomor_invoice')
                        ->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $trx = TransaksiProduk::query()
                            ->where('nomor_invoice', $state)
                            ->first();
                        if ($trx) {
                            $total = (int) round((float) ($trx->total_penjualan ?? 0));
                            // Auto-fill total_piutang and sisa_piutang from selected invoice
                            $set('total_piutang', $total);
                            $set('sisa_piutang', $total);
                            $set('status_pembayaran', 'belum lunas');
                        }
                    })
                    ->required(),

                TextInput::make('total_piutang')
                    ->label('Total Piutang')
                    ->numeric()
                    ->required(),

                TextInput::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->numeric()
                    ->required(),

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

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
