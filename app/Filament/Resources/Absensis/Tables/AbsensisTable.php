<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('karyawan'))
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('karyawan.jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('waktu_absen')
                    ->label('Waktu Absen')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('telat')
                    ->label('Telat')
                    ->boolean(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                IconColumn::make('terkonfirmasi')
                    ->label('Terkonfirmasi')
                    ->boolean(),
                TextColumn::make('dikonfirmasi_oleh')
                    ->label('Dikonfirmasi Oleh')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('waktu_konfirmasi')
                    ->label('Waktu Konfirmasi')
                    ->dateTime()
                    ->sortable(),
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
                Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->default()
                    ->query(fn(Builder $query) => $query->whereDate('tanggal', now()->toDateString())),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn($record) => !$record->terkonfirmasi)
                    ->action(function (\App\Models\Absensi $record) {
                        $record->forceFill([
                            'terkonfirmasi' => true,
                            'dikonfirmasi_oleh' => auth()->id(),
                            'waktu_konfirmasi' => now(),
                        ])->save();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->deferFilters(false);
    }
}
