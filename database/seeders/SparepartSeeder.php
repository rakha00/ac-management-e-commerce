<?php

namespace Database\Seeders;

use App\Models\Sparepart;
use Illuminate\Database\Seeder;

class SparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sparepart::create([
            'kode_sparepart' => 'SP001',
            'nama_sparepart' => 'Filter AC Mobil',
            'harga_modal' => 50000,
            'stok_awal' => 100,
            'stok_akhir' => 100,
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'keterangan' => 'Filter AC untuk berbagai jenis mobil',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Sparepart::create([
            'kode_sparepart' => 'SP002',
            'nama_sparepart' => 'Freon R134a',
            'harga_modal' => 150000,
            'stok_awal' => 50,
            'stok_akhir' => 50,
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'keterangan' => 'Freon R134a untuk pengisian AC mobil',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
