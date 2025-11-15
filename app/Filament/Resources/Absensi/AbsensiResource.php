<?php

namespace App\Filament\Resources\Absensi;

use App\Filament\Resources\Absensi\Pages\EditAbsensi;
use App\Filament\Resources\Absensi\Pages\ListAbsensi;
use App\Filament\Resources\Absensi\Schemas\AbsensiForm;
use App\Filament\Resources\Absensi\Tables\AbsensiTable;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'waktu_absen';

    protected static ?string $pluralModelLabel = 'Absensi';

    protected static ?string $slug = 'absensi';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->karyawan?->jabatan === 'admin' || auth()->user()?->karyawan?->jabatan === 'staff';
    }

    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensiTable::configure($table);
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
        $date = $record->waktu_absen
            ? Carbon::parse($record->waktu_absen)->translatedFormat('d M Y')
            : null;

        return $date ? "{$name} - {$date}" : $name;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensi::route('/'),
            // 'edit' => EditAbsensi::route('/{record}/edit'),
        ];
    }
}
