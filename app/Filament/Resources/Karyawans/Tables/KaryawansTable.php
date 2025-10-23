<?php

namespace App\Filament\Resources\Karyawans\Tables;

use App\Models\Karyawan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KaryawansTable
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
                TextColumn::make('no_hp')
                    ->searchable(),
                TextColumn::make('gaji_pokok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kontak_darurat_serumah')
                    ->searchable(),
                TextColumn::make('kontak_darurat_tidak_serumah')
                    ->searchable(),
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

                // Filter jabatan
                SelectFilter::make('jabatan')
                    ->options([
                        'admin' => 'Admin',
                        'gudang' => 'Gudang',
                        'helper' => 'Helper',
                        'teknisi' => 'Teknisi',
                        'staff' => 'Staff',
                        'sales' => 'Sales',
                    ]),

                // Filter status_aktif
                Filter::make('aktif')
                    ->label('Status Aktif')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('status_aktif', true)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->form([
                        DatePicker::make('from')->label('Tanggal dari'),
                        DatePicker::make('until')->label('Tanggal sampai'),
                    ])
                    ->action(function (array $data) {
                        $query = Karyawan::query()
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date)
                            );

                        $rows = $query->get([
                            'nama',
                            'jabatan',
                            'no_hp',
                            'gaji_pokok',
                            'kontak_darurat_serumah',
                            'kontak_darurat_tidak_serumah',
                            'status_aktif',
                            'created_at',
                        ]);

                        $filename = 'karyawans_'.now()->format('Ymd_His').'.csv';

                        $handle = fopen('php://temp', 'r+');

                        // Header
                        fputcsv($handle, [
                            'nama',
                            'jabatan',
                            'no_hp',
                            'gaji_pokok',
                            'kontak_darurat_serumah',
                            'kontak_darurat_tidak_serumah',
                            'status_aktif',
                            'created_at',
                        ]);

                        foreach ($rows as $row) {
                            fputcsv($handle, [
                                $row->nama,
                                $row->jabatan,
                                $row->no_hp,
                                $row->gaji_pokok,
                                $row->kontak_darurat_serumah,
                                $row->kontak_darurat_tidak_serumah,
                                $row->status_aktif ? '1' : '0',
                                $row->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }

                        rewind($handle);

                        return response()->streamDownload(function () use ($handle) {
                            fpassthru($handle);
                            fclose($handle);
                        }, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
