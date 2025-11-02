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
            KaryawanSeeder::class,
            TeknisiSeeder::class,
            AbsensiSeeder::class,
            UnitACSeeder::class,
            KonsumenSeeder::class,
            SparepartSeeder::class,
            DistributorSeeder::class,
            PrincipalSeeder::class,
            BarangMasukSeeder::class,
            BarangMasukDetailSeeder::class,

            TransaksiJasaSeeder::class,
            TransaksiProdukSeeder::class,
            SparepartMasukSeeder::class,
        ]);
    }
}
