<?php

namespace App\Filament\Resources\SparepartMasuks;

use App\Filament\Resources\SparepartMasuks\Pages\CreateSparepartMasuk;
use App\Filament\Resources\SparepartMasuks\Pages\EditSparepartMasuk;
use App\Filament\Resources\SparepartMasuks\Pages\ListSparepartMasuks;
use App\Filament\Resources\SparepartMasuks\Schemas\SparepartMasukForm;
use App\Filament\Resources\SparepartMasuks\Tables\SparepartMasuksTable;
use App\Models\SparepartMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SparepartMasukResource extends Resource
{
    protected static ?string $model = SparepartMasuk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SparepartMasukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparepartMasuksTable::configure($table);
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
            'index' => ListSparepartMasuks::route('/'),
            'create' => CreateSparepartMasuk::route('/create'),
            'edit' => EditSparepartMasuk::route('/{record}/edit'),
        ];
    }
}
