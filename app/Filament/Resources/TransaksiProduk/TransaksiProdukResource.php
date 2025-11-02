<?php

namespace App\Filament\Resources\TransaksiProduk;

use App\Filament\Resources\TransaksiProduk\Pages\CreateTransaksiProduk;
use App\Filament\Resources\TransaksiProduk\Pages\EditTransaksiProduk;
use App\Filament\Resources\TransaksiProduk\Pages\ListTransaksiProduk;
use App\Filament\Resources\TransaksiProduk\RelationManagers\TransaksiProdukDetailRelationManager;
use App\Filament\Resources\TransaksiProduk\Schemas\TransaksiProdukForm;
use App\Filament\Resources\TransaksiProduk\Tables\TransaksiProdukTable;
use App\Models\TransaksiProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransaksiProdukResource extends Resource
{
    protected static ?string $model = TransaksiProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'sales'], true);
    }

    protected static ?string $recordTitleAttribute = 'nomor_invoice';

    protected static ?string $pluralModelLabel = 'transaksi produk';

    protected static ?string $slug = 'transaksi-produk';

    public static function form(Schema $schema): Schema
    {
        return TransaksiProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiProdukTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransaksiProdukDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiProduk::route('/'),
            'create' => CreateTransaksiProduk::route('/create'),
            'edit' => EditTransaksiProduk::route('/{record}/edit'),
        ];
    }
}
