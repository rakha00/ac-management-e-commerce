<?php

namespace App\Filament\Resources\PengeluaranKantor;

use App\Filament\Resources\PengeluaranKantor\Pages\CreatePengeluaranKantor;
use App\Filament\Resources\PengeluaranKantor\Pages\EditPengeluaranKantor;
use App\Filament\Resources\PengeluaranKantor\Pages\ListPengeluaranKantor;
use App\Filament\Resources\PengeluaranKantor\Schemas\PengeluaranKantorForm;
use App\Filament\Resources\PengeluaranKantor\Tables\PengeluaranKantorTable;
use App\Models\PengeluaranKantor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PengeluaranKantorResource extends Resource
{
    protected static ?string $model = PengeluaranKantor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'staff'], true);
    }

    protected static ?string $pluralModelLabel = 'pengeluaran kantor';

    protected static ?string $slug = 'pengeluaran-kantor';

    protected static ?string $recordTitleAttribute = 'tanggal';

    public static function form(Schema $schema): Schema
    {
        return PengeluaranKantorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengeluaranKantorTable::configure($table);
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
            'index' => ListPengeluaranKantor::route('/'),
            'create' => CreatePengeluaranKantor::route('/create'),
            'edit' => EditPengeluaranKantor::route('/{record}/edit'),
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
