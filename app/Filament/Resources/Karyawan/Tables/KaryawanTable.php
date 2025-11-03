<?php

namespace App\Filament\Resources\Karyawan\Tables;

use App\Filament\Resources\Karyawan\KaryawanResource;
use App\Models\Karyawan;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as ExcelExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class KaryawanTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->getStateUsing(function (Karyawan $record, HasTable $livewire): int {
                        $tableFilters = $livewire->getTable()->getFilters();
                        $tanggalAwal = null;
                        $tanggalAkhir = null;

                        if (isset($tableFilters['tanggal_penghasilan'])) {
                            $filterData = $tableFilters['tanggal_penghasilan']->getState();
                            $tanggalAwal = $filterData['dari'] ?? null;
                            $tanggalAkhir = $filterData['sampai'] ?? null;
                        }

                        return $record->getTotalGaji($tanggalAwal, $tanggalAkhir);
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
                        DatePicker::make('dari')
                            ->default(now()->startOfMonth())
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
                                Column::make('gaji_pokok')
                                    ->format('"Rp"#,##0_-'),
                                Column::make('total_gaji')
                                    ->getStateUsing(function (Karyawan $record, HasTable $livewire): int {
                                        $tableFilters = $livewire->getTable()->getFilters();
                                        $tanggalAwal = null;
                                        $tanggalAkhir = null;

                                        if (isset($tableFilters['tanggal_penghasilan'])) {
                                            $filterData = $tableFilters['tanggal_penghasilan']->getState();
                                            $tanggalAwal = $filterData['dari'] ?? null;
                                            $tanggalAkhir = $filterData['sampai'] ?? null;
                                        }

                                        return $record->getTotalGaji($tanggalAwal, $tanggalAkhir);
                                    })
                                    ->format('"Rp"#,##0_-'),
                                Column::make('status_aktif')
                                    ->formatStateUsing(fn ($state) => $state ? 'aktif' : 'tidak aktif'),
                            ])
                            ->withFilename(fn () => 'karyawan_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
            ]);
    }
}
