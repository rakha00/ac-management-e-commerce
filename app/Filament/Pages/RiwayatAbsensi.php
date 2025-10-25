<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class RiwayatAbsensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.riwayat-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $jabatan = auth()->user()?->karyawan?->jabatan;

        return in_array($jabatan, ['gudang', 'helper', 'teknisi', 'staff', 'sales']);
    }

    public function getTitle(): string
    {
        return 'Riwayat Absensi';
    }

    public function table(Table $table): Table
    {
        $karyawanId = auth()->user()?->karyawan?->id ?? 0;

        return $table
            ->query(
                Absensi::query()
                    ->where('karyawan_id', $karyawanId)
                    ->orderByDesc('tanggal')
                    ->orderByDesc('waktu_absen')
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('waktu_absen')
                    ->label('Waktu')
                    ->dateTime('H:i:s')
                    ->sortable(),

                IconColumn::make('telat')
                    ->label('Telat')
                    ->boolean(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap(),

                IconColumn::make('terkonfirmasi')
                    ->label('Terkonfirmasi')
                    ->boolean(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
