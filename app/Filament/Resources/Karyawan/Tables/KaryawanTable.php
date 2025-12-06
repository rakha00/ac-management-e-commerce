<?php

namespace App\Filament\Resources\Karyawan\Tables;

use App\Filament\Resources\Karyawan\KaryawanResource;
use App\Models\Karyawan;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\IconColumn;
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

                        if (isset($tableFilters['date_range'])) {
                            $filterData = $tableFilters['date_range']->getState();
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

                Filter::make('date_range')
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
                ActionGroup::make([
                    Action::make('penghasilan')
                        ->label('Detail Pengahasilan')
                        ->url(fn ($record) => KaryawanResource::getUrl('penghasilan', ['record' => $record])),
                    Action::make('slip_gaji')
                        ->label('Slip Gaji')
                        ->form([
                            Select::make('bulan')
                                ->options([
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember',
                                ])
                                ->default(now()->month)
                                ->required(),
                            Select::make('tahun')
                                ->options(function () {
                                    $currentYear = now()->year;
                                    $years = range($currentYear - 5, $currentYear + 1);

                                    return array_combine($years, $years);
                                })
                                ->default(now()->year)
                                ->required(),
                        ])
                        ->action(function (Karyawan $record, array $data) {
                            return redirect()->route('karyawan.slip-gaji', [
                                'record' => $record,
                                'bulan' => $data['bulan'],
                                'tahun' => $data['tahun'],
                            ]);
                        }),
                ])
                    ->label('Penghasilan')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->size(Size::Small)
                    ->link(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
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

                                        if (isset($tableFilters['date_range'])) {
                                            $filterData = $tableFilters['date_range']->getState();
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
                Action::make('download_zip_slip_gaji')
                    ->label('Slip Gaji')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Select::make('bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->default(now()->month)
                            ->required(),
                        Select::make('tahun')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = range($currentYear - 5, $currentYear + 1);

                                return array_combine($years, $years);
                            })
                            ->default(now()->year)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('karyawan.bulk-slip-gaji-zip', [
                            'bulan' => $data['bulan'],
                            'tahun' => $data['tahun'],
                        ]);
                    }),
            ]);
    }
}
