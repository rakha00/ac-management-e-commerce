<?php

namespace App\Filament\Resources\HutangProduks;

use App\Filament\Resources\HutangProduks\Pages\CreateHutangProduk;
use App\Filament\Resources\HutangProduks\Pages\EditHutangProduk;
use App\Filament\Resources\HutangProduks\Pages\ListHutangProduks;
use App\Filament\Resources\HutangProduks\Schemas\HutangProdukForm;
use App\Filament\Resources\HutangProduks\Tables\HutangProduksTable;
use App\Models\HutangProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HutangProdukResource extends Resource
{
    protected static ?string $model = HutangProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $pluralModelLabel = 'hutang produk';

    protected static ?string $slug = 'hutang-produk';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return $user->karyawan->jabatan === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return HutangProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HutangProduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailHutangProdukCicilanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHutangProduks::route('/'),
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
