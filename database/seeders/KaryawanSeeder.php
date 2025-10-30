<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed example users and their Karyawan profiles for each role.
        $roles = ['admin', 'gudang', 'helper', 'teknisi', 'staff', 'sales'];

        foreach ($roles as $role) {
            $user = User::query()->create([
                'name' => $role,
                'email' => $role.'@gmail.com',
                'password' => bcrypt($role),
            ]);

            Karyawan::query()->create([
                'user_id' => $user->id,
                'nama' => ucfirst($role).' User',
                'jabatan' => $role,
                'nomor_hp' => '08'.rand(100000000, 999999999),
                'gaji_pokok' => 5000000,
                'alamat' => 'Alamat '.ucfirst($role),
                'path_foto_ktp' => 'N/A',
                'path_dokumen_tambahan' => 'N/A',
                'kontak_darurat_serumah' => '081234567890',
                'kontak_darurat_tidak_serumah' => '081234567891',
                'status_aktif' => true,
                'tanggal_terakhir_aktif' => null,
            ]);
        }
    }
}
