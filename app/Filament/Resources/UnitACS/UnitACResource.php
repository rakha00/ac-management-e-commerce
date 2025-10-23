<?php

namespace App\Filament\Resources\UnitACS;

use App\Filament\Resources\UnitACS\Pages\CreateUnitAC;
use App\Filament\Resources\UnitACS\Pages\EditUnitAC;
use App\Filament\Resources\UnitACS\Pages\ListUnitACS;
use App\Filament\Resources\UnitACS\Schemas\UnitACForm;
use App\Filament\Resources\UnitACS\Tables\UnitACSTable;
use App\Models\UnitAC;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnitACResource extends Resource
{
    protected static ?string $model = UnitAC::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Unit AC';

    public static function form(Schema $schema): Schema
    {
        return UnitACForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitACSTable::configure($table);
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
            'index' => ListUnitACS::route('/'),
            'create' => CreateUnitAC::route('/create'),
            'edit' => EditUnitAC::route('/{record}/edit'),
        ];
    }
}
