<?php

namespace App\Filament\Resources\PengeluaranKantor\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PengeluaranKantorTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('pengeluaran')
                    ->label('Pengeluaran')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('keterangan_pengeluaran')
                    ->label('Keterangan')
                    ->limit(40),
                ImageColumn::make('path_bukti_pembayaran')
                    ->label('Bukti')
                    ->square()
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('tanggal')
                                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d-m-Y')),
                                Column::make('pengeluaran')
                                    ->format('"Rp"#,##0_-'),
                            ])
                            ->withFilename(fn () => 'pengeluaran_kantor_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
