<?php

namespace App\Filament\Resources\UnitAC\Tables;

use App\Filament\Resources\Merk\MerkResource;
use App\Filament\Resources\TipeAC\TipeACResource;
use App\Models\UnitAC;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UnitACTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path_foto_produk')
                    ->disk('public')
                    ->label('Foto')
                    ->size(50)
                    ->limit(1)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->searchable(),
                TextColumn::make('merk.merk')
                    ->label('Merk')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('pk')
                    ->label('PK')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tipeAC.tipe_ac')
                    ->label('Tipe AC')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('harga_dealer')
                    ->label('Harga Dealer')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('harga_ecommerce')
                    ->label('Harga E-commerce')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('harga_retail')
                    ->label('Harga Retail')
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stok_awal')
                    ->label('Stok Awal')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stok_masuk')
                    ->label('Stok Masuk')
                    ->sortable()
                    ->getStateUsing(function (UnitAC $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getTotalStokMasuk($tanggalAwal, $tanggalAkhir);
                    })
                    ->toggleable(),
                TextColumn::make('stok_keluar')
                    ->label('Stok Keluar')
                    ->sortable()
                    ->getStateUsing(function (UnitAC $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getTotalStokKeluar($tanggalAwal, $tanggalAkhir);
                    })
                    ->toggleable(),
                TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->sortable()
                    ->getStateUsing(function (UnitAC $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getCalculatedStokAkhir($tanggalAwal, $tanggalAkhir);
                    })
                    ->toggleable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('merk')
                    ->relationship('merk', 'merk')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tipeAC')
                    ->label('Tipe AC')
                    ->relationship('tipeAC', 'tipe_ac')
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
                ExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('harga_dealer')
                                    ->format('"Rp"#,##0_-'),
                                Column::make('harga_ecommerce')
                                    ->format('"Rp"#,##0_-'),
                                Column::make('harga_retail')
                                    ->format('"Rp"#,##0_-'),
                            ])
                            ->withFilename(fn () => 'unit_ac_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
                Action::make('merk_ac')
                    ->label('Merk AC')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn () => MerkResource::getUrl()),
                Action::make('tipe_ac')
                    ->label('Tipe AC')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn () => TipeACResource::getUrl()),
            ]);
    }
}
