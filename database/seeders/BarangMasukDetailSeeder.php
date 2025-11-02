<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\UnitAC;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

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
        $users = User::all();

        foreach ($barangMasuks as $barangMasuk) {
            for ($i = 0; $i < $faker->numberBetween(1, 5); $i++) {
                $unitAC = $unitACs->random();
                $jumlah = $faker->numberBetween(1, 10);
                BarangMasukDetail::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'unit_ac_id' => $unitAC->id,
                    'sku' => $unitAC->sku,
                    'nama_unit' => $unitAC->nama_unit,
                    'jumlah_barang_masuk' => $jumlah,
                    'keterangan' => $faker->sentence(),
                    'created_by' => $users->random()->id,
                    'updated_by' => $users->random()->id,
                ]);
            }
        }
    }
}