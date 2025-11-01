<?php

namespace App\Filament\Resources\TransaksiJasa\Schemas;

use App\Models\Karyawan;
use App\Models\Konsumen;
use App\Models\TransaksiJasa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class TransaksiJasaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([
                        // Date controls the sequential code generation.
                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->disabled(fn (?Model $record) => $record !== null)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('kode_jasa', $state ? TransaksiJasa::generateSequentialNumber((string) $state, 'KJ') : null);
                            })
                            ->live(),

                        // Read-only preview; actual value is generated on save in the model.
                        TextInput::make('kode_jasa')
                            ->label('Kode Jasa')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(null),

                        Select::make('teknisi_karyawan_id')
                            ->label('Teknisi')
                            ->options(fn () => Karyawan::query()
                                ->where('jabatan', 'teknisi')
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->toArray())
                            ->searchable()
                            ->nullable(),

                        Select::make('helper_karyawan_id')
                            ->label('Helper')
                            ->options(fn () => Karyawan::query()
                                ->where('jabatan', 'helper')
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->toArray())
                            ->searchable()
                            ->nullable(),

                        Select::make('konsumen_id')
                            ->label('Nama Konsumen')
                            ->options(fn () => Konsumen::query()
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->toArray())
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Section::make('Detail Transaksi')
                    ->schema([
                        TextInput::make('garansi_hari')
                            ->label('Garansi (hari)')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                    ]),
            ]);
    }
}
