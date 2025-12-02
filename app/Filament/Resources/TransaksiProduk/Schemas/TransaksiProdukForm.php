<?php

namespace App\Filament\Resources\TransaksiProduk\Schemas;

use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi Produk')
                    ->schema([

                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Transaksi')
                            ->live()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('nomor_invoice', null);
                                    $set('nomor_surat_jalan', null);

                                    return;
                                }

                                $set('nomor_invoice', TransaksiProduk::generateNomorInvoice($state));
                                $set('nomor_surat_jalan', TransaksiProduk::generateNomorSuratJalan($state));
                            })
                            ->required(),

                        TextInput::make('nomor_invoice')
                            ->label('Nomor Invoice')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(),

                        TextInput::make('nomor_surat_jalan')
                            ->label('Nomor Surat Jalan')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated(),

                        Select::make('sales_karyawan_id')
                            ->label('Sales')
                            ->relationship('salesKaryawan', 'nama')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),

                        Select::make('konsumen_id')
                            ->label('Toko/Konsumen')
                            ->relationship('konsumen', 'nama')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->disabled(fn (string $operation) => $operation === 'edit'),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
