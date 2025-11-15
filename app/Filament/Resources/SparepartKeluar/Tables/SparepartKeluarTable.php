<?php

namespace App\Filament\Resources\SparepartKeluar\Tables;

use App\Exports\SparepartKeluarExport;
use App\Models\SparepartKeluar;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

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

                TextColumn::make('konsumen.nama')
                    ->label('Konsumen')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_modal')
                    ->label('Total Modal')
                    ->numeric()
                    ->getStateUsing(fn ($record): int => $record->getTotalModal())
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('total_penjualan')
                    ->label('Total Penjualan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->getStateUsing(fn ($record): int => $record->getTotalPenjualan()),

                TextColumn::make('total_keuntungan')
                    ->label('Total Keuntungan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->getStateUsing(fn ($record): int => $record->getTotalKeuntungan()),
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
                Action::make('print_invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-s-printer')
                    ->modalWidth('sm')
                    ->schema([
                        Select::make('format_type')
                            ->label('Jenis Format')
                            ->options([
                                'a4' => 'A4',
                                'dot-matrix' => 'Dot Matrix',
                            ])
                            ->default('a4')
                            ->selectablePlaceholder(false)
                            ->required(),
                    ])
                    ->action(function (SparepartKeluar $record, array $data) {
                        $format_type = $data['format_type'] ?? 'a4';

                        return redirect()->route('sparepart-keluar.invoice', [
                            'record' => $record,
                            'format_type' => $format_type,
                        ], );
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        SparepartKeluarExport::make('table')
                            ->withFilters(
                                collect($table->getFilters())->mapWithKeys(
                                    fn ($filter, $key) => [$key => $filter->getState()]
                                )->toArray()
                            )
                            ->withFilename(fn () => 'sparepart_keluar_'.now()->format('Ymd_His')),
                    ]),
            ]);
    }
}
