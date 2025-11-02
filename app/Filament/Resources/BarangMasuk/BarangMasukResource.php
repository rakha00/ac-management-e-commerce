<?php

namespace App\Filament\Resources\BarangMasuk;

use App\Filament\Resources\BarangMasuk\Pages\CreateBarangMasuk;
use App\Filament\Resources\BarangMasuk\Pages\EditBarangMasuk;
use App\Filament\Resources\BarangMasuk\Pages\ListBarangMasuk;
use App\Filament\Resources\BarangMasuk\RelationManagers\BarangMasukDetailRelationManager;
use App\Filament\Resources\BarangMasuk\Schemas\BarangMasukForm;
use App\Filament\Resources\BarangMasuk\Tables\BarangMasukTable;
use App\Models\BarangMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'gudang'], true);
    }

    protected static ?string $recordTitleAttribute = 'nomor_barang_masuk';

    protected static ?string $pluralModelLabel = 'barang masuk';

    protected static ?string $slug = 'barang-masuk';

    public static function form(Schema $schema): Schema
    {
        return BarangMasukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangMasukTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BarangMasukDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarangMasuk::route('/'),
            'create' => CreateBarangMasuk::route('/create'),
            'edit' => EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}
