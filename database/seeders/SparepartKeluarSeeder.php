<?php

namespace Database\Seeders;

use App\Models\Konsumen;
use App\Models\Sparepart;
use App\Models\SparepartKeluar;
use App\Models\SparepartKeluarDetail;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SparepartKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::all();

        $spareparts = Sparepart::all();

        $konsumen = Konsumen::all();

        for ($i = 0; $i < 5; $i++) {
            $sparepartKeluar = SparepartKeluar::create([
                'tanggal_keluar' => $faker->date(),
                'nomor_invoice' => 'INVSP-'.date('Ymd').'-'.str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'konsumen_id' => $faker->randomElement($konsumen->pluck('id')->toArray()),
                'keterangan' => $faker->sentence(),
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
            ]);

            $totalModal = 0;
            $totalPenjualan = 0;

            for ($j = 0; $j < rand(2, 5); $j++) {
                $sparepart = $spareparts->random();

                $jumlahKeluar = $faker->numberBetween(1, 10);
                $hargaModal = $sparepart->harga_modal; // Use harga_modal from Sparepart model
                // Calculate harga_jual based on harga_modal with a random margin
                $hargaJual = $hargaModal + $faker->numberBetween(10000, 50000);

                SparepartKeluarDetail::create([
                    'sparepart_keluar_id' => $sparepartKeluar->id,
                    'sparepart_id' => $sparepart->id,
                    'kode_sparepart' => $sparepart->kode_sparepart,
                    'nama_sparepart' => $sparepart->nama_sparepart,
                    'jumlah_keluar' => $jumlahKeluar,
                    'harga_modal' => $hargaModal,
                    'harga_jual' => $hargaJual,
                    'keterangan' => $faker->sentence(),
                    'created_by' => $users->random()->id,
                    'updated_by' => $users->random()->id,
                ]);

                $totalModal += ($hargaModal * $jumlahKeluar);
                $totalPenjualan += ($hargaJual * $jumlahKeluar);
            }

            $sparepartKeluar->update([
                'total_modal' => $totalModal,
                'total_penjualan' => $totalPenjualan,
                'total_keuntungan' => $totalPenjualan - $totalModal,
            ]);
        }
    }
}
