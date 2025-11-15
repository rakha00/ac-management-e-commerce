<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Konsumen;
use App\Models\TransaksiProduk;
use App\Models\TransaksiProdukDetail;
use App\Models\UnitAC;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransaksiProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $salesKaryawans = Karyawan::where('jabatan', 'sales')->pluck('id');
        $konsumens = Konsumen::pluck('id');
        $unitAcs = UnitAC::all();

        for ($i = 0; $i < 3; $i++) {
            $tanggalTransaksi = Carbon::now()->subDays(rand(1, 365));
            $salesKaryawanId = $salesKaryawans->random();
            $konsumenId = $konsumens->random();

            $transaksiProduk = TransaksiProduk::create([
                'tanggal_transaksi' => $tanggalTransaksi,
                'sales_karyawan_id' => $salesKaryawanId,
                'konsumen_id' => $konsumenId,
                'nomor_invoice' => TransaksiProduk::generateNomorInvoice($tanggalTransaksi->toDateString()),
                'nomor_surat_jalan' => TransaksiProduk::generateNomorSuratJalan($tanggalTransaksi->toDateString()),
                'keterangan' => 'Transaksi produk '.($i + 1),
            ]);

            $totalTransaksi = 0;
            $jumlahDetail = 11;

            for ($j = 0; $j < $jumlahDetail; $j++) {
                $unitAc = $unitAcs->random();
                $jumlahKeluar = rand(1, 5);

                $hargaJual = $unitAc->harga_jual > 0 ? $unitAc->harga_jual : rand(2000000, 5000000);
                $hargaModal = rand(1000000, 2000000); // Menambahkan harga_modal secara langsung

                TransaksiProdukDetail::create([
                    'transaksi_produk_id' => $transaksiProduk->id,
                    'unit_ac_id' => $unitAc->id,
                    'jumlah_keluar' => $jumlahKeluar,
                    'harga_modal' => $hargaModal,
                    'harga_jual' => $hargaJual,
                    'keterangan' => 'Detail produk '.($j + 1),
                ]);

                $totalTransaksi += ($hargaJual * $jumlahKeluar);
            }

        }

    }
}
