<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Konsumen;
use App\Models\TransaksiJasa;
use App\Models\TransaksiJasaDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransaksiJasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::first();
        $teknisiKaryawan = Karyawan::where('jabatan', 'teknisi')->get();
        $helperKaryawan = Karyawan::where('jabatan', 'helper')->get();
        $konsumen = Konsumen::all();

        for ($i = 0; $i < 3; $i++) {
            $tanggalTransaksi = Carbon::now()->subDays(rand(1, 365));
            $teknisi = $teknisiKaryawan->random();
            $helper = $helperKaryawan->random();
            $randomKonsumen = $konsumen->random();

            $kodeJasa = TransaksiJasa::generateSequentialNumber($tanggalTransaksi->toDateString(), 'kode_jasa', 'KJ');
            $nomorInvoiceJasa = TransaksiJasa::generateSequentialNumber($tanggalTransaksi->toDateString(), 'nomor_invoice_jasa', 'INV-TJ');
            $nomorSuratJalanJasa = TransaksiJasa::generateSequentialNumber($tanggalTransaksi->toDateString(), 'nomor_surat_jalan_jasa', 'SJ-TJ');

            $transaksiJasa = TransaksiJasa::create([
                'tanggal_transaksi' => $tanggalTransaksi,
                'kode_jasa' => $kodeJasa,
                'nomor_invoice_jasa' => $nomorInvoiceJasa,
                'nomor_surat_jalan_jasa' => $nomorSuratJalanJasa,
                'teknisi_karyawan_id' => $teknisi->id,
                'helper_karyawan_id' => $helper->id,
                'konsumen_id' => $randomKonsumen->id,
                'garansi_hari' => rand(30, 365),
                'keterangan' => fake()->sentence,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $qty = rand(1, 5);
                $hargaJasa = rand(50000, 500000);
                $pengeluaranJasa = rand(10000, 100000);

                TransaksiJasaDetail::create([
                    'transaksi_jasa_id' => $transaksiJasa->id,
                    'jenis_jasa' => fake()->word,
                    'qty' => $qty,
                    'harga_jasa' => $hargaJasa,
                    'keterangan_jasa' => fake()->sentence,
                    'pengeluaran_jasa' => $pengeluaranJasa,
                    'keterangan_pengeluaran' => fake()->sentence,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }
}
