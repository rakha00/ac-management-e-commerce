<?php

namespace App\Filament\Resources\HutangProduks\Schemas;

use App\Models\BarangMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                        Select::make('no_unit_masuk')
                            ->label('Nomor Barang Masuk')
                            ->options(fn () => BarangMasuk::query()
                                ->orderByDesc('tanggal')
                                ->pluck('nomor_barang_masuk', 'nomor_barang_masuk')
                                ->toArray())
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
                                    ->with('principle')
                                    ->where('nomor_barang_masuk', $state)
                                    ->first();

                                if (! $bm) {
                                    return;
                                }

                                // Auto-fill nama_principle (string)
                                $set('nama_principle', $bm->principle?->nama);

                                // Calculate total_hutang from BarangMasukDetail * UnitAC.harga_dealer
                                $total = (int) DB::table('barang_masuk_details')
                                    ->join('unit_a_c_s', 'barang_masuk_details.unit_ac_id', '=', 'unit_a_c_s.id')
                                    ->where('barang_masuk_details.barang_masuk_id', '=', $bm->id)
                                    ->sum(DB::raw('barang_masuk_details.jumlah_barang_masuk * unit_a_c_s.harga_dealer'));

                                $set('total_hutang', $total);
                                // Set initial remaining debt equals to total (no installments yet on create)
                                $set('sisa_hutang', $total);
                            }),
                        TextInput::make('total_hutang')
                            ->label('Total Hutang')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // auto-filled
                            ->dehydrated() // still saved to model
                            ->required(),
                        TextInput::make('sisa_hutang')
                            ->label('Sisa Hutang')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // readonly display only
                            ->dehydrated(false) // do not persist; not a column
                            ->required()
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record) {
                                    $totalCicilan = (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan');
                                    $sisa = max((int) ($record->total_hutang ?? 0) - $totalCicilan, 0);
                                    $set('sisa_hutang', $sisa);
                                }
                            }),
                        TextInput::make('nama_principle')
                            ->label('Nama Principle')
                            ->disabled() // readonly as requested
                            ->dehydrated() // still saved to model
                            ->required(),
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
