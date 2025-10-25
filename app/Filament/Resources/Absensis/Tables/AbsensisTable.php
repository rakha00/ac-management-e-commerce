<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('karyawan'))
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
                    ->label('Tepat Waktu')
                    ->getStateUsing(fn ($record) => ! (bool) $record->telat)
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('waktu_konfirmasi')
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
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->form([
                        TextInput::make('tanggal')
                            ->type('date')
                            ->placeholder('Pilih tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['tanggal'] ?? null),
                            fn (Builder $q): Builder => $q->whereDate('tanggal', $data['tanggal'])
                        );
                    }),
            ])
            ->recordActions([
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->terkonfirmasi)
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
