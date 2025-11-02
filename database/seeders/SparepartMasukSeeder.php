<?php

namespace Database\Seeders;

use App\Models\DistributorSparepart;
use App\Models\Sparepart;
use App\Models\SparepartMasuk;
use App\Models\SparepartMasukDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SparepartMasukSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$distributors = DistributorSparepart::all();

		$spareparts = Sparepart::all();

		for ($i = 0; $i < 3; $i++) {
			$distributor = $distributors->random();
			$date = Carbon::now()->subDays(rand(1, 30));

			$sparepartMasuk = SparepartMasuk::create([
				'tanggal_masuk' => $date,
				'distributor_sparepart_id' => $distributor->id,
				'keterangan' => 'Keterangan untuk sparepart masuk ' . ($i + 1),
				'created_by' => 1, // Assuming user with ID 1 exists
				'updated_by' => 1, // Assuming user with ID 1 exists
			]);

			// Create 1 to 3 dummy SparepartMasukDetail entries for each SparepartMasuk
			for ($j = 0; $j < rand(1, 3); $j++) {
				$sparepart = $spareparts->random();
				SparepartMasukDetail::create([
					'sparepart_masuk_id' => $sparepartMasuk->id,
					'sparepart_id' => $sparepart->id,
					'jumlah_masuk' => rand(1, 20),
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
		}
	}
}