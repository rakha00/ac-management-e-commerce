<?php

namespace App\Filament\Pages;

use App\Models\Absensi;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\ImageEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RiwayatAbsensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.riwayat-absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

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

    public bool $sudahAbsenHariIni = false;

    public function mount(): void
    {
        $karyawanId = auth()->user()?->karyawan?->id ?? 0;
        $this->sudahAbsenHariIni = Absensi::where('karyawan_id', $karyawanId)
            ->whereDate('waktu_absen', now()->toDateString())
            ->exists();
    }

    public function table(Table $table): Table
    {
        $karyawanId = auth()->user()?->karyawan?->id ?? 0;

        return $table
            ->query(
                Absensi::query()
                    ->where('karyawan_id', $karyawanId)
                    ->orderByDesc('waktu_absen')
            )
            ->columns([
                TextColumn::make('waktu_absen')
                    ->label('Tanggal Absen')
                    ->date()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('waktu_absen_time')
                    ->label('Waktu Absen')
                    ->getStateUsing(fn ($record) => \Carbon\Carbon::parse($record->waktu_absen)->format('H:i:s'))
                    ->sortable(),

                IconColumn::make('is_telat')
                    ->label('Tepat Waktu')
                    ->state(function (Absensi $record): bool {
                        return ! $record->is_telat;
                    })
                    ->boolean(),

                IconColumn::make('is_terkonfirmasi')
                    ->label('Terkonfirmasi')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('waktu_absen')
                    ->form([
                        DatePicker::make('waktu_absen'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['waktu_absen'],
                                fn (Builder $query, $date): Builder => $query->whereDate('waktu_absen', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('lihat-foto-bukti')
                    ->label('Lihat Foto Bukti')
                    ->icon('heroicon-o-photo')
                    ->visible(fn ($record) => ! empty($record->foto_bukti))
                    ->schema([
                        Grid::make()
                            ->schema([
                                ImageEntry::make('foto_bukti')
                                    ->disk('local')
                                    ->imageSize(334)
                                    ->getStateUsing(function ($record) {
                                        return $record->foto_bukti ? route('storage.private', $record->foto_bukti) : null;
                                    }),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->modalWidth('sm')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Action::make('kirim-ulang-foto')
                    ->label('Kirim Ulang Foto')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => ! empty($record->foto_bukti) && ! $record->is_terkonfirmasi)
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Ulang Foto Bukti')
                    ->modalDescription('Apakah Anda yakin ingin mengirim ulang foto bukti kehadiran ini?')
                    ->modalSubmitActionLabel('Kirim Ulang')
                    ->action(function ($record) {
                        // Redirect to the photo capture page with the token
                        return redirect()->to(route('absensi.foto-bukti').'?token='.$record->token);
                    }),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->deferFilters(false);
    }
}
