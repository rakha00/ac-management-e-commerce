<?php

namespace App\Filament\Resources\Absensi\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AbsensiTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('karyawan'))
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('karyawan.jabatan')
                    ->label('Jabatan')
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
                TextColumn::make('waktu_absen')
                    ->label('Tanggal Absen')
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('waktu_absen_time')
                    ->label('Waktu Absen')
                    ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->waktu_absen)->format('H:i:s'))
                    ->sortable(),
                IconColumn::make('is_telat')
                    ->label('Tepat Waktu')
                    ->getStateUsing(fn ($record) => ! (bool) $record->is_telat)
                    ->boolean(),
                IconColumn::make('is_terkonfirmasi')
                    ->label('Terkonfirmasi')
                    ->boolean(),
                TextColumn::make('dikonfirmasiOleh.name')
                    ->label('Dikonfirmasi Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dikonfirmasi_pada')
                    ->label('Waktu Konfirmasi')
                    ->dateTime()
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
            ])
            ->filters([
                SelectFilter::make('month')
                    ->options(function () {
                        $months = [];
                        for ($i = 0; $i < 12; $i++) {
                            $date = Carbon::now()->subMonths($i);
                            $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
                        }

                        return $months;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        $yearMonth = explode('-', $data['value']);

                        return $query
                            ->whereYear('waktu_absen', $yearMonth[0])
                            ->whereMonth('waktu_absen', $yearMonth[1]);
                    })
                    ->label('Pilih Bulan'),
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->placeholder('Pilih tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['tanggal'] ?? null),
                            fn (Builder $q): Builder => $q->whereDate('waktu_absen', $data['tanggal'])
                        );
                    }),
            ])
            ->recordActions([
                Action::make('lihat-foto-bukti')
                    ->label('Lihat Foto Bukti')
                    ->icon('heroicon-o-photo')
                    ->visible(fn ($record) => ! empty($record->foto_bukti))
                    ->schema([
                        Grid::make()
                            ->schema([
                                ImageEntry::make('foto_bukti')
                                    ->disk('local')
                                    ->imageSize(334)
                                    ->getStateUsing(function ($record) {
                                        return $record->foto_bukti ? route('storage.private', $record->foto_bukti) : null;
                                    }),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->modalWidth('sm')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->is_terkonfirmasi)
                    ->action(function (\App\Models\Absensi $record) {
                        $record->forceFill([
                            'is_terkonfirmasi' => true,
                            'dikonfirmasi_oleh_id' => auth()->id(),
                            'dikonfirmasi_pada' => now(),
                        ])->save();
                    }),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withColumns([
                                Column::make('waktu_absen'),
                                Column::make('is_telat')->heading('Tepat Waktu')->formatStateUsing(fn ($state) => $state ? 'Tidak' : 'Ya'),
                                Column::make('is_terkonfirmasi')->heading('Terkonfirmasi')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                                Column::make('dikonfirmasiOleh.name')->heading('Dikonfirmasi Oleh'),
                                Column::make('dikonfirmasi_pada')->heading('Waktu Konfirmasi'),
                            ])
                            ->withFilename(fn () => 'absensi_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
                BulkActionGroup::make([
                    BulkAction::make('konfirmasi_semua')
                        ->label('Konfirmasi Semua')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $records->each(function (\App\Models\Absensi $record) {
                                $record->forceFill([
                                    'is_terkonfirmasi' => true,
                                    'dikonfirmasi_oleh_id' => auth()->id(),
                                    'dikonfirmasi_pada' => now(),
                                ])->save();
                            });
                        }),
                ]),
            ])
            ->deferFilters(false);
    }
}
