<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Principal;
use App\Models\UnitAC;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $principals = Principal::all();
        $unitACs = UnitAC::all();

        for ($i = 0; $i < 3; $i++) {
            $tanggal = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
            $barangMasuk = BarangMasuk::create([
                'principal_id' => $principals->random()->id,
                'tanggal' => $tanggal,
                'nomor_barang_masuk' => BarangMasuk::generateNomorBarangMasuk($tanggal),
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $unitAC = $unitACs->random();
                BarangMasukDetail::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'unit_ac_id' => $unitAC->id,
                    'sku' => $unitAC->sku,
                    'nama_unit' => $unitAC->nama_unit,
                    'jumlah_barang_masuk' => $faker->numberBetween(1, 10),
                    'keterangan' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        }
    }
}
