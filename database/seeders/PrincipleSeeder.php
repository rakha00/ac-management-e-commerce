<?php

namespace Database\Seeders;

use App\Models\Principle;
use Illuminate\Database\Seeder;

class PrincipleSeeder extends Seeder
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
                'no_hp' => '081234567890',
                'remarks' => 'Top principle',
            ],
            [
                'nama' => 'PT. Dingin Sejahtera',
                'sales' => 'Susi',
                'no_hp' => '081298765432',
                'remarks' => null,
            ],
            [
                'nama' => 'CV. AC Maju',
                'sales' => 'Andi',
                'no_hp' => '081223344556',
                'remarks' => 'Regional',
            ],
        ];

        foreach ($data as $item) {
            Principle::firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'sales' => $item['sales'],
                    'no_hp' => $item['no_hp'],
                    'remarks' => $item['remarks'],
                ]
            );
        }
    }
}
