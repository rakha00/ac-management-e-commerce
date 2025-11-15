<?php

namespace Database\Seeders;

use App\Models\PiutangJasa;
use App\Models\PiutangJasaCicilanDetail;
use App\Models\TransaksiJasa;
use App\Models\User;
use Illuminate\Database\Seeder;

class PiutangJasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $transaksiJasa = TransaksiJasa::limit(3)->get();

        if ($transaksiJasa->count() === 0) {
            $this->command->info('No TransaksiJasa records found, skipping PiutangJasaSeeder');

            return;
        }

        // Piutang 1: Belum Lunas
        $tj1 = $transaksiJasa->first();
        $total1 = $tj1->detailTransaksiJasa->sum('subtotal_pendapatan');

        $piutang1 = PiutangJasa::create([
            'transaksi_jasa_id' => $tj1->id,
            'total_piutang' => $total1,
            'sisa_piutang' => $total1,
            'status_pembayaran' => 'belum lunas',
            'jatuh_tempo' => now()->addDays(30),
            'keterangan' => 'Belum Lunas',
            'created_by' => $user->id,
        ]);

        // Piutang 2: Tercicil
        $tj2 = $transaksiJasa->count() > 1 ? $transaksiJasa[1] : $tj1;
        $total2 = $tj2->detailTransaksiJasa->sum('subtotal_pendapatan');
        $cicilan2 = $total2 * 0.6;

        $piutang2 = PiutangJasa::create([
            'transaksi_jasa_id' => $tj2->id,
            'total_piutang' => $total2,
            'sisa_piutang' => $total2 - $cicilan2,
            'status_pembayaran' => 'tercicil',
            'jatuh_tempo' => now()->addDays(15),
            'keterangan' => 'Tercicil',
            'created_by' => $user->id,
        ]);

        // Piutang 3: Sudah Lunas
        $tj3 = $transaksiJasa->count() > 2 ? $transaksiJasa[2] : $tj1;
        $total3 = $tj3->detailTransaksiJasa->sum('subtotal_pendapatan');

        $piutang3 = PiutangJasa::create([
            'transaksi_jasa_id' => $tj3->id,
            'total_piutang' => $total3,
            'sisa_piutang' => 0,
            'status_pembayaran' => 'sudah lunas',
            'jatuh_tempo' => now()->subDays(7),
            'keterangan' => 'Sudah Lunas',
            'created_by' => $user->id,
        ]);

        // Add cicilan details
        PiutangJasaCicilanDetail::create([
            'piutang_jasa_id' => $piutang2->id,
            'nominal_cicilan' => $cicilan2,
            'tanggal_cicilan' => now()->subDays(5),
            'created_by' => $user->id,
        ]);

        PiutangJasaCicilanDetail::create([
            'piutang_jasa_id' => $piutang3->id,
            'nominal_cicilan' => $total3,
            'tanggal_cicilan' => now()->subDays(3),
            'created_by' => $user->id,
        ]);
    }
}
