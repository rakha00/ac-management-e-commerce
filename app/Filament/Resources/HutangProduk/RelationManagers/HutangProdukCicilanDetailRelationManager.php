<?php

namespace App\Filament\Resources\HutangProduk\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HutangProdukCicilanDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'hutangProdukCicilanDetail';

    protected static ?string $pluralLabel = 'cicilan hutang produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nominal_cicilan')
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->maxValue(fn () => $this->getOwnerRecord()->sisa_hutang)
                    ->helperText(fn () => 'Sisa hutang: Rp '.number_format($this->getOwnerRecord()->sisa_hutang)),
                DatePicker::make('tanggal_cicilan')
                    ->required(),
            ])
            ->columns(2);

    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_cicilan')
                    ->date()
                    ->sortable(),
                TextColumn::make('nominal_cicilan')
                    ->numeric()
                    ->money(currency: 'IDR', decimalPlaces: 0, locale: 'id_ID')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
