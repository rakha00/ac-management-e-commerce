<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('harga_unit_ac_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_ac_id')->constrained('unit_ac')->onDelete('cascade');
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->integer('harga_dealer');
            $table->integer('harga_ecommerce');
            $table->integer('harga_retail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_unit_ac_history');
    }
};
