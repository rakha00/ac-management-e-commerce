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
            KonsumenSeeder::class,
            DistributorSeeder::class,

            BarangMasukSeeder::class,
            HutangProdukSeeder::class,
            TransaksiProdukSeeder::class,
            PiutangProdukSeeder::class,

            SparepartSeeder::class,
            // AbsensiSeeder::class,

            // TransaksiJasaSeeder::class,
            // SparepartMasukSeeder::class,
            // SparepartKeluarSeeder::class,
        ]);
    }
}
