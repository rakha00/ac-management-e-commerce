<?php

namespace App\Filament\Resources\KonsumenSpareparts;

use App\Filament\Resources\KonsumenSpareparts\Pages\CreateKonsumenSparepart;
use App\Filament\Resources\KonsumenSpareparts\Pages\EditKonsumenSparepart;
use App\Filament\Resources\KonsumenSpareparts\Pages\ListKonsumenSpareparts;
use App\Filament\Resources\KonsumenSpareparts\Schemas\KonsumenSparepartForm;
use App\Filament\Resources\KonsumenSpareparts\Tables\KonsumenSparepartsTable;
use App\Models\KonsumenSparepart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KonsumenSparepartResource extends Resource
{
    protected static ?string $model = KonsumenSparepart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KonsumenSparepartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KonsumenSparepartsTable::configure($table);
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
            'index' => ListKonsumenSpareparts::route('/'),
            'create' => CreateKonsumenSparepart::route('/create'),
            'edit' => EditKonsumenSparepart::route('/{record}/edit'),
        ];
    }
}
