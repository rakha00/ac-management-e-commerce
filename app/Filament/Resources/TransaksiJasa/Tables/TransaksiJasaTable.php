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
use Illuminate\Support\Carbon;

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
                TextColumn::make('nomor_invoice_jasa')
                    ->label('Nomor Invoice')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nomor_surat_jalan_jasa')
                    ->label('Nomor Surat Jalan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('teknisi.nama')
                    ->label('Teknisi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('helper.nama')
                    ->label('Helper')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('konsumen.nama')
                    ->label('Nama Konsumen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_pendapatan_jasa')
                    ->label('Total Pendapatan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn ($record) => 'Rp '.number_format($record->detailTransaksiJasa->sum('subtotal_pendapatan')))
                    ->sortable(),
                TextColumn::make('total_pengeluaran_jasa')
                    ->label('Total Pengeluaran')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn ($record) => 'Rp '.number_format($record->detailTransaksiJasa->sum('pengeluaran_jasa')))
                    ->sortable(),
                TextColumn::make('total_keuntungan_jasa')
                    ->label('Total Keuntungan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn ($record) => 'Rp '.number_format($record->detailTransaksiJasa->sum('subtotal_keuntungan')))
                    ->sortable(),
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
                Filter::make('tanggal_transaksi')
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
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('tanggal_transaksi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators['dari'] = 'Dari '.Carbon::parse($data['dari'])->toFormattedDateString();
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators['sampai'] = 'Hingga '.Carbon::parse($data['sampai'])->toFormattedDateString();
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
            ]);
    }
}
