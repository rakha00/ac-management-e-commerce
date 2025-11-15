<?php

namespace App\Filament\Resources\SparepartMasuk\Tables;

use App\Exports\SparepartMasukExport;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

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

                TextColumn::make('distributor.nama_distributor')
                    ->label('Distributor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_qty')
                    ->label('Total Qty')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->detailSparepartMasuk()->sum('jumlah_masuk')),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->schema([
                        DatePicker::make('dari')
                            ->maxDate(fn (callable $get) => $get('sampai') ?? null),
                        DatePicker::make('sampai')
                            ->minDate(fn (callable $get) => $get('dari')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_masuk', '>=', $date),
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_masuk', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        SparepartMasukExport::make('table')
                            ->withFilters(
                                collect($table->getFilters())->mapWithKeys(
                                    fn ($filter, $key) => [$key => $filter->getState()]
                                )->toArray()
                            )
                            ->withFilename(fn () => 'sparepart_masuk_'.now()->format('Ymd_His')),
                    ]),
            ]);
    }
}
