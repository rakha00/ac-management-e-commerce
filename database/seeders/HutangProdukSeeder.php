<?php

namespace Database\Seeders;

use App\Models\HutangProduk;
use Illuminate\Database\Seeder;

class HutangProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HutangProduk::create([
            'barang_masuk_id' => 1, // Assuming barang_masuk with id 1 exists
            'total_hutang' => 1000000,
            'sisa_hutang' => 500000,
            'status_pembayaran' => 'belum lunas',
            'jatuh_tempo' => '2025-12-31',
            'keterangan' => 'Data ini dari Seeder',
            'created_by' => 1, // Assuming user with id 1 exists
        ]);

        HutangProduk::create([
            'barang_masuk_id' => 2, // Assuming barang_masuk with id 2 exists
            'total_hutang' => 2500000,
            'sisa_hutang' => 2500000,
            'status_pembayaran' => 'belum lunas',
            'jatuh_tempo' => '2026-01-15',
            'keterangan' => 'Data ini dari Seeder',
            'created_by' => 1, // Assuming user with id 1 exists
        ]);
    }
}
