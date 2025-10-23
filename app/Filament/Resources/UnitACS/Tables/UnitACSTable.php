<?php

namespace App\Filament\Resources\UnitACS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitACSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_produk')
                    ->label('Foto')
                    ->circular()
                    ->size(50),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_merk')
                    ->label('Nama Merk')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('harga_dealer')
                    ->label('Harga Dealer')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('harga_retail')
                    ->label('Harga Retail')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('stock_awal')
                    ->label('Awal')
                    ->sortable(),

                TextColumn::make('stock_masuk')
                    ->label('Masuk')
                    ->sortable(),

                TextColumn::make('stock_keluar')
                    ->label('Keluar')
                    ->sortable(),

                TextColumn::make('stock_akhir')
                    ->label('Akhir')
                    ->sortable(),

                TextColumn::make('remarks')
                    ->label('Keterangan')
                    ->limit(25),
            ])
            ->filters([
                Tables\Filters\Filter::make('stok_kosong')
                    ->label('Stok Kosong')
                    ->query(fn ($query) => $query->where('stock_akhir', '<=', 0)),

                Tables\Filters\Filter::make('harga_tinggi')
                    ->label('Harga Retail > 5 Juta')
                    ->query(fn ($query) => $query->where('harga_retail', '>', 5000000)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
