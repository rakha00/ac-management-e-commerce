<?php

namespace Database\Seeders;

use App\Models\TipeAC;
use Illuminate\Database\Seeder;

class TipeACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipeAC::insert([
            ['tipe_ac' => 'Split AC'],
            ['tipe_ac' => 'Single AC'],
            ['tipe_ac' => 'Cassette AC'],
            ['tipe_ac' => 'Portable AC'],
            ['tipe_ac' => 'Window AC'],
            ['tipe_ac' => 'Central AC'],
            ['tipe_ac' => 'Floor Standing AC'],
            ['tipe_ac' => 'Inverter AC'],
        ]);
    }
}
