<?php

namespace App\Filament\Resources\HutangProduk\Schemas;

use App\Models\BarangMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HutangProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaksi')
                    ->schema([
                        Select::make('barang_masuk_id')
                            ->label('Nomor Barang Masuk')
                            ->relationship('barangMasuk', 'nomor_barang_masuk')
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
                                    return;
                                }

                                $bm = BarangMasuk::query()
                                    ->with('principal')
                                    ->where('id', $state)
                                    ->first();

                                if (! $bm) {
                                    return;
                                }

                                // Calculate total_hutang from BarangMasukDetail * UnitAC.harga_dealer
                                $total = (int) DB::table('barang_masuk_detail')
                                    ->join('unit_ac', 'barang_masuk_detail.unit_ac_id', '=', 'unit_ac.id')
                                    ->where('barang_masuk_detail.barang_masuk_id', '=', $bm->id)
                                    ->sum(DB::raw('barang_masuk_detail.jumlah_barang_masuk * unit_ac.harga_dealer'));

                                $set('total_hutang', number_format($total, 0, ',', ','));
                                // Set initial remaining debt equals to total (no installments yet on create)
                                $set('sisa_hutang', number_format($total, 0, ',', ','));
                            }),
                        TextInput::make('total_hutang')
                            ->label('Total Hutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled() // auto-filled
                            ->dehydrated() // still saved to model
                            ->required(),
                        TextInput::make('sisa_hutang')
                            ->label('Sisa Hutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->disabled() // readonly display only
                            ->dehydrated(false) // do not persist; not a column
                            ->required()
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record) {
                                    $totalCicilan = (int) $record->hutangProdukCicilanDetail()->sum('nominal_cicilan');
                                    $sisa = max((int) ($record->total_hutang ?? 0) - $totalCicilan, 0);
                                    $set('sisa_hutang', $sisa);
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
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
