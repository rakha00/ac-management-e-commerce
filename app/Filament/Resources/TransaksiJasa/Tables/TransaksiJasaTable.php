<?php

namespace App\Filament\Resources\TransaksiJasa\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiJasaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('kode_jasa')
                    ->label('Kode Jasa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('teknisi_nama')
                    ->label('Teknisi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('helper_nama')
                    ->label('Helper')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama_konsumen')
                    ->label('Nama Konsumen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_pendapatan_jasa')
                    ->label('Total Pendapatan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_pengeluaran_jasa')
                    ->label('Total Pengeluaran')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_keuntungan_jasa')
                    ->label('Total Keuntungan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
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
                                fn(Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dari ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Hingga ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
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
