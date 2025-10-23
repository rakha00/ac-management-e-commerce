<?php

namespace App\Filament\Resources\Laporans;

use App\Filament\Resources\Laporans\Pages\CreateLaporan;
use App\Filament\Resources\Laporans\Pages\EditLaporan;
use App\Filament\Resources\Laporans\Pages\ListLaporans;
use App\Filament\Resources\Laporans\Schemas\LaporanForm;
use App\Filament\Resources\Laporans\Tables\LaporansTable;
use App\Models\Laporan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $pluralModelLabel = 'laporan';

    protected static ?string $slug = 'laporan';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->karyawan) {
            return false;
        }

        return in_array($user->karyawan->jabatan, ['admin', 'teknisi'], true);
    }

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
        return LaporansTable::configure($table);
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
            'index' => ListLaporans::route('/'),
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
