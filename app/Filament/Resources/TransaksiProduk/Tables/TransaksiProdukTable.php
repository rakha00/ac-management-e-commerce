<?php

namespace App\Filament\Resources\TransaksiProduk\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiProdukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('nomor_invoice')
                    ->label('No. Invoice')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nomor_surat_jalan')
                    ->label('No. Surat Jalan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sales_nama')
                    ->label('Sales')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('toko_konsumen')
                    ->label('Toko/Konsumen')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Custom date range filter for 'tanggal_transaksi'
                Filter::make('tanggal_transaksi')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari'),
                        DatePicker::make('created_until')->label('Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
