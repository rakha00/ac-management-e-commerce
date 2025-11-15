<?php

namespace App\Filament\Resources\MerkSpareparts\Tables;

use App\Filament\Resources\Spareparts\SparepartResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MerkSparepartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('merk_spareparts')
                    ->label('Merk Spareparts')
                    ->searchable(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('merk_spareparts')
                    ->label('Spareparts')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => SparepartResource::getUrl()),
            ]);
    }
}
