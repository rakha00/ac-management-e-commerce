<?php

namespace App\Filament\Resources\SparepartMasuk\RelationManagers;

use App\Models\Sparepart;
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
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SparepartMasukDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'detailSparepartMasuk';

    protected static ?string $pluralLabel = 'detail sparepart masuk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('sparepart_id')
                    ->label('Kode Sparepart')
                    ->options(fn () => Sparepart::query()
                        ->orderBy('kode_sparepart')
                        ->pluck('kode_sparepart', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $sp = Sparepart::find($state);
                        if ($sp) {
                            $set('nama_sparepart', $sp->nama_sparepart);
                        } else {
                            $set('nama_sparepart', null);
                        }
                    }),

                TextInput::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('jumlah_masuk')
                    ->label('Jumlah Masuk')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sparepart.kode_sparepart')
            ->columns([
                TextColumn::make('sparepart.kode_sparepart')
                    ->label('Kode Sparepart')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_masuk')
                    ->label('Jumlah Masuk')
                    ->searchable()
                    ->sortable(),
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
            ->bulkActions([
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
