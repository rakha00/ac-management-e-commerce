<?php

namespace App\Filament\Resources\TransaksiProduk\Schemas;

use App\Models\Karyawan;
use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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
                            ->required()
                            ->disabled(fn (?Model $record) => $record !== null)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('nomor_invoice', $state ? TransaksiProduk::generateNomorInvoice((string) $state) : null);
                                $set('nomor_surat_jalan', $state ? TransaksiProduk::generateNomorSuratJalan((string) $state) : null);
                            }),
                        TextInput::make('nomor_invoice')
                            ->label('Nomor Invoice')
                            ->required()
                            ->disabled()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record && $record->nomor_invoice) {
                                    $set('nomor_invoice', $record->nomor_invoice);
                                }
                            }),
                        TextInput::make('nomor_surat_jalan')
                            ->label('Nomor Surat Jalan')
                            ->required()
                            ->disabled()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                                if ($record && $record->nomor_surat_jalan) {
                                    $set('nomor_surat_jalan', $record->nomor_surat_jalan);
                                }
                            }),
                        Select::make('sales_karyawan_id')
                            ->label('Sales')
                            ->options(fn () => Karyawan::query()
                                ->where('jabatan', 'sales')
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->toArray())
                            ->searchable()
                            ->reactive(),
                        TextInput::make('toko_konsumen')
                            ->label('Toko/Konsumen')
                            ->required(),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
