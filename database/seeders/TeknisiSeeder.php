<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeknisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * email: teknisi1@gmail.com
         * password: teknisi
         *
         * email: teknisi2@gmail.com
         * password:teknisi
         */
        foreach ([1, 2] as $i) {
            $name = "Teknisi {$i}";
            $email = "teknisi{$i}@gmail.com";

            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('teknisi'),
                ]
            );

            Karyawan::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $name,
                    'jabatan' => 'teknisi',
                    'no_hp' => '08'.random_int(100000000, 999999999),
                    'gaji_pokok' => 5000000,
                    'alamat' => 'Alamat Teknisi '.$i,
                    'foto_ktp' => 'N/A',
                    'dokumen_tambahan' => 'N/A',
                    'kontak_darurat_serumah' => '081234567'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'kontak_darurat_tidak_serumah' => '081234568'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'status_aktif' => true,
                    'tanggal_terakhir_aktif' => null,
                ]
            );
        }
    }
}
