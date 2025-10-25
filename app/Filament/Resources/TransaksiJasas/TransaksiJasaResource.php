<?php

namespace App\Filament\Resources\TransaksiJasas;

use App\Filament\Resources\TransaksiJasas\Pages\CreateTransaksiJasa;
use App\Filament\Resources\TransaksiJasas\Pages\EditTransaksiJasa;
use App\Filament\Resources\TransaksiJasas\Pages\ListTransaksiJasas;
use App\Filament\Resources\TransaksiJasas\Schemas\TransaksiJasaForm;
use App\Filament\Resources\TransaksiJasas\Tables\TransaksiJasasTable;
use App\Filament\Resources\TransaksiJasas\RelationManagers\DetailTransaksiJasaRelationManager;
use App\Models\TransaksiJasa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransaksiJasaResource extends Resource
{
    protected static ?string $model = TransaksiJasa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TransaksiJasaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiJasasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailTransaksiJasaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiJasas::route('/'),
            'create' => CreateTransaksiJasa::route('/create'),
            'edit' => EditTransaksiJasa::route('/{record}/edit'),
        ];
    }
}
