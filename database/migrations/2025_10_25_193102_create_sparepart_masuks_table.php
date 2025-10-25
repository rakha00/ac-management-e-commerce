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
        Schema::create('sparepart_masuks', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->date('tanggal_masuk'); // transaction date
            $table->string('nomor_sparepart_masuk'); // auto-generated per date, unique & sequential

            // Distributor info (sourced from distributor_spareparts)
            $table->foreignId('distributor_sparepart_id')
                ->nullable()
                ->constrained('distributor_spareparts')
                ->nullOnDelete();
            $table->string('distributor_nama')->nullable(); // denormalized distributor name

            // Aggregates
            $table->integer('total_qty')->default(0);

            // Notes
            $table->text('keterangan')->nullable();

            // Ensure number is unique per date
            $table->unique(['tanggal_masuk', 'nomor_sparepart_masuk'], 'unique_sparepart_masuk_per_tanggal');

            $table->timestamps();
            $table->softDeletes(); // enable soft deletes for historical accuracy
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_masuks');
    }
};
