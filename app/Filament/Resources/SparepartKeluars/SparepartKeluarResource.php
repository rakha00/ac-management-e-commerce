<?php

namespace App\Filament\Resources\SparepartKeluars;

use App\Filament\Resources\SparepartKeluars\Pages\CreateSparepartKeluar;
use App\Filament\Resources\SparepartKeluars\Pages\EditSparepartKeluar;
use App\Filament\Resources\SparepartKeluars\Pages\ListSparepartKeluars;
use App\Filament\Resources\SparepartKeluars\Schemas\SparepartKeluarForm;
use App\Filament\Resources\SparepartKeluars\Tables\SparepartKeluarsTable;
use App\Models\SparepartKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SparepartKeluarResource extends Resource
{
    protected static ?string $model = SparepartKeluar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SparepartKeluarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparepartKeluarsTable::configure($table);
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
            'index' => ListSparepartKeluars::route('/'),
            'create' => CreateSparepartKeluar::route('/create'),
            'edit' => EditSparepartKeluar::route('/{record}/edit'),
        ];
    }
}
