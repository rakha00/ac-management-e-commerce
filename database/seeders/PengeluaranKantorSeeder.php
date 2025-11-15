<?php

namespace Database\Seeders;

use App\Models\PengeluaranKantor;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengeluaranKantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // Sample 1: Office supplies expense
        PengeluaranKantor::create([
            'tanggal' => now()->subDays(7),
            'pengeluaran' => 350000,
            'keterangan_pengeluaran' => 'Pembelian perlengkapan kantor',
            'path_bukti_pembayaran' => 'bukti_pembayaran/perlengkapan_kantor.pdf',
            'created_by' => $user->id,
        ]);

        // Sample 2: Office maintenance expense
        PengeluaranKantor::create([
            'tanggal' => now()->subDays(4),
            'pengeluaran' => 250000,
            'keterangan_pengeluaran' => 'Biaya perawatan AC kantor',
            'path_bukti_pembayaran' => 'bukti_pembayaran/perawatan_ac.pdf',
            'created_by' => $user->id,
        ]);

        // Sample 3: Office utility expense
        PengeluaranKantor::create([
            'tanggal' => now()->subDays(1),
            'pengeluaran' => 450000,
            'keterangan_pengeluaran' => 'Biaya listrik dan air kantor',
            'path_bukti_pembayaran' => 'bukti_pembayaran/listrik_air.pdf',
            'created_by' => $user->id,
        ]);
    }
}
