<?php

namespace App\Filament\Resources\Spareparts\Tables;

use App\Models\SparepartKeluarDetail;
use App\Models\SparepartMasukDetail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SparepartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path_foto_sparepart')
                    ->label('Foto')
                    ->size(50)
                    ->limit(1),
                TextColumn::make('kode_sparepart')
                    ->label('Kode Sparepart')
                    ->searchable(),

                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable(),

                TextColumn::make('harga_modal')
                    ->label('Harga Modal')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->numeric()
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('stok_awal')
                    ->label('Stok Awal')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stok_masuk')
                    ->label('Stok Masuk')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_masuk;
                        }

                        $query = SparepartMasukDetail::where('sparepart_id', $record->id)
                            ->join('sparepart_masuk', 'sparepart_masuk.id', '=', 'sparepart_masuk_detail.sparepart_masuk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $query->whereDate('sparepart_masuk.tanggal_masuk', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $query->whereDate('sparepart_masuk.tanggal_masuk', '<=', $dateFilter['until_date']);
                        }

                        return $query->sum('jumlah_masuk');
                    }),

                TextColumn::make('stok_keluar')
                    ->label('Stok Keluar')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_keluar;
                        }

                        $query = SparepartKeluarDetail::where('sparepart_id', $record->id)
                            ->join('sparepart_keluar', 'sparepart_keluar.id', '=', 'sparepart_keluar_detail.sparepart_keluar_id');

                        if (! empty($dateFilter['from_date'])) {
                            $query->whereDate('sparepart_keluar.tanggal_keluar', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $query->whereDate('sparepart_keluar.tanggal_keluar', '<=', $dateFilter['until_date']);
                        }

                        return $query->sum('jumlah_keluar');
                    }),

                TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $dateFilter = session('date_filter');

                        if (empty($dateFilter)) {
                            return $record->stok_akhir;
                        }

                        $stokAwal = $record->stok_awal;

                        $stokMasukQuery = SparepartMasukDetail::where('sparepart_id', $record->id)
                            ->join('sparepart_masuk', 'sparepart_masuk.id', '=', 'sparepart_masuk_detail.sparepart_masuk_id');

                        if (! empty($dateFilter['from_date'])) {
                            $stokMasukQuery->whereDate('sparepart_masuk.tanggal_masuk', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $stokMasukQuery->whereDate('sparepart_masuk.tanggal_masuk', '<=', $dateFilter['until_date']);
                        }

                        $stokMasuk = $stokMasukQuery->sum('jumlah_masuk');

                        $stokKeluarQuery = SparepartKeluarDetail::where('sparepart_id', $record->id)
                            ->join('sparepart_keluar', 'sparepart_keluar.id', '=', 'sparepart_keluar_detail.sparepart_keluar_id');

                        if (! empty($dateFilter['from_date'])) {
                            $stokKeluarQuery->whereDate('sparepart_keluar.tanggal_keluar', '>=', $dateFilter['from_date']);
                        }

                        if (! empty($dateFilter['until_date'])) {
                            $stokKeluarQuery->whereDate('sparepart_keluar.tanggal_keluar', '<=', $dateFilter['until_date']);
                        }

                        $stokKeluar = $stokKeluarQuery->sum('jumlah_keluar');

                        return $stokAwal + $stokMasuk - $stokKeluar;
                    }),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Dari Tanggal')
                            ->placeholder('Dari Tanggal')
                            ->afterStateUpdated(function ($state) {
                                $dateFilter = session('date_filter', []);
                                $dateFilter['from_date'] = $state;
                                session(['date_filter' => $dateFilter]);
                            }),
                        DatePicker::make('until_date')
                            ->label('Sampai Tanggal')
                            ->placeholder('Sampai Tanggal')
                            ->afterStateUpdated(function ($state) {
                                $dateFilter = session('date_filter', []);
                                $dateFilter['until_date'] = $state;
                                session(['date_filter' => $dateFilter]);
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators['from_date'] = 'Dari '.Carbon::parse($data['from_date'])->toFormattedDateString();
                        }
                        if ($data['until_date'] ?? null) {
                            $indicators['until_date'] = 'Sampai '.Carbon::parse($data['until_date'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Filter::make('stok_kosong')
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['is_empty']) && $data['is_empty']) {
                            $query->where('stok_akhir', 0);
                        }

                        return $query;
                    })
                    ->form([
                        Checkbox::make('is_empty')
                            ->label('Stok Kosong')
                            ->default(false),
                    ])
                    ->indicateUsing(function (array $data): array {
                        if (isset($data['is_empty']) && $data['is_empty']) {
                            return ['Stok Kosong'];
                        }

                        return [];
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferFilters(false);
    }
}
