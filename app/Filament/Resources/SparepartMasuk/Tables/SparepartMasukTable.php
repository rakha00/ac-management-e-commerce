<?php

namespace App\Filament\Resources\SparepartMasuk\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SparepartMasukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_sparepart_masuk')
                    ->label('No. Masuk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_masuk')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('distributor_nama')
                    ->label('Distributor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_qty')
                    ->label('Total Qty')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('tanggal_masuk')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('date_from')->label('Dari'),
                        DatePicker::make('date_until')->label('Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_masuk', '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_masuk', '<=', $date),
                            );
                    }),
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
