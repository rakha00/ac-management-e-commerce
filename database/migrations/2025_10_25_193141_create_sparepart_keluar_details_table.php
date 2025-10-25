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
        Schema::create('sparepart_keluar_details', function (Blueprint $table) {
            $table->id();

            // Parent transaction relation
            $table->foreignId('sparepart_keluar_id')
                ->constrained('sparepart_keluars')
                ->onDelete('cascade'); // delete details when parent is deleted

            // Sparepart relation and denormalized info
            $table->foreignId('sparepart_id')
                ->nullable()
                ->constrained('spareparts')
                ->restrictOnDelete(); // keep detail if sparepart is removed, restrict delete when referenced
            $table->string('kode_sparepart'); // denormalized code
            $table->string('nama_sparepart'); // denormalized name

            // Detail amounts for keluar
            $table->integer('jumlah_keluar');
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);

            // Notes
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes(); // enable soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_keluar_details');
    }
};
