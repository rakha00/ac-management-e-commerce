<?php

namespace App\Filament\Resources\BarangMasuks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class BarangMasuksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_barang_masuk')
                    ->label('Nomor BM')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('principle.nama')
                    ->label('Principle')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([])
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
