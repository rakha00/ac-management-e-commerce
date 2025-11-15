<?php

namespace App\Filament\Resources\HutangProduk\Schemas;

use App\Models\BarangMasuk;
use App\Models\HutangProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

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
                            ->live()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $bm = BarangMasuk::where('id', $state)->first();

                                if (! $bm) {
                                    return;
                                }

                                $total = HutangProduk::calculateTotalHutang($bm->id);

                                $set('total_hutang', number_format($total));
                                $set('sisa_hutang', number_format($total));
                            }),
                        TextInput::make('total_hutang')
                            ->label('Total Hutang')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('sisa_hutang')
                            ->label('Sisa Hutang')
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
