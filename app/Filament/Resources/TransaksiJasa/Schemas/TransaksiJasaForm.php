<?php

namespace App\Filament\Resources\TransaksiJasa\Schemas;

use App\Models\TransaksiJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([

                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->live()
                            ->disabled(function (string $context) {
                                return $context === 'edit'
                                    && auth()->user()->karyawan->jabatan !== 'admin';
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('kode_jasa', $state ? TransaksiJasa::generateSequentialNumber((string) $state, 'kode_jasa', 'KJ') : null);
                                $set('nomor_invoice_jasa', $state ? TransaksiJasa::generateSequentialNumber((string) $state, 'nomor_invoice_jasa', 'INV-TJ') : null);
                                $set('nomor_surat_jalan_jasa', $state ? TransaksiJasa::generateSequentialNumber((string) $state, 'nomor_surat_jalan_jasa', 'SJ-TJ') : null);
                            }),

                        TextInput::make('kode_jasa')
                            ->label('Kode Jasa')
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            )
                            ->dehydrated(),

                        TextInput::make('nomor_invoice_jasa')
                            ->label('Nomor Invoice Jasa')
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            )
                            ->dehydrated(),

                        TextInput::make('nomor_surat_jalan_jasa')
                            ->label('Nomor Surat Jalan Jasa')
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            )
                            ->dehydrated(),

                        Select::make('teknisi_karyawan_id')
                            ->label('Teknisi')
                            ->relationship('teknisi', 'nama')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            ),

                        Select::make('helper_karyawan_id')
                            ->label('Helper')
                            ->relationship('helper', 'nama')
                            ->preload()
                            ->searchable()
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            ),

                        Select::make('konsumen_id')
                            ->label('Nama Konsumen')
                            ->relationship('konsumen', 'nama')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            ),

                    ])->columns(2),
                Section::make('Detail Transaksi')
                    ->schema([

                        TextInput::make('garansi_hari')
                            ->label('Garansi (hari)')
                            ->numeric()
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            ),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->disabled(
                                fn (string $context) => $context === 'edit'
                                && auth()->user()->karyawan->jabatan !== 'admin'
                            ),

                    ]),

            ]);
    }
}
