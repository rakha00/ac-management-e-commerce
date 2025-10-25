<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();

            // Core identity & attributes (Indonesian field names as requested)
            $table->string('kode_sparepart')->unique(); // unique sparepart code
            $table->string('nama_sparepart'); // sparepart name
            $table->decimal('harga_modal', 15, 2)->default(0); // base cost in IDR

            // Stock fields (stock_akhir will be computed at model level)
            $table->integer('stock_awal')->default(0);
            $table->integer('stock_masuk')->default(0);
            $table->integer('stock_keluar')->default(0);

            // Notes
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes(); // enable soft deletes per requirement pattern
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
