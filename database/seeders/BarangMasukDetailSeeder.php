<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\UnitAC;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BarangMasukDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $barangMasuks = BarangMasuk::all();
        $unitACs = UnitAC::all();

        foreach ($barangMasuks as $barangMasuk) {
            for ($i = 0; $i < $faker->numberBetween(1, 5); $i++) {
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
