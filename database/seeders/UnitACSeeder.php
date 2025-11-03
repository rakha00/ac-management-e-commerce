<?php

namespace Database\Seeders;

use App\Models\UnitAC;
use Illuminate\Database\Seeder;

class UnitACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitAC::create([
            'sku' => 'AC001',
            'nama_unit' => 'AC Split 1 PK',
            'merk_id' => 1,
            'pk' => 1,
            'tipe_ac_id' => 1,
            'path_foto_produk' => ['unit_ac/ac_split_1pk.jpg'],
            'harga_dealer' => 3000000,
            'harga_ecommerce' => 3500000,
            'harga_retail' => 4000000,
            'stok_awal' => 10,
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'keterangan' => 'Unit AC Split 1 PK standar',
        ]);

        UnitAC::create([
            'sku' => 'AC002',
            'nama_unit' => 'AC Inverter 1.5 PK',
            'merk_id' => 2,
            'pk' => 2,
            'tipe_ac_id' => 2,
            'path_foto_produk' => ['unit_ac/ac_inverter_1_5pk.jpg'],
            'harga_dealer' => 4500000,
            'harga_ecommerce' => 5000000,
            'harga_retail' => 5500000,
            'stok_awal' => 8,
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'keterangan' => 'Unit AC Inverter hemat energi 1.5 PK',
        ]);

        UnitAC::create([
            'sku' => 'AC003',
            'nama_unit' => 'AC Cassette 2 PK',
            'merk_id' => 3,
            'pk' => 2,
            'tipe_ac_id' => 3,
            'path_foto_produk' => ['unit_ac/ac_cassette_2pk.jpg'],
            'harga_dealer' => 7000000,
            'harga_ecommerce' => 7500000,
            'harga_retail' => 8000000,
            'stok_awal' => 5,
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'keterangan' => 'Unit AC Cassette untuk ruangan besar 2 PK',
        ]);
    }
}
