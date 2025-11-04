<?php

namespace App\Filament\Resources\BarangMasuk\RelationManagers;

use App\Models\UnitAC;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangMasukDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'barangMasukDetail';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('unit_ac_id')
                    ->label('Unit AC')
                    ->relationship('unitAC', 'nama_unit')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $unitAc = UnitAC::find($state);
                            if ($unitAc) {
                                $set('sku', $unitAc->sku);
                                $set('nama_unit', $unitAc->nama_unit);
                            }
                        } else {
                            $set('sku', null);
                            $set('nama_unit', null);
                        }
                    }),
                TextInput::make('sku')
                    ->label('SKU')
                    ->disabled(),
                TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled(),
                TextInput::make('jumlah_barang_masuk')
                    ->label('Jumlah Barang Masuk')
                    ->numeric()
                    ->suffix('unit'),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_unit')
            ->columns([
                TextColumn::make('unitAC.sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unitAC.nama_unit')
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_barang_masuk')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deletedBy.name')
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
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
