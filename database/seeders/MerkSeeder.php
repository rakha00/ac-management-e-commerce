<?php

namespace Database\Seeders;

use App\Models\Merk;
use Illuminate\Database\Seeder;

class MerkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Merk::insert([
            ['merk' => 'Daikin'],
            ['merk' => 'Panasonic'],
            ['merk' => 'Sharp'],
            ['merk' => 'LG'],
            ['merk' => 'Samsung'],
            ['merk' => 'Toshiba'],
            ['merk' => 'Midea'],
            ['merk' => 'Haier'],
            ['merk' => 'Gree'],
            ['merk' => 'Carrier'],
        ]);
    }
}
