<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $teknisi = Karyawan::where('jabatan', 'teknisi')->first();

        if (! $teknisi) {
            $this->command->info('No teknisi found, skipping LaporanSeeder');

            return;
        }

        // Sample 1: Routine maintenance report
        Laporan::create([
            'karyawan_id' => $teknisi->id,
            'tanggal' => now()->subDays(3)->setTime(9, 0),
            'nama_konsumen' => 'PT. ABC Manufacturing',
            'jenis_pengerjaan' => 'Perawatan Rutin',
            'path_foto_pengerjaan' => 'laporan/foto_perawatan_1.jpg',
            'keterangan' => 'Melakukan perawatan rutin AC ruang meeting',
            'created_by' => $user->id,
        ]);

        // Sample 2: Repair report
        Laporan::create([
            'karyawan_id' => $teknisi->id,
            'tanggal' => now()->subDays(2)->setTime(14, 30),
            'nama_konsumen' => 'Toko Serba Ada',
            'jenis_pengerjaan' => 'Perbaikan',
            'path_foto_pengerjaan' => 'laporan/foto_perbaikan_1.jpg',
            'keterangan' => 'Memperbaiki AC yang tidak dingin, ditemukan kebocoran pada pipa',
            'created_by' => $user->id,
        ]);

        // Sample 3: Installation report
        Laporan::create([
            'karyawan_id' => $teknisi->id,
            'tanggal' => now()->subDays(1)->setTime(10, 15),
            'nama_konsumen' => 'Rumah Bapak Joko',
            'jenis_pengerjaan' => 'Instalasi',
            'path_foto_pengerjaan' => 'laporan/foto_instalasi_1.jpg',
            'keterangan' => 'Instalasi AC baru di kamar utama',
            'created_by' => $user->id,
        ]);
    }
}
