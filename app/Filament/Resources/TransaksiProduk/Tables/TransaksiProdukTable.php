<?php

namespace App\Filament\Resources\TransaksiProduk\Tables;

use App\Models\TransaksiProduk;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
                TextColumn::make('salesKaryawan.nama')
                    ->label('Sales')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('konsumen.nama')
                    ->label('Konsumen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sub_total_modal')
                    ->label('Sub Total Modal')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn (TransaksiProduk $record): string => $record->transaksiProdukDetail->sum(fn ($detail) => $detail->harga_modal * $detail->jumlah_keluar))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sub_total_penjualan')
                    ->label('Sub Total Penjualan')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn (TransaksiProduk $record): string => $record->transaksiProdukDetail->sum(fn ($detail) => $detail->harga_jual * $detail->jumlah_keluar))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sub_total_keuntungan')
                    ->label('Sub Total Keuntungan')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(fn (TransaksiProduk $record): string => $record->transaksiProdukDetail->sum(fn ($detail) => ($detail->harga_jual - $detail->harga_modal) * $detail->jumlah_keluar))
                    ->sortable()
                    ->searchable(),
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
                SelectFilter::make('sales_karyawan_id')
                    ->label('Sales')
                    ->relationship('salesKaryawan', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('konsumen_id')
                    ->label('Konsumen')
                    ->relationship('konsumen', 'nama')
                    ->searchable()
                    ->preload(),
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
                            $indicators['sampai'] = 'Sampai '.Carbon::parse($data['sampai'])->toFormattedDateString();
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
