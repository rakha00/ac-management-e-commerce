<?php

namespace App\Filament\Widgets;

use App\Models\BarangMasukDetail;
use App\Models\HutangProduk;
use App\Models\Karyawan;
use App\Models\PengeluaranKantor;
use App\Models\PiutangJasa;
use App\Models\PiutangProduk;
use App\Models\SparepartKeluarDetail;
use App\Models\SparepartMasukDetail;
use App\Models\TransaksiJasaDetail;
use App\Models\TransaksiProdukDetail;
use Filament\Schemas\Components\Section;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    public static function canView(): bool
    {
        return auth()->user()->karyawan->jabatan === 'admin';
    }

    protected function getStats(): array
    {
        // Get filter values from page filters
        $month = $this->pageFilters['month'] ?? null;
        $year = $this->pageFilters['year'] ?? null;

        // Validate month and year
        $validMonth = $month && in_array($month, range(1, 12));
        $validYear = $year && preg_match('/^\d{4}$/', $year);

        // Build date condition if both month and year are provided
        $dateCondition = null;
        if ($validMonth && $validYear) {
            $dateCondition = [
                'month' => $month,
                'year' => $year,
            ];
        } elseif ($validYear) {
            $dateCondition = [
                'year' => $year,
            ];
        }

        // Widget total keseluruhan gaji karyawan (hanya yang status_aktif = true, total gaji = gaji pokok + lembur + bonus - potongan, abaikan kasbon)
        $karyawanQuery = Karyawan::where('status_aktif', true);
        $totalGajiKaryawan = $karyawanQuery->get()->sum(function ($karyawan) use ($dateCondition) {
            $gajiPokok = $karyawan->gaji_pokok ?? 0;

            $penghasilanQuery = $karyawan->karyawanPenghasilanDetail();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $penghasilanQuery->whereMonth('tanggal', $dateCondition['month'])
                        ->whereYear('tanggal', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $penghasilanQuery->whereYear('tanggal', $dateCondition['year']);
                }
            }

            $penghasilanLain = $penghasilanQuery->selectRaw('SUM(lembur + bonus - potongan) as total_penghasilan')->first();

            $totalPenghasilanLain = $penghasilanLain ? $penghasilanLain->total_penghasilan : 0;

            return $gajiPokok + $totalPenghasilanLain;
        });

        // Widget barang masuk (seluruh total qty dari BarangMasukDetail)
        $barangMasukQuery = BarangMasukDetail::whereHas('barangMasuk', function ($q) use ($dateCondition) {
            $q->withoutTrashed();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $q->whereMonth('tanggal', $dateCondition['month'])
                        ->whereYear('tanggal', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $q->whereYear('tanggal', $dateCondition['year']);
                }
            }
        });
        $totalBarangMasuk = $barangMasukQuery->sum('jumlah_barang_masuk');

        // Widget hutang produk (seluruh sisa hutang)
        $hutangProdukQuery = HutangProduk::query();
        if ($dateCondition) {
            if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                $hutangProdukQuery->whereMonth('created_at', $dateCondition['month'])
                    ->whereYear('created_at', $dateCondition['year']);
            } elseif (isset($dateCondition['year'])) {
                $hutangProdukQuery->whereYear('created_at', $dateCondition['year']);
            }
        }
        $totalHutangProduk = $hutangProdukQuery->sum('sisa_hutang');

        // Widget transaksi produk (subtotal modal, subtotal penjualan, subtotal keuntungan dari TransaksiProdukDetail)
        $transaksiProdukQuery = TransaksiProdukDetail::whereHas('transaksiProduk', function ($q) use ($dateCondition) {
            $q->withoutTrashed();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $q->whereMonth('tanggal_transaksi', $dateCondition['month'])
                        ->whereYear('tanggal_transaksi', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $q->whereYear('tanggal_transaksi', $dateCondition['year']);
                }
            }
        });
        $transaksiProdukData = $transaksiProdukQuery->selectRaw('
            SUM(jumlah_keluar * harga_modal) as subtotal_modal,
            SUM(jumlah_keluar * harga_jual) as subtotal_penjualan,
            SUM((jumlah_keluar * harga_jual) - (jumlah_keluar * harga_modal)) as subtotal_keuntungan
        ')->first();

        // Widget piutang produk (seluruh sisa piutang)
        $piutangProdukQuery = PiutangProduk::query();
        if ($dateCondition) {
            if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                $piutangProdukQuery->whereMonth('created_at', $dateCondition['month'])
                    ->whereYear('created_at', $dateCondition['year']);
            } elseif (isset($dateCondition['year'])) {
                $piutangProdukQuery->whereYear('created_at', $dateCondition['year']);
            }
        }
        $totalPiutangProduk = $piutangProdukQuery->sum('sisa_piutang');

        // Widget sparepart masuk (seluruh total qty dari SparepartMasukDetail)
        $sparepartMasukQuery = SparepartMasukDetail::whereHas('sparepartMasuk', function ($q) use ($dateCondition) {
            $q->withoutTrashed();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $q->whereMonth('tanggal_masuk', $dateCondition['month'])
                        ->whereYear('tanggal_masuk', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $q->whereYear('tanggal_masuk', $dateCondition['year']);
                }
            }
        });
        $totalSparepartMasuk = $sparepartMasukQuery->sum('jumlah_masuk');

        // Widget sparepart keluar (subtotal modal, subtotal penjualan, subtotal keuntungan dari SparepartKeluarDetail)
        $sparepartKeluarQuery = SparepartKeluarDetail::whereHas('sparepartKeluar', function ($q) use ($dateCondition) {
            $q->withoutTrashed();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $q->whereMonth('created_at', $dateCondition['month'])
                        ->whereYear('created_at', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $q->whereYear('created_at', $dateCondition['year']);
                }
            }
        });
        $sparepartKeluarData = $sparepartKeluarQuery->selectRaw('
            SUM(jumlah_keluar * harga_modal) as subtotal_modal,
            SUM(jumlah_keluar * harga_jual) as subtotal_penjualan,
            SUM((jumlah_keluar * harga_jual) - (jumlah_keluar * harga_modal)) as subtotal_keuntungan
        ')->first();

        // Widget transaksi jasa (total pendapatan, total pengeluaran, subtotal keuntungan dari TransaksiJasaDetail)
        $transaksiJasaQuery = TransaksiJasaDetail::whereHas('transaksiJasa', function ($q) use ($dateCondition) {
            $q->withoutTrashed();

            if ($dateCondition) {
                if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                    $q->whereMonth('tanggal_transaksi', $dateCondition['month'])
                        ->whereYear('tanggal_transaksi', $dateCondition['year']);
                } elseif (isset($dateCondition['year'])) {
                    $q->whereYear('tanggal_transaksi', $dateCondition['year']);
                }
            }
        });
        $transaksiJasaData = $transaksiJasaQuery->selectRaw('
            SUM(qty * harga_jasa) as total_pendapatan,
            SUM(pengeluaran_jasa) as total_pengeluaran,
            SUM((qty * harga_jasa) - pengeluaran_jasa) as subtotal_keuntungan
        ')->first();

        // Widget piutang jasa (seluruh sisa piutang)
        $piutangJasaQuery = PiutangJasa::query();
        if ($dateCondition) {
            if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                $piutangJasaQuery->whereMonth('created_at', $dateCondition['month'])
                    ->whereYear('created_at', $dateCondition['year']);
            } elseif (isset($dateCondition['year'])) {
                $piutangJasaQuery->whereYear('created_at', $dateCondition['year']);
            }
        }
        $totalPiutangJasa = $piutangJasaQuery->sum('sisa_piutang');

        // Widget pengeluaran kantor (total pengeluaran)
        $pengeluaranKantorQuery = PengeluaranKantor::query();
        if ($dateCondition) {
            if (isset($dateCondition['month']) && isset($dateCondition['year'])) {
                $pengeluaranKantorQuery->whereMonth('tanggal', $dateCondition['month'])
                    ->whereYear('tanggal', $dateCondition['year']);
            } elseif (isset($dateCondition['year'])) {
                $pengeluaranKantorQuery->whereYear('tanggal', $dateCondition['year']);
            }
        }
        $totalPengeluaranKantor = $pengeluaranKantorQuery->sum('pengeluaran');

        $currentYear = $dateCondition['year'] ?? now()->year;

        // Chart data for Penjualan Produk
        $transaksiProdukChartData = TransaksiProdukDetail::query()
            ->whereHas('transaksiProduk', function ($q) use ($currentYear) {
                $q->whereYear('tanggal_transaksi', $currentYear);
            })
            ->selectRaw('MONTH(transaksi_produk.tanggal_transaksi) as month, SUM(transaksi_produk_detail.jumlah_keluar * transaksi_produk_detail.harga_jual) as monthly_sales')
            ->join('transaksi_produk', 'transaksi_produk_detail.transaksi_produk_id', '=', 'transaksi_produk.id')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('monthly_sales', 'month')
            ->toArray();

        $transaksiProdukChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $transaksiProdukChart[] = $transaksiProdukChartData[$i] ?? 0;
        }

        // Chart data for Penjualan Sparepart
        $sparepartKeluarChartData = SparepartKeluarDetail::query()
            ->whereHas('sparepartKeluar', function ($q) use ($currentYear) {
                $q->whereYear('created_at', $currentYear);
            })
            ->selectRaw('MONTH(sparepart_keluar.created_at) as month, SUM(sparepart_keluar_detail.jumlah_keluar * sparepart_keluar_detail.harga_jual) as monthly_sales')
            ->join('sparepart_keluar', 'sparepart_keluar_detail.sparepart_keluar_id', '=', 'sparepart_keluar.id')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('monthly_sales', 'month')
            ->toArray();

        $sparepartKeluarChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $sparepartKeluarChart[] = $sparepartKeluarChartData[$i] ?? 0;
        }

        // Chart data for Pendapatan Jasa
        $transaksiJasaChartData = TransaksiJasaDetail::query()
            ->whereHas('transaksiJasa', function ($q) use ($currentYear) {
                $q->whereYear('tanggal_transaksi', $currentYear);
            })
            ->selectRaw('MONTH(transaksi_jasa.tanggal_transaksi) as month, SUM(transaksi_jasa_detail.qty * transaksi_jasa_detail.harga_jasa) as monthly_revenue')
            ->join('transaksi_jasa', 'transaksi_jasa_detail.transaksi_jasa_id', '=', 'transaksi_jasa.id')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('monthly_revenue', 'month')
            ->toArray();

        $transaksiJasaChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $transaksiJasaChart[] = $transaksiJasaChartData[$i] ?? 0;
        }

        return [
            // KELOMPOK PENDAPATAN
            Section::make('Pendapatan')
                ->description('Pendapatan dari berbagai transaksi')
                ->columns(3)
                ->schema([
                    // Subtotal Penjualan Transaksi Produk
                    Stat::make('Penjualan Produk', 'Rp '.number_format($transaksiProdukData->subtotal_penjualan ?? 0, 0, ',', '.'))
                        ->description('Pendapatan Produk')
                        ->descriptionIcon('heroicon-m-shopping-cart')
                        ->chart($transaksiProdukChart),
                    // Subtotal Penjualan Sparepart Keluar
                    Stat::make('Penjualan Sparepart', 'Rp '.number_format($sparepartKeluarData->subtotal_penjualan ?? 0, 0, ',', '.'))
                        ->description('Pendapatan Sparepart')
                        ->descriptionIcon('heroicon-m-shopping-cart')
                        ->chart($sparepartKeluarChart),

                    // Total Pendapatan Transaksi Jasa
                    Stat::make('Pendapatan Jasa', 'Rp '.number_format($transaksiJasaData->total_pendapatan ?? 0, 0, ',', '.'))
                        ->description('Pendapatan Jasa')
                        ->descriptionIcon('heroicon-m-currency-dollar')
                        ->chart($transaksiJasaChart),

                    // KELOMPOK MODAL
                    Section::make('Modal')
                        ->description('Modal dari berbagai transaksi')
                        ->columns(3)
                        ->schema([
                            // Subtotal Modal Transaksi Produk
                            Stat::make('Modal Produk', 'Rp '.number_format($transaksiProdukData->subtotal_modal ?? 0, 0, ',', '.'))
                                ->description('Biaya Produk')
                                ->descriptionIcon('heroicon-m-currency-dollar'),
                            // Subtotal Modal Sparepart Keluar
                            Stat::make('Modal Sparepart', 'Rp '.number_format($sparepartKeluarData->subtotal_modal ?? 0, 0, ',', '.'))
                                ->description('Biaya Sparepart')
                                ->descriptionIcon('heroicon-m-currency-dollar'),
                            // Total Pengeluaran Transaksi Jasa
                            Stat::make('Pengeluaran Jasa', 'Rp '.number_format($transaksiJasaData->total_pengeluaran ?? 0, 0, ',', '.'))
                                ->description('Biaya Jasa')
                                ->descriptionIcon('heroicon-m-currency-bangladeshi'),
                        ])
                        ->columnSpanFull(),

                    // KELOMPOK KEUNTUNGAN
                    Section::make('Keuntungan')
                        ->description('Keuntungan dari berbagai transaksi')
                        ->columns(3)
                        ->schema([
                            // Subtotal Keuntungan Transaksi Produk
                            Stat::make('Keuntungan Produk', 'Rp '.number_format($transaksiProdukData->subtotal_keuntungan ?? 0, 0, ',', '.'))
                                ->description('Laba Produk')
                                ->descriptionIcon('heroicon-m-arrow-trending-up'),
                            // Subtotal Keuntungan Sparepart Keluar
                            Stat::make('Keuntungan Sparepart', 'Rp '.number_format($sparepartKeluarData->subtotal_keuntungan ?? 0, 0, ',', '.'))
                                ->description('Laba Sparepart')
                                ->descriptionIcon('heroicon-m-arrow-trending-up'),
                            // Subtotal Keuntungan Transaksi Jasa
                            Stat::make('Keuntungan Jasa', 'Rp '.number_format($transaksiJasaData->subtotal_keuntungan ?? 0, 0, ',', '.'))
                                ->description('Laba Jasa')
                                ->descriptionIcon('heroicon-m-arrow-trending-up'),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            // KELOMPOK BIAYA & PENGELUARAN
            Section::make('Biaya & Pengeluaran')
                ->description('Biaya dan pengeluaran dari berbagai transaksi')
                ->columns(2)
                ->schema([
                    // Total Gaji Karyawan
                    Stat::make('Total Gaji Karyawan', 'Rp '.number_format($totalGajiKaryawan, 0, ',', '.'))
                        ->description('Karyawan Aktif: '.Karyawan::where('status_aktif', true)->count())
                        ->descriptionIcon('heroicon-m-user'),
                    // Pengeluaran Kantor
                    Stat::make('Pengeluaran Kantor', 'Rp '.number_format($totalPengeluaranKantor, 0, ',', '.'))
                        ->description('Biaya Operasional')
                        ->descriptionIcon('heroicon-m-building-office'),
                ])
                ->columnSpanFull(),

            // KELOMPOK STOK & TRANSAKSI
            Section::make('Stok & Transaksi')
                ->description('Stok dan transaksi dari berbagai transaksi')
                ->columns(2)
                ->schema([
                    // Barang Masuk
                    Stat::make('Total Barang Masuk', $totalBarangMasuk)
                        ->description('Unit AC')
                        ->descriptionIcon('heroicon-m-arrow-down-tray'),
                    // Sparepart Masuk
                    Stat::make('Total Sparepart Masuk', $totalSparepartMasuk)
                        ->description('Sparepart')
                        ->descriptionIcon('heroicon-m-arrow-down-tray'),
                ])
                ->columnSpanFull(),

            // KELOMPOK PIUTANG & HUTANG
            Section::make('Piutang & Hutang')
                ->description('Piutang dan hutang dari berbagai transaksi')
                ->columns(3)
                ->schema([
                    // Hutang Produk
                    Stat::make('Total Hutang Produk', 'Rp '.number_format($totalHutangProduk, 0, ',', '.'))
                        ->description('Kewajiban Produk')
                        ->descriptionIcon('heroicon-m-banknotes'),

                    // Piutang Produk
                    Stat::make('Total Piutang Produk', 'Rp '.number_format($totalPiutangProduk, 0, ',', '.'))
                        ->description('Hutang Produk')
                        ->descriptionIcon('heroicon-m-banknotes'),

                    // Piutang Jasa
                    Stat::make('Total Piutang Jasa', 'Rp '.number_format($totalPiutangJasa, 0, ',', '.'))
                        ->description('Hutang Jasa')
                        ->descriptionIcon('heroicon-m-banknotes'),
                ])
                ->columnSpanFull(),
        ];
    }
}
