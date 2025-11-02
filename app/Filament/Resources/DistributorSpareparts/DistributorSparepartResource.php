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
use UnitEnum;

class DistributorSparepartResource extends Resource
{
    protected static ?string $model = DistributorSparepart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return $user->karyawan->jabatan === 'admin';
    }

    protected static ?string $pluralModelLabel = 'Distributor Sparepart';

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
