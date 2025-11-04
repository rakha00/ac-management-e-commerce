<?php

namespace App\Filament\Resources\Spareparts\Tables;

use App\Models\Sparepart;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

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
                Filter::make('stok_kosong')
                    ->label('Stok Kosong')
                    ->query(fn ($query) => $query->where('stok_akhir', '<=', 0)),
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
