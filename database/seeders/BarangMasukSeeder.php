<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Principal;
use App\Models\UnitAC;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BarangMasukSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $principalA = Principal::firstOrCreate(
            ['nama' => 'PT. Sejuk Abadi'],
            [
                'sales' => 'Budi',
                'nomor_hp' => '081234567890',
                'keterangan' => 'Autocreated by BarangMasukSeeder',
            ]
        );

        $principalB = Principal::firstOrCreate(
            ['nama' => 'PT. Dingin Sejahtera'],
            [
                'sales' => 'Susi',
                'nomor_hp' => '081298765432',
                'keterangan' => 'Autocreated by BarangMasukSeeder',
            ]
        );

        $unitA = UnitAC::firstOrCreate(
            ['sku' => 'AC-SKU-001'],
            [
                'nama_unit' => 'CoolBrand A',
                'path_foto_produk' => null,
                'harga_dealer' => 3500000,
                'harga_ecommerce' => 3700000,
                'harga_retail' => 4000000,
                'stok_awal' => 15,
                'stok_akhir' => 15,
                'stok_masuk' => 3,
                'stok_keluar' => 0,
                'keterangan' => 'Seeder unit',
            ]
        );

        $unitB = UnitAC::firstOrCreate(
            ['sku' => 'AC-SKU-002'],
            [
                'nama_unit' => 'CoolBrand B',
                'path_foto_produk' => null,
                'harga_dealer' => 4500000,
                'harga_ecommerce' => 4700000,
                'harga_retail' => 5000000,
                'stok_awal' => 10,
                'stok_akhir' => 10,
                'stok_masuk' => 6,
                'stok_keluar' => 0,
                'keterangan' => 'Seeder unit',
            ]
        );

        $today = Carbon::today();
        $d = $today->format('dmY');
        $yesterday = $today->copy()->subDay();
        $dy = $yesterday->format('dmY');

        $payloads = [
            [
                'principal_id' => (int) $principalA->id,
                'tanggal' => $today,
                'nomor_barang_masuk' => "BM/{$d}-1",
                'barangMasukDetail' => [
                    [
                        'unit' => $unitA,
                        'jumlah' => 3,
                        'keterangan' => 'Barang Masuk A-1',
                    ],
                    [
                        'unit' => $unitB,
                        'jumlah' => 2,
                        'keterangan' => 'Barang Masuk B-1',
                    ],
                ],
            ],
            [
                'principal_id' => (int) $principalB->id,
                'tanggal' => $yesterday,
                'nomor_barang_masuk' => "BM/{$dy}-1",
                'barangMasukDetail' => [
                    [
                        'unit' => $unitB,
                        'jumlah' => 4,
                        'keterangan' => 'Barang Masuk B-2',
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
                    'principal_id' => $data['principal_id'],
                    'tanggal' => $data['tanggal'],
                ]
            );

            // Create detail entries for this BarangMasuk
            foreach ($data['barangMasukDetail'] as $detail) {
                $unit = $detail['unit'];

                BarangMasukDetail::firstOrCreate(
                    [
                        'barang_masuk_id' => (int) $bm->id,
                        'unit_ac_id' => (int) $unit->id,
                        'sku' => $unit->sku,
                        'nama_unit' => $unit->nama_unit,
                    ],
                    [
                        'jumlah_barang_masuk' => (int) $detail['jumlah'],
                        'keterangan' => $detail['keterangan'],
                    ]
                );
            }
        }
    }
}
