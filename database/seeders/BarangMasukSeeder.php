<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Principle;
use App\Models\UnitAC;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BarangMasukSeeder extends Seeder
{
    /**
     * Jalankan seeder BarangMasuk beserta details.
     */
    public function run(): void
    {
        // Pastikan minimal dua principle tersedia.
        $principleA = Principle::firstOrCreate(
            ['nama' => 'PT. Sejuk Abadi'],
            [
                'sales' => 'Budi',
                'no_hp' => '081234567890',
                'remarks' => 'Autocreated by BarangMasukSeeder',
            ]
        );

        $principleB = Principle::firstOrCreate(
            ['nama' => 'PT. Dingin Sejahtera'],
            [
                'sales' => 'Susi',
                'no_hp' => '081298765432',
                'remarks' => 'Autocreated by BarangMasukSeeder',
            ]
        );

        // Pastikan minimal dua Unit AC tersedia (untuk relasi detail).
        $unitA = UnitAC::firstOrCreate(
            ['sku' => 'AC-SKU-001'],
            [
                'nama_merk' => 'CoolBrand A',
                'foto_produk' => null,
                'harga_dealer' => 3500000,
                'harga_ecommerce' => 3700000,
                'harga_retail' => 4000000,
                'stock_awal' => 0,
                'stock_masuk' => 0,
                'stock_keluar' => 0,
                'remarks' => 'Seeder unit',
            ]
        );

        $unitB = UnitAC::firstOrCreate(
            ['sku' => 'AC-SKU-002'],
            [
                'nama_merk' => 'CoolBrand B',
                'foto_produk' => null,
                'harga_dealer' => 4500000,
                'harga_ecommerce' => 4700000,
                'harga_retail' => 5000000,
                'stock_awal' => 0,
                'stock_masuk' => 0,
                'stock_keluar' => 0,
                'remarks' => 'Seeder unit',
            ]
        );

        $today = Carbon::today();
        $d = $today->format('dmY');
        $yesterday = $today->copy()->subDay();
        $dy = $yesterday->format('dmY');

        // Contoh data Barang Masuk (2 record)
        $payloads = [
            [
                'principle_id' => (int) $principleA->id,
                'tanggal' => $today,
                'nomor_barang_masuk' => "BM/{$d}-1",
                'details' => [
                    [
                        'unit' => $unitA,
                        'jumlah' => 3,
                        'remarks' => 'Barang Masuk A-1',
                    ],
                    [
                        'unit' => $unitB,
                        'jumlah' => 2,
                        'remarks' => 'Barang Masuk B-1',
                    ],
                ],
            ],
            [
                'principle_id' => (int) $principleB->id,
                'tanggal' => $yesterday,
                'nomor_barang_masuk' => "BM/{$dy}-1",
                'details' => [
                    [
                        'unit' => $unitB,
                        'jumlah' => 4,
                        'remarks' => 'Barang Masuk B-2',
                    ],
                ],
            ],
        ];

        foreach ($payloads as $data) {
            // Create header BarangMasuk
            $bm = BarangMasuk::firstOrCreate(
                [
                    'nomor_barang_masuk' => $data['nomor_barang_masuk'],
                ],
                [
                    'principle_id' => $data['principle_id'],
                    'tanggal' => $data['tanggal'],
                ]
            );

            // Create detail entries for this BarangMasuk
            foreach ($data['details'] as $detail) {
                $unit = $detail['unit'];

                BarangMasukDetail::firstOrCreate(
                    [
                        'barang_masuk_id' => (int) $bm->id,
                        'unit_ac_id' => (int) $unit->id,
                        'sku' => $unit->sku,
                        'nama_unit' => $unit->nama_merk,
                    ],
                    [
                        'jumlah_barang_masuk' => (int) $detail['jumlah'],
                        'remarks' => $detail['remarks'],
                    ]
                );
            }
        }
    }
}
