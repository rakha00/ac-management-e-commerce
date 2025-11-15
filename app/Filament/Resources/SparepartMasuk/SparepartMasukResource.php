<?php

namespace App\Filament\Resources\SparepartMasuk;

use App\Filament\Resources\SparepartMasuk\RelationManagers\SparepartMasukDetailRelationManager;
use App\Filament\Resources\SparepartMasuk\Schemas\SparepartMasukForm;
use App\Filament\Resources\SparepartMasuk\Tables\SparepartMasukTable;
use App\Models\SparepartMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SparepartMasukResource extends Resource
{
    protected static ?string $model = SparepartMasuk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Sparepart';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'sparepart masuk';

    protected static ?string $slug = 'sparepart-masuk';

    public static function form(Schema $schema): Schema
    {
        return SparepartMasukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparepartMasukTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSparepartMasuk::route('/'),
            'create' => Pages\CreateSparepartMasuk::route('/create'),
            'edit' => Pages\EditSparepartMasuk::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            SparepartMasukDetailRelationManager::class,
        ];
    }
}
