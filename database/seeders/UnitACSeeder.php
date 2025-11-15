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
        $tipeACTypes = [
            1 => ['tipe' => 'Split AC', 'pk_variations' => [0.5, 1, 1.5, 2, 2.5]],
            2 => ['tipe' => 'Single AC', 'pk_variations' => [0.5, 1, 1.5, 2]],
            3 => ['tipe' => 'Cassette AC', 'pk_variations' => [2, 2.5, 3, 4, 5]],
            4 => ['tipe' => 'Portable AC', 'pk_variations' => [0.5, 1, 1.5, 2]],
            5 => ['tipe' => 'Window AC', 'pk_variations' => [0.5, 1, 1.5, 2, 2.5]],
            6 => ['tipe' => 'Central AC', 'pk_variations' => [3, 4, 5, 7.5, 10]],
            7 => ['tipe' => 'Floor Standing AC', 'pk_variations' => [2, 2.5, 3, 4, 5]],
            8 => ['tipe' => 'Inverter AC', 'pk_variations' => [1, 1.5, 2, 2.5, 3]],
        ];

        $skuCounter = 1;

        foreach ($tipeACTypes as $tipeId => $tipeData) {
            $tipeName = $tipeData['tipe'];
            $pkVariations = $tipeData['pk_variations'];

            // Create 10 units for each AC type
            for ($i = 1; $i <= 10; $i++) {
                // Rotate through different PK variations
                $pk = $pkVariations[($i - 1) % count($pkVariations)];

                // Rotate through different brands (1-10)
                $merkId = $i;
                if ($merkId > 10) {
                    $merkId = (($i - 1) % 10) + 1;
                }

                // Generate dynamic SKU
                $sku = 'AC'.str_pad($skuCounter, 3, '0', STR_PAD_LEFT);

                // Calculate prices based on PK and type
                $basePrice = $this->getBasePriceByType($tipeId, $pk);
                $hargaDealer = $basePrice;
                $hargaEcommerce = $basePrice + ($basePrice * 0.15); // 15% markup
                $hargaRetail = $basePrice + ($basePrice * 0.25); // 25% markup

                // Generate unit name
                $unitName = "AC {$tipeName} {$pk} PK";

                // Random stock variation
                $stok = rand(5, 15);

                UnitAC::create([
                    'sku' => $sku,
                    'nama_unit' => $unitName,
                    'merk_id' => $merkId,
                    'pk' => $pk,
                    'tipe_ac_id' => $tipeId,
                    'path_foto_produk' => null,
                    'harga_dealer' => $hargaDealer,
                    'harga_ecommerce' => $hargaEcommerce,
                    'harga_retail' => $hargaRetail,
                    'stok_awal' => $stok,
                    'stok_masuk' => 0,
                    'stok_keluar' => 0,
                    'keterangan' => "Unit AC {$tipeName} {$pk} PK merk {$this->getMerkName($merkId)}",
                ]);

                $skuCounter++;
            }
        }
    }

    private function getBasePriceByType($tipeId, $pk)
    {
        // Base prices per PK by AC type
        $basePrices = [
            1 => [ // Split AC
                0.5 => 2000000,
                1 => 2800000,
                1.5 => 3500000,
                2 => 4200000,
                2.5 => 5000000,
            ],
            2 => [ // Single AC
                0.5 => 1800000,
                1 => 2500000,
                1.5 => 3200000,
                2 => 3800000,
            ],
            3 => [ // Cassette AC
                2 => 6000000,
                2.5 => 7000000,
                3 => 8000000,
                4 => 10000000,
                5 => 12000000,
            ],
            4 => [ // Portable AC
                0.5 => 1500000,
                1 => 2200000,
                1.5 => 2800000,
                2 => 3500000,
            ],
            5 => [ // Window AC
                0.5 => 1600000,
                1 => 2300000,
                1.5 => 3000000,
                2 => 3700000,
                2.5 => 4400000,
            ],
            6 => [ // Central AC
                3 => 15000000,
                4 => 18000000,
                5 => 22000000,
                7.5 => 30000000,
                10 => 40000000,
            ],
            7 => [ // Floor Standing AC
                2 => 5500000,
                2.5 => 6500000,
                3 => 7500000,
                4 => 9500000,
                5 => 11500000,
            ],
            8 => [ // Inverter AC
                1 => 3200000,
                1.5 => 4000000,
                2 => 4800000,
                2.5 => 5600000,
                3 => 6400000,
            ],
        ];

        // Get base price for the type, default to PK 1 if not found
        if (isset($basePrices[$tipeId][$pk])) {
            return $basePrices[$tipeId][$pk];
        }

        // Default fallback prices
        $fallbackPrices = [0.5 => 2000000, 1 => 2800000, 1.5 => 3500000, 2 => 4200000, 3 => 6000000, 4 => 8000000, 5 => 10000000, 7.5 => 15000000, 10 => 20000000];

        foreach ([$pk, 1, 2] as $testPk) {
            if (isset($fallbackPrices[$testPk])) {
                return $fallbackPrices[$testPk];
            }
        }

        return 3000000; // Ultimate fallback
    }

    private function getMerkName($merkId)
    {
        $merks = [
            1 => 'Daikin',
            2 => 'Panasonic',
            3 => 'Sharp',
            4 => 'LG',
            5 => 'Samsung',
            6 => 'Toshiba',
            7 => 'Midea',
            8 => 'Haier',
            9 => 'Gree',
            10 => 'Carrier',
        ];

        return $merks[$merkId] ?? 'Unknown';
    }
}
