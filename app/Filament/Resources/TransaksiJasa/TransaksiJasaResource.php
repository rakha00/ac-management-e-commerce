<?php

namespace App\Filament\Resources\TransaksiJasa;

use App\Filament\Resources\TransaksiJasa\Pages\CreateTransaksiJasa;
use App\Filament\Resources\TransaksiJasa\Pages\EditTransaksiJasa;
use App\Filament\Resources\TransaksiJasa\Pages\ListTransaksiJasa;
use App\Filament\Resources\TransaksiJasa\RelationManagers\TransaksiJasaDetailRelationManager;
use App\Filament\Resources\TransaksiJasa\Schemas\TransaksiJasaForm;
use App\Filament\Resources\TransaksiJasa\Tables\TransaksiJasaTable;
use App\Models\TransaksiJasa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransaksiJasaResource extends Resource
{
    protected static ?string $model = TransaksiJasa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'transaksi jasa';

    protected static ?string $slug = 'transaksi-jasa';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'helper', 'teknisi'], true);
    }

    public static function form(Schema $schema): Schema
    {
        return TransaksiJasaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiJasaTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransaksiJasaDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiJasa::route('/'),
            'create' => CreateTransaksiJasa::route('/create'),
            'edit' => EditTransaksiJasa::route('/{record}/edit'),
        ];
    }
}
