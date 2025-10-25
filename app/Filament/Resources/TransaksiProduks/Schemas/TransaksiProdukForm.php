<?php

namespace App\Filament\Resources\TransaksiProduks\Schemas;

use App\Models\Karyawan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransaksiProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_transaksi')
                    ->required(),

                Select::make('sales_karyawan_id')
                    ->label('Sales')
                    ->options(fn() => Karyawan::query()
                        ->where('jabatan', 'sales')
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->toArray())
                    ->searchable(),

                TextInput::make('toko_konsumen')
                    ->required(),

                // Read-only previews; values are auto-generated in model creating hook
                TextInput::make('nomor_invoice')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('nomor_surat_jalan')
                    ->disabled()
                    ->dehydrated(false),

                Textarea::make('keterangan')
                    ->nullable(),
            ])
            ->columns(2);
    }
}
