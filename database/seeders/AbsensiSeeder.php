<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $karyawans = Karyawan::limit(2)->get();

        if ($karyawans->count() < 2) {
            $this->command->info('Not enough karyawan found, skipping AbsensiSeeder');

            return;
        }

        // Sample 1: On-time attendance
        Absensi::create([
            'karyawan_id' => $karyawans[0]->id,
            'waktu_absen' => now()->subDays(2)->setTime(8, 0, 0),
            'is_telat' => false,
            'keterangan' => 'Hadir tepat waktu',
            'is_terkonfirmasi' => true,
            'dikonfirmasi_oleh_id' => $user->id,
            'dikonfirmasi_pada' => now()->subDays(2)->setTime(8, 15, 0),
        ]);

        // Sample 2: Late attendance
        Absensi::create([
            'karyawan_id' => $karyawans[1]->id,
            'waktu_absen' => now()->subDays(1)->setTime(9, 30, 0),
            'is_telat' => true,
            'keterangan' => 'Terlambat karena kendala transportasi',
            'is_terkonfirmasi' => true,
            'dikonfirmasi_oleh_id' => $user->id,
            'dikonfirmasi_pada' => now()->subDays(1)->setTime(9, 45, 0),
        ]);

        // Sample 3: Unconfirmed attendance
        Absensi::create([
            'karyawan_id' => $karyawans[0]->id,
            'waktu_absen' => now(),
            'is_telat' => false,
            'keterangan' => 'Hadir',
            'is_terkonfirmasi' => false,
            'dikonfirmasi_oleh_id' => null,
            'dikonfirmasi_pada' => null,
        ]);
    }
}
