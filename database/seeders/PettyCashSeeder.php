<?php

namespace Database\Seeders;

use App\Models\PettyCash;
use App\Models\User;
use Illuminate\Database\Seeder;

class PettyCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // Sample 1: Expense transaction
        PettyCash::create([
            'tanggal' => now()->subDays(5),
            'pengeluaran' => 150000,
            'keterangan_pengeluaran' => 'Pembelian perlengkapan kantor',
            'pemasukan' => null,
            'keterangan_pemasukan' => null,
            'path_bukti_pembayaran' => 'bukti_pembayaran/pembelian_perlengkapan.pdf',
            'created_by' => $user->id,
        ]);

        // Sample 2: Income transaction
        PettyCash::create([
            'tanggal' => now()->subDays(3),
            'pengeluaran' => null,
            'keterangan_pengeluaran' => null,
            'pemasukan' => 500000,
            'keterangan_pemasukan' => 'Pengembalian uang petty cash',
            'path_bukti_pembayaran' => 'bukti_pembayaran/pengembalian_uang.pdf',
            'created_by' => $user->id,
        ]);

        // Sample 3: Another expense transaction
        PettyCash::create([
            'tanggal' => now()->subDays(1),
            'pengeluaran' => 75000,
            'keterangan_pengeluaran' => 'Biaya transportasi kantor',
            'pemasukan' => null,
            'keterangan_pemasukan' => null,
            'path_bukti_pembayaran' => 'bukti_pembayaran/biaya_transportasi.pdf',
            'created_by' => $user->id,
        ]);
    }
}
