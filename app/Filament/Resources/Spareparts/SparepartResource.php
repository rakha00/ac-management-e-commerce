<?php

namespace App\Filament\Resources\Spareparts;

use App\Filament\Resources\Spareparts\Pages\CreateSparepart;
use App\Filament\Resources\Spareparts\Pages\EditSparepart;
use App\Filament\Resources\Spareparts\Pages\ListSpareparts;
use App\Filament\Resources\Spareparts\Schemas\SparepartForm;
use App\Filament\Resources\Spareparts\Tables\SparepartsTable;
use App\Models\Sparepart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'gudang'], true);
    }

    public static function form(Schema $schema): Schema
    {
        return SparepartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparepartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HargaHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpareparts::route('/'),
            'create' => CreateSparepart::route('/create'),
            'edit' => EditSparepart::route('/{record}/edit'),
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
