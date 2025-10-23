<?php

namespace App\Filament\Resources\PiutangJasas;

use App\Filament\Resources\PiutangJasas\Pages\CreatePiutangJasa;
use App\Filament\Resources\PiutangJasas\Pages\EditPiutangJasa;
use App\Filament\Resources\PiutangJasas\Pages\ListPiutangJasas;
use App\Filament\Resources\PiutangJasas\Schemas\PiutangJasaForm;
use App\Filament\Resources\PiutangJasas\Tables\PiutangJasasTable;
use App\Models\PiutangJasa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PiutangJasaResource extends Resource
{
    protected static ?string $model = PiutangJasa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $pluralModelLabel = 'piutang jasa';

    protected static ?string $slug = 'piutang-jasa';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return $user->karyawan->jabatan === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return PiutangJasaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiutangJasasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPiutangJasaCicilanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangJasas::route('/'),
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
