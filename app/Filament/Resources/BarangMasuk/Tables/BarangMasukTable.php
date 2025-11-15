<?php

namespace App\Filament\Resources\BarangMasuk\Tables;

use App\Exports\BarangMasukExport;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class BarangMasukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['barangMasukDetail']))
            ->columns([
                TextColumn::make('nomor_barang_masuk')
                    ->label('Nomor BM')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('principal.nama')
                    ->label('Principal')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('barangMasukDetails.jumlah_barang_masuk')
                    ->getStateUsing(function ($record) {
                        return $record->barangMasukDetail()->sum('jumlah_barang_masuk');
                    })
                    ->label('Total Qty'),
                TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deletedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('principal_id')
                    ->label('Principal')
                    ->relationship('principal', 'nama')
                    ->searchable()
                    ->preload(),
                Filter::make('date_range')
                    ->label('Filter Tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->maxDate(fn (callable $get) => $get('sampai') ?? null),
                        DatePicker::make('sampai')
                            ->minDate(fn (callable $get) => $get('dari')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators['dari'] = 'Dari '.Carbon::parse($data['dari'])->toFormattedDateString();
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators['sampai'] = 'Sampai '.Carbon::parse($data['sampai'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        BarangMasukExport::make('table')
                            ->withFilters(
                                collect($table->getFilters())->mapWithKeys(
                                    fn ($filter, $key) => [$key => $filter->getState()]
                                )->toArray()
                            )
                            ->withFilename(fn () => 'barang_masuk_'.now()->format('Ymd_His')),
                    ]),
            ]);
    }
}
