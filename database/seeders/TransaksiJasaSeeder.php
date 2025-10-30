<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\TransaksiJasa;
use App\Models\TransaksiJasaDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransaksiJasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there's at least one user for created_by/updated_by
        $user = User::first();
        if (! $user) {
            $user = User::create([
                'name' => 'Seeder User',
                'email' => 'seeder@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        // Ensure there's at least one Teknisi
        $teknisi = Karyawan::where('jabatan', 'teknisi')->first();
        if (! $teknisi) {
            Karyawan::create([
                'nama' => 'Teknisi Seeder 1',
                'alamat' => 'Alamat Teknisi 1',
                'telepon' => '081234567890',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1990-01-01',
                'jabatan' => 'teknisi',
                'gaji_pokok' => 5000000,
                'tanggal_masuk' => '2020-01-01',
                'status' => 'Aktif',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        // Ensure there's at least one Helper
        $helper = Karyawan::where('jabatan', 'helper')->first();
        if (! $helper) {
            Karyawan::create([
                'nama' => 'Helper Seeder 1',
                'alamat' => 'Alamat Helper 1',
                'telepon' => '081234567891',
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1992-02-02',
                'jabatan' => 'helper',
                'gaji_pokok' => 3000000,
                'tanggal_masuk' => '2021-01-01',
                'status' => 'Aktif',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        // Fetch all Teknisi and Helper after ensuring they exist
        $teknisiKaryawan = Karyawan::where('jabatan', 'teknisi')->get();
        $helperKaryawan = Karyawan::where('jabatan', 'helper')->get();

        for ($i = 0; $i < 3; $i++) {
            $tanggalTransaksi = Carbon::now()->subDays(rand(1, 365));
            $teknisi = $teknisiKaryawan->random();
            $helper = $helperKaryawan->random();

            $kodeJasa = TransaksiJasa::generateSequentialNumber($tanggalTransaksi->toDateString(), 'KJ');

            $transaksiJasa = TransaksiJasa::create([
                'tanggal_transaksi' => $tanggalTransaksi,
                'kode_jasa' => $kodeJasa,
                'nama_konsumen' => fake()->name(),
                'teknisi_karyawan_id' => $teknisi->id,
                'helper_karyawan_id' => $helper->id,
                'teknisi_nama' => $teknisi->nama,
                'helper_nama' => $helper->nama,
                'garansi_hari' => rand(30, 365),
                'keterangan' => fake()->sentence,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $qty = rand(1, 5);
                $hargaJasa = rand(50000, 500000);
                $pengeluaranJasa = rand(10000, 100000);

                TransaksiJasaDetail::create([
                    'transaksi_jasa_id' => $transaksiJasa->id,
                    'jenis_data' => fake()->word,
                    'qty' => $qty,
                    'harga_jasa' => $hargaJasa,
                    'keterangan_jasa' => fake()->sentence,
                    'pengeluaran_jasa' => $pengeluaranJasa,
                    'keterangan_pengeluaran' => fake()->sentence,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }
    }
}
