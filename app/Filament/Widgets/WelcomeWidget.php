<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Schemas\Components\Section;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WelcomeWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->karyawan->jabatan !== 'admin';
    }

    public bool $sudahAbsenHariIni = false;

    public string $currentDateTime = '';

    public function mount(): void
    {
        $karyawanId = auth()->user()?->karyawan?->id ?? 0;
        $this->sudahAbsenHariIni = Absensi::where('karyawan_id', $karyawanId)
            ->whereDate('waktu_absen', now('Asia/Jakarta')->toDateString())
            ->exists();

        $this->currentDateTime = now('Asia/Jakarta')->format('d F Y, H:i').' WIB';
    }

    protected function getStats(): array
    {
        $namaKaryawan = auth()->user()?->karyawan?->nama ?? 'Karyawan';

        return [
            Section::make(new \Illuminate\Support\HtmlString('<span style="font-size: 1.5rem; font-weight: 600;">Halo, '.$namaKaryawan.'!</span>'))
                ->columns(2)
                ->schema([
                    Stat::make('Status Absensi Hari Ini', $this->sudahAbsenHariIni ? 'Sudah Absen ✅' : 'Belum Absen ❌')
                        ->description($this->sudahAbsenHariIni ? 'Anda sudah melakukan absensi' : 'Anda belum melakukan absensi')
                        ->chart([$this->sudahAbsenHariIni ? 1 : 0]),

                    Stat::make('Waktu Sekarang', $this->currentDateTime)
                        ->description('Tanggal dan jam saat ini (WIB)')
                        ->descriptionIcon('heroicon-m-clock')
                        ->chart([1]),
                ])
                ->columnSpanFull(),
        ];
    }
}
