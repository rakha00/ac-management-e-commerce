<?php

namespace Database\Seeders;

use App\Models\MerkSparepart;
use Illuminate\Database\Seeder;

class MerkSparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MerkSparepart::insert([
            ['merk_spareparts' => 'Daikin'],
            ['merk_spareparts' => 'Copeland'],
            ['merk_spareparts' => 'Danfoss'],
            ['merk_spareparts' => 'Emerson'],
            ['merk_spareparts' => 'Honeywell'],
            ['merk_spareparts' => 'Johnson Controls'],
            ['merk_spareparts' => 'Carrier'],
            ['merk_spareparts' => 'Trane'],
            ['merk_spareparts' => 'York'],
            ['merk_spareparts' => 'Rheem'],
            ['merk_spareparts' => 'Goodman'],
        ]);
    }
}
