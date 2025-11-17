<?php

namespace App\Filament\Resources\Spareparts\Tables;

use App\Filament\Resources\MerkSpareparts\MerkSparepartResource;
use App\Models\Sparepart;
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

class SparepartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path_foto_sparepart')
                    ->disk('public')
                    ->label('Foto')
                    ->size(50)
                    ->limit(1)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kode_sparepart')
                    ->label('Kode Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('merkSparepart.merk_spareparts')
                    ->label('Merk')
                    ->searchable()
                    ->sortable(),

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
                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getTotalStokMasuk($tanggalAwal, $tanggalAkhir);
                    }),

                TextColumn::make('stok_keluar')
                    ->label('Stok Keluar')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getTotalStokKeluar($tanggalAwal, $tanggalAkhir);
                    }),

                TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getCalculatedStokAkhir($tanggalAwal, $tanggalAkhir);
                    }),
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
                Filter::make('date_range')
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
                SelectFilter::make('merkSparepart.merk_spareparts')
                    ->label('Merk Sparepart')
                    ->relationship('merkSparepart', 'merk_spareparts')
                    ->searchable()
                    ->preload(),
                Filter::make('stok_kosong')
                    ->label('Stok Kosong')
                    ->query(fn ($query) => $query->where('stok_akhir', '<=', 0)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('harga_modal')
                                    ->format('"Rp"#,##0_-'),
                                Column::make('harga_ecommerce')
                                    ->format('"Rp"#,##0_-'),
                                Column::make('stok_awal'),
                                Column::make('stok_masuk')
                                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                                        $tableFilters = $livewire->getTable()->getFilters();
                                        $tanggalAwal = null;
                                        $tanggalAkhir = null;

                                        if (isset($tableFilters['date_range'])) {
                                            $filterData = $tableFilters['date_range']->getState();
                                            $tanggalAwal = $filterData['dari'] ?? null;
                                            $tanggalAkhir = $filterData['sampai'] ?? null;
                                        }

                                        return $record->getTotalStokMasuk($tanggalAwal, $tanggalAkhir);
                                    }),
                                Column::make('stok_keluar')
                                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                                        $tableFilters = $livewire->getTable()->getFilters();
                                        $tanggalAwal = null;
                                        $tanggalAkhir = null;

                                        if (isset($tableFilters['date_range'])) {
                                            $filterData = $tableFilters['date_range']->getState();
                                            $tanggalAwal = $filterData['dari'] ?? null;
                                            $tanggalAkhir = $filterData['sampai'] ?? null;
                                        }

                                        return $record->getTotalStokKeluar($tanggalAwal, $tanggalAkhir);
                                    }),
                                Column::make('stok_akhir')
                                    ->getStateUsing(function (Sparepart $record, HasTable $livewire): int {
                                        $tableFilters = $livewire->getTable()->getFilters();
                                        $tanggalAwal = null;
                                        $tanggalAkhir = null;

                                        if (isset($tableFilters['date_range'])) {
                                            $filterData = $tableFilters['date_range']->getState();
                                            $tanggalAwal = $filterData['dari'] ?? null;
                                            $tanggalAkhir = $filterData['sampai'] ?? null;
                                        }

                                        return $record->getCalculatedStokAkhir($tanggalAwal, $tanggalAkhir);
                                    }),
                            ])
                            ->withFilename(fn () => 'spareparts_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
                Action::make('merk_spareparts')
                    ->label('Merk Spareparts')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn () => MerkSparepartResource::getUrl()),
            ]);
    }
}
