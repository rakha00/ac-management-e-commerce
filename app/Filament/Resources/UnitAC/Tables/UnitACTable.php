<?php

namespace App\Filament\Resources\UnitAC\Tables;

use App\Models\BarangMasukDetail;
use App\Models\TransaksiProdukDetail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitACTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_produk')
                    ->label('Foto')
                    ->size(50)
                    ->limit(1),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->searchable(),
                TextColumn::make('harga_dealer')
                    ->label('Harga Dealer')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('harga_retail')
                    ->label('Harga Retail')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('stok_awal')
                    ->label('Awal')
                    ->sortable(),
                TextColumn::make('stok_masuk')
                    ->label('Masuk')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_masuk;
                        }

                        $query = BarangMasukDetail::where('unit_ac_id', $record->id)
                            ->join('barang_masuk', 'barang_masuk.id', '=', 'barang_masuk_detail.barang_masuk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $query->whereDate('barang_masuk.tanggal', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $query->whereDate('barang_masuk.tanggal', '<=', $dateFilter['until_date']);
                        }

                        return $query->sum('jumlah_barang_masuk');
                    }),
                TextColumn::make('stok_keluar')
                    ->label('Keluar')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_keluar;
                        }

                        $query = TransaksiProdukDetail::where('unit_ac_id', $record->id)
                            ->join('transaksi_produk', 'transaksi_produk.id', '=', 'transaksi_produk_detail.transaksi_produk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $query->whereDate('transaksi_produk.tanggal_transaksi', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $query->whereDate('transaksi_produk.tanggal_transaksi', '<=', $dateFilter['until_date']);
                        }

                        return $query->sum('jumlah_keluar');
                    }),
                TextColumn::make('stok_akhir')
                    ->label('Akhir')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_akhir;
                        }

                        // Hitung stok masuk berdasarkan filter tanggal
                        $stokMasukQuery = BarangMasukDetail::where('unit_ac_id', $record->id)
                            ->join('barang_masuk', 'barang_masuk.id', '=', 'barang_masuk_detail.barang_masuk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $stokMasukQuery->whereDate('barang_masuk.tanggal', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $stokMasukQuery->whereDate('barang_masuk.tanggal', '<=', $dateFilter['until_date']);
                        }

                        $stokMasuk = $stokMasukQuery->sum('jumlah_barang_masuk');

                        // Hitung stok keluar berdasarkan filter tanggal
                        $stokKeluarQuery = TransaksiProdukDetail::where('unit_ac_id', $record->id)
                            ->join('transaksi_produk', 'transaksi_produk.id', '=', 'transaksi_produk_detail.transaksi_produk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $stokKeluarQuery->whereDate('transaksi_produk.tanggal_transaksi', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $stokKeluarQuery->whereDate('transaksi_produk.tanggal_transaksi', '<=', $dateFilter['until_date']);
                        }

                        $stokKeluar = $stokKeluarQuery->sum('jumlah_keluar');

                        // Hitung stok akhir: stok awal + stok masuk - stok keluar
                        return $record->stok_awal + $stokMasuk - $stokKeluar;
                    }),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(25),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('date_range')
                    ->label('Filter Tanggal')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('until_date')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Jika kedua tanggal tidak diisi, kembalikan query tanpa filter
                        if (empty($data['from_date']) && empty($data['until_date'])) {
                            return $query;
                        }

                        // Simpan filter date range dalam session
                        session([
                            'date_filter' => [
                                'from_date' => $data['from_date'] ?? null,
                                'until_date' => $data['until_date'] ?? null,
                            ],
                        ]);

                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $indicators = [];

                        if (! empty($data['from_date'])) {
                            $indicators[] = 'Dari: '.$data['from_date'];
                        }

                        if (! empty($data['until_date'])) {
                            $indicators[] = 'Sampai: '.$data['until_date'];
                        }

                        return count($indicators) ? implode(' ', $indicators) : null;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $indicators = [];

                        if (! empty($data['from_date'])) {
                            $indicators[] = 'Dari: '.$data['from_date'];
                        }

                        if (! empty($data['until_date'])) {
                            $indicators[] = 'Sampai: '.$data['until_date'];
                        }

                        return count($indicators) ? implode(' ', $indicators) : null;
                    }),
                Filter::make('stok_kosong')
                    ->label('Stok Kosong')
                    ->query(fn ($query) => $query->where('stok_akhir', '<=', 0)),

                Filter::make('harga_tinggi')
                    ->label('Harga Retail > 5 Juta')
                    ->query(fn ($query) => $query->where('harga_retail', '>', 5000000)),
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
