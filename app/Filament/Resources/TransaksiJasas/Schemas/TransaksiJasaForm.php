<?php

namespace App\Filament\Resources\TransaksiJasas\Schemas;

use App\Models\Karyawan;
use App\Models\TransaksiJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransaksiJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Date controls the sequential code generation.
                DatePicker::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->required()
                    ->reactive(),

                // Read-only preview; actual value is generated on save in the model.
                TextInput::make('kode_jasa')
                    ->label('Kode Jasa')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn($state, callable $get) => TransaksiJasa::generateSequentialNumber(
                        (string) ($get('tanggal_transaksi') ?? now()->toDateString()),
                        'KJ'
                    )),

                // teknisi diambil dari karyawan.jabatan = 'teknisi'
                Select::make('teknisi_karyawan_id')
                    ->label('Teknisi')
                    ->options(fn() => Karyawan::query()
                        ->where('jabatan', 'teknisi')
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->toArray())
                    ->searchable()
                    ->nullable(),

                // helper diambil dari karyawan.jabatan = 'helper'
                Select::make('helper_karyawan_id')
                    ->label('Helper')
                    ->options(fn() => Karyawan::query()
                        ->where('jabatan', 'helper')
                        ->orderBy('nama')
                        ->pluck('nama', 'id')
                        ->toArray())
                    ->searchable()
                    ->nullable(),

                // Toko/Konsumen sebagai string biasa
                TextInput::make('nama_konsumen')
                    ->label('Nama Konsumen')
                    ->required(),

                TextInput::make('garansi_hari')
                    ->label('Garansi (hari)')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
            ])
            ->columns(2);
    }
}
