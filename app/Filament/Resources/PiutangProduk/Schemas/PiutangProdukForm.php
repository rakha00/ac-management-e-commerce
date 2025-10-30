<?php

namespace App\Filament\Resources\PiutangProduk\Schemas;

use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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
                            ->options(fn () => TransaksiProduk::query()
                                ->orderBy('tanggal_transaksi', 'desc')
                                ->pluck('nomor_invoice', 'id')
                                ->toArray())
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $trx = TransaksiProduk::find($state);
                                if ($trx && $trx->transaksiProdukDetail) {
                                    // Calculate total_penjualan from transaction details
                                    $total = 0;
                                    foreach ($trx->transaksiProdukDetail as $detail) {
                                        $total += ($detail->harga_jual ?? 0) * ($detail->jumlah_keluar ?? 0);
                                    }
                                    $total = (int) $total;
                                    // Auto-fill total_piutang from selected invoice
                                    $set('total_piutang', number_format($total, 0, '', ','));
                                    $set('sisa_piutang', number_format($total, 0, '', ','));
                                    $set('status_pembayaran', 'belum lunas');
                                }
                            })
                            ->required(),
                        TextInput::make('total_piutang')
                            ->label('Total Piutang')
                            ->prefix('Rp')
                            ->stripCharacters(',')
                            ->disabled() // auto-filled
                            ->dehydrated()
                            ->required(),
                        TextInput::make('sisa_piutang')
                            ->label('Sisa Piutang')
                            ->prefix('Rp')
                            ->disabled() // readonly display only
                            ->dehydrated(false) // do not persist; not a column
                            ->required()
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record) {
                                    $totalCicilan = (int) $record->piutangProdukCicilanDetail()->sum('nominal_cicilan');
                                    $sisa = max((int) ($record->total_piutang ?? 0) - $totalCicilan, 0);
                                    $set('sisa_piutang', number_format($sisa, 0));
                                }
                            }),
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
                            ->native(false)
                            ->default('belum lunas')
                            ->required(),
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
