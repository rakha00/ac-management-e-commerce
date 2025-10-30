<?php

namespace App\Filament\Resources\PiutangJasa;

use App\Filament\Resources\PiutangJasa\Pages\CreatePiutangJasa;
use App\Filament\Resources\PiutangJasa\Pages\EditPiutangJasa;
use App\Filament\Resources\PiutangJasa\Pages\ListPiutangJasa;
use App\Filament\Resources\PiutangJasa\Schemas\PiutangJasaForm;
use App\Filament\Resources\PiutangJasa\Tables\PiutangJasaTable;
use App\Models\PiutangJasa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PiutangJasaResource extends Resource
{
    protected static ?string $model = PiutangJasa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralModelLabel = 'piutang jasa';

    protected static ?string $slug = 'piutang-jasa';

    public static function form(Schema $schema): Schema
    {
        return PiutangJasaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangJasaTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PiutangJasaCicilanDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangJasa::route('/'),
            'create' => CreatePiutangJasa::route('/create'),
            'edit' => EditPiutangJasa::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
