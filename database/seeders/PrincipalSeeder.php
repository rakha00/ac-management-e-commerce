<?php

namespace Database\Seeders;

use App\Models\Principal;
use Illuminate\Database\Seeder;

class PrincipalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'PT. Sejuk Abadi',
                'sales' => 'Budi',
                'nomor_hp' => '081234567890',
                'keterangan' => 'Top principal',
            ],
            [
                'nama' => 'PT. Dingin Sejahtera',
                'sales' => 'Susi',
                'nomor_hp' => '081298765432',
                'keterangan' => null,
            ],
            [
                'nama' => 'CV. AC Maju',
                'sales' => 'Andi',
                'nomor_hp' => '081223344556',
                'keterangan' => 'Regional',
            ],
        ];

        foreach ($data as $item) {
            Principal::firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'sales' => $item['sales'],
                    'nomor_hp' => $item['nomor_hp'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
