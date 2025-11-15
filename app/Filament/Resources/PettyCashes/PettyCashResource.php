<?php

namespace App\Filament\Resources\PettyCashes;

use App\Filament\Resources\PettyCashes\Pages\CreatePettyCash;
use App\Filament\Resources\PettyCashes\Pages\EditPettyCash;
use App\Filament\Resources\PettyCashes\Pages\ListPettyCashes;
use App\Filament\Resources\PettyCashes\Schemas\PettyCashForm;
use App\Filament\Resources\PettyCashes\Tables\PettyCashesTable;
use App\Models\PettyCash;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PettyCashResource extends Resource
{
    protected static ?string $model = PettyCash::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return $user->karyawan->jabatan === 'admin';
    }

    protected static ?string $recordTitleAttribute = 'tanggal';

    public static function form(Schema $schema): Schema
    {
        return PettyCashForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PettyCashesTable::configure($table);
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
            'index' => ListPettyCashes::route('/'),
            'create' => CreatePettyCash::route('/create'),
            'edit' => EditPettyCash::route('/{record}/edit'),
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
