<?php

namespace App\Filament\Resources\Laporans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('nama_konsumen')
                    ->label('Nama Konsumen')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('karyawan.nama')
                    ->label('Nama Teknisi')
                    ->searchable()
                    ->visible(fn (): bool => auth()->user()?->karyawan?->jabatan === 'admin'),
                TextColumn::make('jenis_pengerjaan')
                    ->label('Jenis Pengerjaan')
                    ->badge(),
                ImageColumn::make('foto_pengerjaan')
                    ->label('Foto')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->size(50),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('jenis_pengerjaan')
                    ->label('Jenis Pengerjaan')
                    ->options([
                        'Perawatan Rutin' => 'Perawatan Rutin',
                        'Perbaikan' => 'Perbaikan',
                        'Instalasi' => 'Instalasi',
                        'Pengisian Freon' => 'Pengisian Freon',
                        'Lain-lain' => 'Lain-lain',
                    ]),
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorizeIndividualRecords('delete'),
                    ForceDeleteBulkAction::make()->authorizeIndividualRecords('forceDelete'),
                    RestoreBulkAction::make()->authorizeIndividualRecords('restore'),
                ]),
            ]);
    }
}
