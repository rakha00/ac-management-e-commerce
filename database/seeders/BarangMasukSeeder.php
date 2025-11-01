<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Principal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $principals = Principal::all();
        $users = User::all();

        if ($principals->isEmpty()) {
            $this->call(PrincipalSeeder::class);
            $principals = Principal::all();
        }

        if ($principals->isEmpty() || $users->isEmpty()) {
            $this->command->info('Please seed Principals and Users first!');
            return;
        }

        for ($i = 0; $i < 3; $i++) {
            $tanggal = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
            BarangMasuk::create([
                'principal_id' => $principals->random()->id,
                'tanggal' => $tanggal,
                'nomor_barang_masuk' => BarangMasuk::generateNomorBarangMasuk($tanggal),
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
            ]);
        }
    }
}