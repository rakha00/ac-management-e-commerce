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
                    })
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('jumlah_barang_masuk')
                    ->label('Jumlah Barang Masuk')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Textarea::make('keterangan')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_unit')
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_barang_masuk')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
