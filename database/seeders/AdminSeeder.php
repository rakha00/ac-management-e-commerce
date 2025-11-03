<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => bcrypt('admin'),
            ]
        );

        Karyawan::firstOrCreate([
            'user_id' => $user->id,
            'nama' => 'Admin User',
            'jabatan' => 'admin',
            'nomor_hp' => '08'.rand(100000000, 999999999),
            'gaji_pokok' => 0,
            'alamat' => 'Alamat Admin',
            'path_foto_ktp' => 'N/A',
            'path_dokumen_tambahan' => 'N/A',
            'kontak_darurat_serumah' => '081234567890',
            'kontak_darurat_tidak_serumah' => '081234567891',
            'status_aktif' => true,
            'tanggal_terakhir_aktif' => null,
        ]);
    }
}
