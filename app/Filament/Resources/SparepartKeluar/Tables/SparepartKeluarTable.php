<?php

namespace App\Filament\Resources\SparepartKeluar\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SparepartKeluarTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_invoice')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_keluar')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('konsumen_nama')
                    ->label('Konsumen')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_modal')
                    ->label('Total Modal')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('total_penjualan')
                    ->label('Total Penjualan')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('total_keuntungan')
                    ->label('Total Keuntungan')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('tanggal_keluar')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('date_from')->label('Dari'),
                        DatePicker::make('date_until')->label('Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_keluar', '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_keluar', '<=', $date),
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
