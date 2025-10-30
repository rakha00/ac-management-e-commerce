<?php

namespace App\Filament\Resources\Karyawan\Tables;

use App\Filament\Resources\Karyawan\KaryawanResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nomor_hp')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('gaji_pokok')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
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
                Filter::make('aktif')
                    ->label('Status Aktif')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('status_aktif', true)),
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
                                Column::make('kontak_darurat_serumah'),
                                Column::make('kontak_darurat_tidak_serumah'),
                                Column::make('status_aktif'),
                                Column::make('created_at')->heading('Dibuat Pada'),
                            ])
                            ->withFilename(fn () => 'karyawan_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->deferFilters(false);
    }
}
