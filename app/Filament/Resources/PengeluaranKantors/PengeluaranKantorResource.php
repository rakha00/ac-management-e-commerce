<?php

namespace App\Filament\Resources\PengeluaranKantors;

use App\Filament\Resources\PengeluaranKantors\Pages\CreatePengeluaranKantor;
use App\Filament\Resources\PengeluaranKantors\Pages\EditPengeluaranKantor;
use App\Filament\Resources\PengeluaranKantors\Pages\ListPengeluaranKantors;
use App\Filament\Resources\PengeluaranKantors\Schemas\PengeluaranKantorForm;
use App\Filament\Resources\PengeluaranKantors\Tables\PengeluaranKantorsTable;
use App\Models\PengeluaranKantor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengeluaranKantorResource extends Resource
{
    protected static ?string $model = PengeluaranKantor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pengeluaran Kantor';

    public static function form(Schema $schema): Schema
    {
        return PengeluaranKantorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengeluaranKantorsTable::configure($table);
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
            'index' => ListPengeluaranKantors::route('/'),
            'create' => CreatePengeluaranKantor::route('/create'),
            'edit' => EditPengeluaranKantor::route('/{record}/edit'),
        ];
    }
}
