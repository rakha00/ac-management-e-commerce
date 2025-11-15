<?php

namespace Database\Seeders;

use App\Models\DistributorSparepart;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DistributorSparepart::create([
            'nama_distributor' => 'Distributor A',
            'alamat' => 'Jl. Contoh No. 1',
            'kontak' => '081234567890',
        ]);

        DistributorSparepart::create([
            'nama_distributor' => 'Distributor B',
            'alamat' => 'Jl. Contoh No. 2',
            'kontak' => '089876543210',
        ]);
    }
}
