<?php

namespace App\Filament\Resources\HutangProduk;

use App\Filament\Resources\HutangProduk\Pages\CreateHutangProduk;
use App\Filament\Resources\HutangProduk\Pages\EditHutangProduk;
use App\Filament\Resources\HutangProduk\Pages\ListHutangProduk;
use App\Filament\Resources\HutangProduk\RelationManagers\HutangProdukCicilanDetailRelationManager;
use App\Filament\Resources\HutangProduk\Schemas\HutangProdukForm;
use App\Filament\Resources\HutangProduk\Tables\HutangProdukTable;
use App\Models\HutangProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class HutangProdukResource extends Resource
{
    protected static ?string $model = HutangProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'hutang produk';

    protected static ?string $slug = 'hutang-produk';

    public static function form(Schema $schema): Schema
    {
        return HutangProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HutangProdukTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HutangProdukCicilanDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHutangProduk::route('/'),
            'create' => CreateHutangProduk::route('/create'),
            'edit' => EditHutangProduk::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
