<?php

namespace App\Filament\Resources\PiutangJasa\Schemas;

use App\Models\TransaksiJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;

class PiutangJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Piutang Jasa')
                    ->columnSpanFull()
                    ->schema([
                        // Pilih Kode Jasa dari Transaksi Jasa; auto-fill totals on selection
                        Select::make('transaksi_jasa_id')
                            ->label('Kode Jasa')
                            ->relationship('transaksiJasa', 'kode_jasa')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required()
                            ->dehydrated() // persist value even when disabled
                            ->disabled(function (?Model $record): bool {
                                // Make readonly on edit
                                return (bool) $record;
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('total_piutang', null);
                                    $set('sisa_piutang', null);

                                    return;
                                }

                                $trx = TransaksiJasa::query()
                                    ->where('id', $state)
                                    ->first();

                                if (! $trx) {
                                    return;
                                }

                                // Use total_pendapatan_jasa as the receivable amount
                                $total = (int) round((float) ($trx->total_pendapatan_jasa ?? 0));
                                // Auto-fill total_piutang and sisa_piutang from selected kode jasa
                                $set('total_piutang', number_format($total, 0, '.', ','));
                                $set('sisa_piutang', number_format($total, 0, '.', ','));
                                $set('status_pembayaran', 'belum lunas');
                            }),

                        TextInput::make('total_piutang')
                            ->label('Total Piutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled() // auto-filled
                            ->dehydrated() // still saved to model
                            ->required(),

                        TextInput::make('sisa_piutang')
                            ->label('Sisa Piutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->disabled() // readonly display only
                            ->dehydrated(false) // do not persist; not a column
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set, callable $get) {
                                if ($record) {
                                    $totalCicilan = (int) $record->piutangJasaCicilanDetail()->sum('nominal_cicilan');
                                    $sisa = max((int) ($record->total_piutang ?? 0) - $totalCicilan, 0);
                                    $set('sisa_piutang', $sisa ?? null);
                                } else {
                                    // For new records, initialize sisa_piutang with total_piutang
                                    $totalPiutangValue = $get('total_piutang');
                                    $set('sisa_piutang', $totalPiutangValue === null ? null : (int) $totalPiutangValue);
                                }
                            }),

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
