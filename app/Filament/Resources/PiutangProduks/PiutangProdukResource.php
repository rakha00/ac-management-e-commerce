<?php

namespace App\Filament\Resources\PiutangProduks;

use App\Filament\Resources\PiutangProduks\Pages\CreatePiutangProduk;
use App\Filament\Resources\PiutangProduks\Pages\EditPiutangProduk;
use App\Filament\Resources\PiutangProduks\Pages\ListPiutangProduks;
use App\Filament\Resources\PiutangProduks\Schemas\PiutangProdukForm;
use App\Filament\Resources\PiutangProduks\Tables\PiutangProduksTable;
use App\Models\PiutangProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PiutangProdukResource extends Resource
{
    protected static ?string $model = PiutangProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $pluralModelLabel = 'piutang produk';

    protected static ?string $slug = 'piutang-produk';

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
        return PiutangProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangProduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPiutangProdukCicilanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangProduks::route('/'),
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
