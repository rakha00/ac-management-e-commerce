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

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return $user->karyawan->jabatan === 'admin';
    }

    protected static ?string $pluralModelLabel = 'Konsumen';

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
