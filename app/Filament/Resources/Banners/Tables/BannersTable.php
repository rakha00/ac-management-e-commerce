<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Models\Banner;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('order')
                    ->numeric()
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
                //
            ])
            ->defaultSort('order')
            ->recordActions([
                Action::make('moveUp')
                    ->icon('heroicon-m-arrow-up')
                    ->iconButton()
                    ->action(function (Banner $record) {
                        $previous = Banner::where('order', '<', $record->order)
                            ->orderBy('order', 'desc')
                            ->first();

                        if ($previous) {
                            $tempOrder = $record->order;
                            $record->update(['order' => $previous->order]);
                            $previous->update(['order' => $tempOrder]);
                        }
                    })
                    ->disabled(fn (Banner $record) => Banner::min('order') == $record->order),
                Action::make('moveDown')
                    ->icon('heroicon-m-arrow-down')
                    ->iconButton()
                    ->action(function (Banner $record) {
                        $next = Banner::where('order', '>', $record->order)
                            ->orderBy('order', 'asc')
                            ->first();

                        if ($next) {
                            $tempOrder = $record->order;
                            $record->update(['order' => $next->order]);
                            $next->update(['order' => $tempOrder]);
                        }
                    })
                    ->disabled(fn (Banner $record) => Banner::max('order') == $record->order),
                Action::make('activate')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->iconButton()
                    ->action(fn (Banner $record) => $record->update(['active' => true]))
                    ->visible(fn (Banner $record) => ! $record->active),
                Action::make('deactivate')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->iconButton()
                    ->action(fn (Banner $record) => $record->update(['active' => false]))
                    ->visible(fn (Banner $record) => $record->active),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
