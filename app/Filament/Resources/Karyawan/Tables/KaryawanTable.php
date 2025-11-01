<?php

namespace App\Filament\Resources\Karyawan\Tables;

use App\Filament\Resources\Karyawan\KaryawanResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as ExcelExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class KaryawanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, \Filament\Tables\Contracts\HasTable $livewire) {
                $tableFilters = $livewire->getTable()->getFilters();

                if (isset($tableFilters['tanggal_penghasilan'])) {
                    $filterData = $tableFilters['tanggal_penghasilan']->getState();
                    if (isset($filterData['tanggal_awal']) || isset($filterData['tanggal_akhir'])) {
                        $query->leftJoin('karyawan_penghasilan_detail', function ($join) use ($filterData) {
                            $join->on('karyawan.id', '=', 'karyawan_penghasilan_detail.karyawan_id');
                            if (isset($filterData['tanggal_awal'])) {
                                $join->whereDate('karyawan_penghasilan_detail.tanggal', '>=', $filterData['tanggal_awal']);
                            }
                            if (isset($filterData['tanggal_akhir'])) {
                                $join->whereDate('karyawan_penghasilan_detail.tanggal', '<=', $filterData['tanggal_akhir']);
                            }
                        })
                            ->select('karyawan.*') // Explicitly select columns from karyawan table
                            ->groupBy('karyawan.id');
                    }
                }
            })
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jabatan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'gudang' => 'warning',
                        'helper' => 'info',
                        'teknisi' => 'primary',
                        'staff' => 'success',
                        'sales' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nomor_hp')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('gaji_pokok')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
                TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->getStateUsing(function (\App\Models\Karyawan $record, \Filament\Tables\Contracts\HasTable $livewire): int {
                        $totalPenghasilan = $record->gaji_pokok;

                        $query = $record->karyawanPenghasilanDetail();

                        $tableFilters = $livewire->getTable()->getFilters();

                        if (isset($tableFilters['tanggal_penghasilan'])) {
                            $filterData = $tableFilters['tanggal_penghasilan']->getState();
                            if (isset($filterData['tanggal_awal'])) {
                                $query->whereDate('tanggal', '>=', $filterData['tanggal_awal']);
                            }

                            if (isset($filterData['tanggal_akhir'])) {
                                $query->whereDate('tanggal', '<=', $filterData['tanggal_akhir']);
                            }
                        }

                        $details = $query->get();

                        foreach ($details as $detail) {
                            $totalPenghasilan += -$detail->kasbon + $detail->lembur + $detail->bonus - $detail->potongan;
                        }

                        return $totalPenghasilan;
                    })
                    ->sortable(),
                TextColumn::make('kontak_darurat_serumah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kontak_darurat_tidak_serumah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status_aktif')
                    ->boolean(),
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
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('jabatan')
                    ->options([
                        'admin' => 'Admin',
                        'gudang' => 'Gudang',
                        'helper' => 'Helper',
                        'teknisi' => 'Teknisi',
                        'staff' => 'Staff',
                        'sales' => 'Sales',
                    ]),
                SelectFilter::make('status_aktif')
                    ->options([
                        true => 'Aktif',
                        false => 'Tidak Aktif',
                    ])
                    ->default(true)
                    ->label('Status Aktif'),

                Filter::make('tanggal_penghasilan')
                    ->form([
                        DatePicker::make('tanggal_awal')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('tanggal_akhir')
                            ->default(now()->endOfMonth()),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['tanggal_awal'] ?? null) {
                            $indicators['tanggal_awal'] = 'Dari '.Carbon::parse($data['tanggal_awal'])->toFormattedDateString();
                        }
                        if ($data['tanggal_akhir'] ?? null) {
                            $indicators['tanggal_akhir'] = 'Sampai '.Carbon::parse($data['tanggal_akhir'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('penghasilan')
                    ->label('Penghasilan')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn ($record) => KaryawanResource::getUrl('penghasilan', ['record' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExcelExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withColumns([
                                Column::make('nama'),
                                Column::make('jabatan'),
                                Column::make('nomor_hp')->heading('Nomor HP'),
                                Column::make('gaji_pokok'),
                                Column::make('total_gaji')
                                    ->getStateUsing(function (\App\Models\Karyawan $record, \Filament\Tables\Contracts\HasTable $livewire): int {
                                        $totalPenghasilan = $record->gaji_pokok;

                                        $query = $record->karyawanPenghasilanDetail();

                                        $tableFilters = $livewire->getTable()->getFilters();

                                        if (isset($tableFilters['tanggal_penghasilan'])) {
                                            $filterData = $tableFilters['tanggal_penghasilan']->getState();
                                            if (isset($filterData['tanggal_awal'])) {
                                                $query->whereDate('tanggal', '>=', $filterData['tanggal_awal']);
                                            }

                                            if (isset($filterData['tanggal_akhir'])) {
                                                $query->whereDate('tanggal', '<=', $filterData['tanggal_akhir']);
                                            }
                                        }

                                        $details = $query->get();

                                        foreach ($details as $detail) {
                                            $totalPenghasilan += -$detail->kasbon + $detail->lembur + $detail->bonus - $detail->potongan;
                                        }

                                        return $totalPenghasilan;
                                    }),
                                Column::make('kontak_darurat_serumah'),
                                Column::make('kontak_darurat_tidak_serumah'),
                                Column::make('status_aktif')
                                    ->formatStateUsing(fn ($state) => $state ? 'aktif' : 'tidak aktif'),
                                Column::make('created_at')->heading('Dibuat Pada'),
                            ])
                            ->withFilename(fn () => 'karyawan_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
            ])
            ->deferFilters(false);
    }
}
