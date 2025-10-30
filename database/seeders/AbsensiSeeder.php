<?php

namespace Database\Seeders;

use App\Models\Absensi;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Absensi::create([
            'karyawan_id' => 1, // Assuming a Karyawan with ID 1 exists
            'waktu_absen' => now()->subDays(2)->setTime(8, 0, 0),
            'is_telat' => false,
            'keterangan' => 'Hadir tepat waktu',
            'is_terkonfirmasi' => true,
            'dikonfirmasi_oleh_id' => 1, // Assuming a User with ID 1 exists
            'dikonfirmasi_pada' => now()->subDays(2)->setTime(9, 0, 0),
        ]);

        Absensi::create([
            'karyawan_id' => 2, // Assuming a Karyawan with ID 2 exists
            'waktu_absen' => now()->subDays(1)->setTime(9, 15, 0),
            'is_telat' => true,
            'keterangan' => 'Terlambat karena macet',
            'is_terkonfirmasi' => true,
            'dikonfirmasi_oleh_id' => 1, // Assuming a User with ID 1 exists
            'dikonfirmasi_pada' => now()->subDays(1)->setTime(9, 30, 0),
        ]);

        Absensi::create([
            'karyawan_id' => 1, // Assuming a Karyawan with ID 1 exists
            'waktu_absen' => now()->setTime(8, 30, 0),
            'is_telat' => false,
            'keterangan' => 'Hadir',
            'is_terkonfirmasi' => false,
            'dikonfirmasi_oleh_id' => null,
            'dikonfirmasi_pada' => null,
        ]);
    }
}
