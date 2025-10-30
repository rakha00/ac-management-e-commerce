<?php

namespace App\Filament\Resources\UnitAC\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UnitACTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_produk')
                    ->label('Foto')
                    ->size(50),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('harga_dealer')
                    ->label('Harga Dealer')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('harga_retail')
                    ->label('Harga Retail')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('stok_awal')
                    ->label('Awal')
                    ->sortable(),
                TextColumn::make('stok_masuk')
                    ->label('Masuk')
                    ->sortable(),
                TextColumn::make('stok_keluar')
                    ->label('Keluar')
                    ->sortable(),
                TextColumn::make('stok_akhir')
                    ->label('Akhir')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(25),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('stok_kosong')
                    ->label('Stok Kosong')
                    ->query(fn ($query) => $query->where('stok_akhir', '<=', 0)),

                Filter::make('harga_tinggi')
                    ->label('Harga Retail > 5 Juta')
                    ->query(fn ($query) => $query->where('harga_retail', '>', 5000000)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->deferFilters(false);
    }
}
