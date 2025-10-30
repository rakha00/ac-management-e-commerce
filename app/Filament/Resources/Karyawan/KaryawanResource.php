<?php

namespace App\Filament\Resources\Karyawan;

use App\Filament\Resources\Karyawan\Pages\CreateKaryawan;
use App\Filament\Resources\Karyawan\Pages\EditKaryawan;
use App\Filament\Resources\Karyawan\Pages\ListKaryawan;
use App\Filament\Resources\Karyawan\Pages\ManageKaryawanPenghasilanDetail;
use App\Filament\Resources\Karyawan\RelationManagers\KaryawanPenghasilanDetailRelationManager;
use App\Filament\Resources\Karyawan\Schemas\KaryawanForm;
use App\Filament\Resources\Karyawan\Tables\KaryawanTable;
use App\Models\Karyawan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'karyawan';

    protected static ?string $slug = 'karyawan';

    public static function form(Schema $schema): Schema
    {
        return KaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KaryawanTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            KaryawanPenghasilanDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKaryawan::route('/'),
            'create' => CreateKaryawan::route('/create'),
            'edit' => EditKaryawan::route('/{record}/edit'),
            'penghasilan' => ManageKaryawanPenghasilanDetail::route('/{record}/penghasilan'),
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
