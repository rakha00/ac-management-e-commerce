<?php

namespace App\Filament\Resources\BarangMasuks\Schemas;

use App\Models\BarangMasuk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BarangMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('principle_id')
                    ->label('Principle')
                    ->relationship('principle', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if (! $state) {
                            return;
                        }

                        $tanggal = Carbon::parse($state);
                        $d = $tanggal->format('dmY');

                        $lastNumber = BarangMasuk::whereDate('tanggal', $tanggal)
                            ->get()
                            ->map(function ($item) {
                                if (preg_match('/-(\d+)$/', $item->no_invoice, $matches)) {
                                    return (int) $matches[1];
                                }

                                return 0;
                            })
                            ->max();

                        $newNumber = $lastNumber + 1;

                        $set('nomor_barang_masuk', "BM/{$d}-{$newNumber}");
                    }),

                TextInput::make('nomor_barang_masuk')
                    ->label('Nomor Barang Masuk')
                    ->disabled()
                    ->dehydrated(true)
                    ->afterStateHydrated(function ($component, ?Model $record, callable $set) {
                        // Saat edit, tampilkan nomor yang sudah ada
                        if ($record && $record->nomor_barang_masuk) {
                            $set('nomor_barang_masuk', $record->nomor_barang_masuk);
                        }
                    })
                    ->formatStateUsing(function ($state, callable $get, ?Model $record) {
                        // Generate otomatis kalau belum ada
                        if ($state) {
                            return $state;
                        }

                        $tanggal = $get('tanggal') ?? now()->format('Ymd');
                        $nextId = ($record?->id ?? \App\Models\BarangMasuk::max('id') + 1);

                        return 'BM-'.str_replace('-', '', $tanggal).'-'.$nextId;
                    }),
            ]);
    }
}
