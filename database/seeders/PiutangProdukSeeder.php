<?php

namespace Database\Seeders;

use App\Models\PiutangProduk;
use Illuminate\Database\Seeder;

class PiutangProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PiutangProduk::create([
            'transaksi_produk_id' => 1, // Assuming transaksi_produk with id 1 exists
            'total_piutang' => 1500000,
            'sisa_piutang' => 750000,
            'status_pembayaran' => 'belum lunas',
            'jatuh_tempo' => '2025-11-30',
            'keterangan' => 'Data ini dari Seeder',
            'created_by' => 1, // Assuming user with id 1 exists
        ]);

        PiutangProduk::create([
            'transaksi_produk_id' => 2, // Assuming transaksi_produk with id 2 exists
            'total_piutang' => 3000000,
            'sisa_piutang' => 3000000,
            'status_pembayaran' => 'belum lunas',
            'jatuh_tempo' => '2026-02-28',
            'keterangan' => 'Data ini dari Seeder',
            'created_by' => 1, // Assuming user with id 1 exists
        ]);
    }
}
