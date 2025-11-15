<?php

namespace App\Filament\Resources\Konsumen;

use App\Filament\Resources\Konsumen\Schemas\KonsumenForm;
use App\Filament\Resources\Konsumen\Tables\KonsumenTable;
use App\Models\Konsumen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KonsumenResource extends Resource
{
    protected static ?string $model = Konsumen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    protected static ?string $pluralModelLabel = 'Toko/Konsumen';

    protected static ?string $slug = 'toko-konsumen';

    public static function form(Schema $schema): Schema
    {
        return KonsumenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KonsumenTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKonsumen::route('/'),
            'create' => Pages\CreateKonsumen::route('/create'),
            'edit' => Pages\EditKonsumen::route('/{record}/edit'),
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
