<?php

namespace App\Filament\Resources\Spareparts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class SparepartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_sparepart')
                    ->label('Kode Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga_modal')
                    ->label('Harga Modal')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('stock_awal')
                    ->label('Stock Awal')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock_masuk')
                    ->label('Stock Masuk')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock_keluar')
                    ->label('Stock Keluar')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock_akhir')
                    ->label('Stock Akhir')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                // Custom date range filter for 'created_at' (master data date)
                Filter::make('created_at')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari'),
                        DatePicker::make('created_until')->label('Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Export Excel')
                    ->fileName(fn() => 'sparepart-master-' . now()->format('Ymd-His'))
                    ->fromTable(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
