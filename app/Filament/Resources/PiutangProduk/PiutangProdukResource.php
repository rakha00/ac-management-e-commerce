<?php

namespace App\Filament\Resources\PiutangProduk;

use App\Filament\Resources\PiutangProduk\Pages\CreatePiutangProduk;
use App\Filament\Resources\PiutangProduk\Pages\EditPiutangProduk;
use App\Filament\Resources\PiutangProduk\Pages\ListPiutangProduk;
use App\Filament\Resources\PiutangProduk\RelationManagers\PiutangProdukCicilanRelationDetailManager;
use App\Filament\Resources\PiutangProduk\Schemas\PiutangProdukForm;
use App\Filament\Resources\PiutangProduk\Tables\PiutangProdukTable;
use App\Models\PiutangProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PiutangProdukResource extends Resource
{
    protected static ?string $model = PiutangProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'piutang produk';

    protected static ?string $slug = 'piutang-produk';

    public static function form(Schema $schema): Schema
    {
        return PiutangProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangProdukTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PiutangProdukCicilanRelationDetailManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangProduk::route('/'),
            'create' => CreatePiutangProduk::route('/create'),
            'edit' => EditPiutangProduk::route('/{record}/edit'),
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
