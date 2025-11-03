<?php

namespace App\Filament\Resources\Spareparts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HargaHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'hargaHistory';

    protected static ?string $pluralLabel = 'Riwayat Harga';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('harga_modal')
            ->columns([
                TextColumn::make('harga_modal')
                    ->label('Harga Modal')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Perubahan')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('karyawan.nama')
                    ->label('Diubah Oleh')
                    ->default('Sistem')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->latest());
    }
}
