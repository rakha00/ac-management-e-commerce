<?php

namespace App\Filament\Resources\TipeAC\Tables;

use App\Filament\Resources\UnitAC\UnitACResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TipeACTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipe_ac')
                    ->label('Tipe AC')
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
                Action::make('unit_ac')
                    ->label('Unit AC')
                    ->icon('heroicon-o-cube')
                    ->url(fn () => UnitACResource::getUrl()),
            ]);
    }
}
