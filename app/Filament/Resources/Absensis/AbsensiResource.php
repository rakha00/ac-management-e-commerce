<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Resources\Absensis\Pages\ViewAbsensi;
use App\Filament\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Resources\Absensis\Schemas\AbsensiInfolist;
use App\Filament\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'tanggal';

    // Label navigasi dalam Bahasa Indonesia
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $modelLabel = 'Absensi';
    protected static ?string $pluralModelLabel = 'Absensi';
    protected static ?string $slug = 'absensi';



    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AbsensiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensisTable::configure($table);
    }

    // Tampilkan resource ini di navigasi hanya untuk admin
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->karyawan?->jabatan === 'admin';
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
            'index' => ListAbsensis::route('/'),
            'create' => CreateAbsensi::route('/create'),
            'view' => ViewAbsensi::route('/{record}'),
            'edit' => EditAbsensi::route('/{record}/edit'),
        ];
    }
}
