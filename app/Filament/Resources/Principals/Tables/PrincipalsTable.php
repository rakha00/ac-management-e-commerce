<?php

namespace App\Filament\Resources\Principals\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PrincipalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sales')
                    ->label('Sales')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nomor_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make('export_excel')
                    ->exports([
                        ExcelExport::make('table')
                            ->withFilename(fn () => 'principals_'.now()->format('Ymd_His'))
                            ->fromTable(),
                    ]),
            ]);
    }
}
