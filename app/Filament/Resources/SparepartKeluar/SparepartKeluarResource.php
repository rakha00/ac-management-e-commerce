<?php

namespace App\Filament\Resources\SparepartKeluar;

use App\Filament\Resources\SparepartKeluar\Pages\CreateSparepartKeluar;
use App\Filament\Resources\SparepartKeluar\Pages\EditSparepartKeluar;
use App\Filament\Resources\SparepartKeluar\Pages\ListSparepartKeluar;
use App\Filament\Resources\SparepartKeluar\RelationManagers\SparepartKeluarDetailRelationManager;
use App\Filament\Resources\SparepartKeluar\Schemas\SparepartKeluarForm;
use App\Filament\Resources\SparepartKeluar\Tables\SparepartKeluarTable;
use App\Models\SparepartKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SparepartKeluarResource extends Resource
{
    protected static ?string $model = SparepartKeluar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 8;

    protected static ?string $pluralModelLabel = 'sparepart keluar';

    protected static ?string $slug = 'sparepart-keluar';

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
        return SparepartKeluarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparepartKeluarTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SparepartKeluarDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSparepartKeluar::route('/'),
            'create' => CreateSparepartKeluar::route('/create'),
            'edit' => EditSparepartKeluar::route('/{record}/edit'),
        ];
    }
}
