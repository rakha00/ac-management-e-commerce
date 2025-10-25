<?php

namespace App\Filament\Resources\TransaksiProduks;

use App\Filament\Resources\TransaksiProduks\Pages\CreateTransaksiProduk;
use App\Filament\Resources\TransaksiProduks\Pages\EditTransaksiProduk;
use App\Filament\Resources\TransaksiProduks\Pages\ListTransaksiProduks;
use App\Filament\Resources\TransaksiProduks\Schemas\TransaksiProdukForm;
use App\Filament\Resources\TransaksiProduks\Tables\TransaksiProduksTable;
use App\Filament\Resources\TransaksiProduks\RelationManagers\DetailTransaksiProdukRelationManager;
use App\Models\TransaksiProduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransaksiProdukResource extends Resource
{
    protected static ?string $model = TransaksiProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TransaksiProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiProduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailTransaksiProdukRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiProduks::route('/'),
            'create' => CreateTransaksiProduk::route('/create'),
            'edit' => EditTransaksiProduk::route('/{record}/edit'),
        ];
    }
}
