<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Principal;
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

        for ($i = 0; $i < 3; $i++) {
            $tanggal = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
            BarangMasuk::create([
                'principal_id' => $principals->random()->id,
                'tanggal' => $tanggal,
                'nomor_barang_masuk' => BarangMasuk::generateNomorBarangMasuk($tanggal),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
