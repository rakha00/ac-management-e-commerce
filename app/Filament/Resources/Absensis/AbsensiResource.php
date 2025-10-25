<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use UnitEnum;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'tanggal';

    protected static ?string $pluralModelLabel = 'Absensi';

    protected static ?string $slug = 'absensi';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->karyawan?->jabatan === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return (string) static::getModelLabel();
        }

        $name = optional($record->karyawan)->nama ?? 'Tanpa Nama';
        $date = $record->tanggal
            ? Carbon::parse($record->tanggal)->translatedFormat('d M Y')
            : null;

        return $date ? "{$name} - {$date}" : $name;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensis::route('/'),
            // 'create' => CreateAbsensi::route('/create'),
            'edit' => EditAbsensi::route('/{record}/edit'),
        ];
    }
}
