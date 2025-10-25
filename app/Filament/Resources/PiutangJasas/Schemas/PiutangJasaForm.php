<?php

namespace App\Filament\Resources\PiutangJasas\Schemas;

use App\Models\TransaksiJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PiutangJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Pilih Kode Jasa dari Transaksi Jasa; auto-fill totals on selection
                Select::make('no_kode_jasa')
                    ->label('Kode Jasa')
                    ->options(fn() => TransaksiJasa::query()
                        ->orderBy('tanggal_transaksi', 'desc')
                        ->pluck('kode_jasa', 'kode_jasa')
                        ->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $trx = TransaksiJasa::query()
                            ->where('kode_jasa', $state)
                            ->first();

                        if ($trx) {
                            // Use total_pendapatan_jasa as the receivable amount
                            $total = (int) round((float) ($trx->total_pendapatan_jasa ?? 0));
                            // Auto-fill total_piutang and sisa_piutang from selected kode jasa
                            $set('total_piutang', $total);
                            $set('sisa_piutang', $total);
                            $set('status_pembayaran', 'belum lunas');
                        }
                    })
                    ->required(),

                TextInput::make('total_piutang')
                    ->label('Total Piutang (Rp)')
                    ->required()
                    ->numeric(),

                TextInput::make('sisa_piutang')
                    ->label('Sisa Piutang (Rp)')
                    ->required()
                    ->numeric(),

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
