<?php

namespace App\Filament\Resources\HutangProduks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HutangProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_unit_masuk')
                    ->searchable(),
                TextColumn::make('total_hutang')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sisa_hutang')
                    ->numeric()
                    ->sortable()
                    ->state(fn ($record): int => max(($record->total_hutang ?? 0) - (int) $record->detailHutangProdukCicilan()->sum('nominal_cicilan'), 0)),
                TextColumn::make('nama_principle')
                    ->searchable(),
                TextColumn::make('status_pembayaran')
                    ->searchable(),
                TextColumn::make('jatuh_tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
