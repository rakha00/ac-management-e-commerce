<?php

namespace App\Filament\Resources\Laporan;

use App\Filament\Resources\Laporan\Pages\CreateLaporan;
use App\Filament\Resources\Laporan\Pages\EditLaporan;
use App\Filament\Resources\Laporan\Pages\ListLaporan;
use App\Filament\Resources\Laporan\Schemas\LaporanForm;
use App\Filament\Resources\Laporan\Tables\LaporanTable;
use App\Models\Laporan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'laporan';

    protected static ?string $slug = 'laporan';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return $query->whereRaw('1 = 0');
        }

        $jabatan = $user->karyawan->jabatan;

        if ($jabatan === 'admin') {
            return $query;
        }

        if ($jabatan === 'teknisi') {
            return $query->where('karyawan_id', $user->karyawan->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return LaporanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanTable::configure($table);
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
            'index' => ListLaporan::route('/'),
            'create' => CreateLaporan::route('/create'),
            'edit' => EditLaporan::route('/{record}/edit'),
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
