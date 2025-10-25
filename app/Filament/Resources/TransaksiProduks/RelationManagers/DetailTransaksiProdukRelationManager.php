<?php

namespace App\Filament\Resources\TransaksiProduks\RelationManagers;

use App\Models\TransaksiProduk;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailTransaksiProdukRelationManager extends RelationManager
{
    // Relationship name must match the Eloquent relation on parent model
    protected static string $relationship = 'detailTransaksiProduk';

    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('unit_ac_id')
                    ->label('SKU')
                    ->options(fn() => UnitAC::query()
                        ->orderBy('sku')
                        ->pluck('sku', 'id')
                        ->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $unit = UnitAC::find($state);
                        if ($unit) {
                            $set('sku', $unit->sku);
                            $set('nama_unit', $unit->nama_merk);
                            $set('harga_dealer', $unit->harga_dealer);
                            $set('harga_ecommerce', $unit->harga_ecommerce);
                            $set('harga_retail', $unit->harga_retail);
                        }
                    }),

                TextInput::make('sku')
                    ->disabled(),

                TextInput::make('nama_unit')
                    ->disabled(),

                TextInput::make('jumlah_keluar')
                    ->numeric()
                    ->required(),

                TextInput::make('harga_modal')
                    ->numeric()
                    ->required()
                    ->default(0),

                TextInput::make('harga_jual')
                    ->numeric()
                    ->required()
                    ->default(0),

                TextInput::make('harga_dealer')
                    ->numeric()
                    ->disabled(),

                TextInput::make('harga_ecommerce')
                    ->numeric()
                    ->disabled(),

                TextInput::make('harga_retail')
                    ->numeric()
                    ->disabled(),

                Textarea::make('keterangan')
                    ->nullable(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->recordTitle(fn($record): string => "{$record->sku} ({$record->jumlah_keluar})")
            ->columns([
                TextColumn::make('sku')->sortable()->searchable(),
                TextColumn::make('nama_unit')->sortable()->searchable(),
                TextColumn::make('jumlah_keluar')->numeric()->sortable(),
                TextColumn::make('harga_modal')->numeric()->sortable(),
                TextColumn::make('harga_jual')->numeric()->sortable(),
                TextColumn::make('subtotal_modal')
                    ->label('Subtotal Modal')
                    ->state(fn($record) => (float) $record->harga_modal * (int) $record->jumlah_keluar)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal_penjualan')
                    ->label('Subtotal Penjualan')
                    ->state(fn($record) => (float) $record->harga_jual * (int) $record->jumlah_keluar)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal_keuntungan')
                    ->label('Subtotal Keuntungan')
                    ->state(fn($record) => max(((float) $record->harga_jual - (float) $record->harga_modal) * (int) $record->jumlah_keluar, 0))
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->after(fn() => $this->recalculateParent()),
                DeleteAction::make()->after(fn() => $this->recalculateParent()),
                ForceDeleteAction::make()->after(fn() => $this->recalculateParent()),
                RestoreAction::make()->after(fn() => $this->recalculateParent()),
            ])
            ->headerActions([
                CreateAction::make()->after(fn() => $this->recalculateParent()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn() => $this->recalculateParent()),
                    ForceDeleteBulkAction::make()->after(fn() => $this->recalculateParent()),
                    RestoreBulkAction::make()->after(fn() => $this->recalculateParent()),
                ]),
            ]);
    }

    protected function recalculateParent(): void
    {
        $owner = $this->getOwnerRecord();
        if ($owner instanceof TransaksiProduk) {
            $owner->recalcFromDetails();
        }
    }
}
