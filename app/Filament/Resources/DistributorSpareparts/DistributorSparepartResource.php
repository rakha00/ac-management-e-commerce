<?php

namespace App\Filament\Resources\DistributorSpareparts;

use App\Filament\Resources\DistributorSpareparts\Pages\CreateDistributorSparepart;
use App\Filament\Resources\DistributorSpareparts\Pages\EditDistributorSparepart;
use App\Filament\Resources\DistributorSpareparts\Pages\ListDistributorSpareparts;
use App\Filament\Resources\DistributorSpareparts\Schemas\DistributorSparepartForm;
use App\Filament\Resources\DistributorSpareparts\Tables\DistributorSparepartsTable;
use App\Models\DistributorSparepart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DistributorSparepartResource extends Resource
{
    protected static ?string $model = DistributorSparepart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DistributorSparepartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DistributorSparepartsTable::configure($table);
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
            'index' => ListDistributorSpareparts::route('/'),
            'create' => CreateDistributorSparepart::route('/create'),
            'edit' => EditDistributorSparepart::route('/{record}/edit'),
        ];
    }
}
