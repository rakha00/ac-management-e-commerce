<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            KaryawanSeeder::class,
            TeknisiSeeder::class,

            MerkSeeder::class,
            TipeACSeeder::class,
            UnitACSeeder::class,

            PrincipalSeeder::class,
            BarangMasukSeeder::class,

            // KonsumenSeeder::class,
            // SparepartSeeder::class,
            // DistributorSeeder::class,
            // BarangMasukDetailSeeder::class,
            // AbsensiSeeder::class,

            // TransaksiJasaSeeder::class,
            // TransaksiProdukSeeder::class,
            // SparepartMasukSeeder::class,
            // SparepartKeluarSeeder::class,
        ]);
    }
}
