<?php

namespace App\Filament\Resources\UnitAC;

use App\Filament\Resources\UnitAC\Pages\CreateUnitAC;
use App\Filament\Resources\UnitAC\Pages\EditUnitAC;
use App\Filament\Resources\UnitAC\Pages\ListUnitAC;
use App\Filament\Resources\UnitAC\RelationManagers\HargaHistoryRelationManager;
use App\Filament\Resources\UnitAC\Schemas\UnitACForm;
use App\Filament\Resources\UnitAC\Tables\UnitACTable;
use App\Models\UnitAC;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UnitACResource extends Resource
{
    protected static ?string $model = UnitAC::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'unit AC';

    protected static ?string $pluralModelLabel = 'unit AC';

    protected static ?string $slug = 'unit-ac';

    protected static ?string $recordTitleAttribute = 'sku';

    public static function form(Schema $schema): Schema
    {
        return UnitACForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitACTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HargaHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnitAC::route('/'),
            'create' => CreateUnitAC::route('/create'),
            'edit' => EditUnitAC::route('/{record}/edit'),
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
