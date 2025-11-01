<?php

namespace Database\Seeders;

use App\Models\Konsumen;
use Illuminate\Database\Seeder;

class KonsumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Konsumen::create([
            'nama' => 'Budi Santoso',
            'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            'telepon' => '081234567890',
        ]);

        Konsumen::create([
            'nama' => 'Siti Aminah',
            'alamat' => 'Jl. Sudirman No. 25, Bandung',
            'telepon' => '087654321098',
        ]);

        Konsumen::create([
            'nama' => 'Joko Susilo',
            'alamat' => 'Jl. Gatot Subroto No. 5, Surabaya',
            'telepon' => '085098765432',
        ]);
    }
}
